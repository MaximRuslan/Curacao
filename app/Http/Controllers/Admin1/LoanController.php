<?php

namespace App\Http\Controllers\Admin1;

use App\Http\Controllers\Controller;
use App\Library\Helper;
use App\Models\Branch;
use App\Models\Country;
use App\Models\Documents;
use App\Models\ExistingLoanType;
use App\Models\LoanAmounts;
use App\Models\LoanApplication;
use App\Models\LoanCalculationHistory;
use App\Models\LoanDeclineReason;
use App\Models\LoanNotes;
use App\Models\LoanOnHoldReason;
use App\Models\LoanProof;
use App\Models\LoanReason;
use App\Models\LoanStatus;
use App\Models\LoanStatusHistory;
use App\Models\LoanTransaction;
use App\Models\LoanType;
use App\Models\ReferralHistory;
use App\Models\TransactionType;
use App\Models\User;
use App\Models\UserInfo;
use App\Models\UserWork;
use App\Models\Wallet;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class LoanController extends Controller
{

    public function __construct()
    {
        $this->middleware('role:super admin|admin|processor|auditor|debt collector|loan approval|credit and processing|client')
            ->only([
                'index',
                'indexDatatable',
                'show',
                'loansMasterData',
                'transactionDatatable',
                'loanLastCalculationHistory',
                'loanStatusHistory',
            ]);
        $this->middleware('role:super admin|admin|auditor|processor|client')
            ->only([
                'saveTransaction',
            ]);
        $this->middleware('role:super admin|admin|processor|debt collector|loan approval|credit and processing|client')
            ->only([
                'changeStatus',
            ]);
        $this->middleware('role:super admin|admin|processor|debt collector|loan approval|credit and processing')
            ->only([
                'loanHistory',
            ]);
        $this->middleware('role:super admin|admin')
            ->only([
                'calculationHistoryUpdate',
            ]);

        $this->middleware('role:super admin|admin|processor|debt collector|loan approval|credit and processing|client')
            ->only([
                'loanTypeInfo',
                'loanUserBranches',
                'loansApplicationExcel',
            ]);

        $this->middleware('role:super admin|admin|processor|client')
            ->only([
                'store',
                'edit',
                'destroy',
            ]);
    }

    public function index($assign = false, $my_client = false)
    {
        $data = [];
        $country = session()->has('country') ? session()->get('country') : '';

        $data['reasons'] = LoanReason::getAllReasons()->pluck('title', 'id');
        $data['otherLoanTypes'] = ExistingLoanType::orderBy('title', 'ASC')->get();


        $client = User::select(DB::raw('concat(firstname," ",lastname) as name'), 'id');
        if (auth()->user()->hasRole('super admin') && $country != '') {
            $client->where('users.country', '=', $country);
        } else {
            if (!auth()->user()->hasRole('super admin')) {
                $client->where('users.country', '=', auth()->user()->country);
            }
        }
        $data['clients'] = $client->where('role_id', '=', '3')
            ->orderBy('firstname', 'asc')
            ->pluck('name', 'id');

        $data['employees'] = User::getEmployees($country);

        $data['clients_has_active_loans'] = LoanApplication::inActiveClients();

        $data['declineReasons'] = LoanDeclineReason::pluck('title', 'id');
        $data['onHoldReasons'] = LoanOnHoldReason::pluck('title', 'id');
        $data['transaction_types'] = TransactionType::orderBy('id', 'ASC')
            ->where('id', '=', 1)
            ->pluck('title', 'id');
        $data['payment_types'] = config('site.payment_types');
        $data['cash_back_payment_types'] = config('site.cash_back_payment_types');

        if (request('status')) {
            if (request('status') == 'deleted') {
                $data['status_name'] = 'Deleted';
            } else {
                $loanstatus = LoanStatus::find(request('status'));
                if ($loanstatus != null) {
                    $data['status_name'] = $loanstatus->title;
                }
            }
        }
        if ($assign) {
            $data['assign'] = $assign;
            $data['status_name'] = 'To Assign';
        }
        $data['statuses'] = [];
        if ($my_client) {
            $data['my_client'] = $my_client;
            $data['status_name'] = 'My Clients';
            $data['statuses'] = LoanStatus::select('*');
            if (!auth()->user()->hasRole('super admin|admin')) {
                $only_statuses = LoanStatus::userWiseStatus(auth()->user());
                $data['statuses']->whereIn('id', $only_statuses);
            }
            $data['statuses'] = $data['statuses']->pluck('title', 'id');
            $data['employees'] = User::getEmployees($country, true);
        }
        return view('admin1.pages.loans.index', $data);
    }

    public function assign()
    {
        return $this->index(true);
    }

    public function myClients()
    {
        return $this->index(false, true);
    }

    public function store()
    {
        $this->validate(request(), LoanApplication::adminValidationRules(), LoanApplication::validationMessage());

        $format = config('site.date_format.php');

        //validation messages set
        //        $this->validate(request(), LoanApplication::validationRules($max, $salaryMax, request('id')), LoanApplication::validationMessage());

        $inputs = request()->only([
            'client_id',
            'loan_type',
            'amount',
            'loan_reason',
        ]);

        $inputs['amount'] = floatval($inputs['amount']);

        $user = User::find(request('client_id'));
        if (request('id') == '' || request('id') == '0') {
            if (LoanApplication::hasActiveLoan($user)) {
                $data = [];
                $data['client_id'] = [
                    'The client already has active loan.',
                ];
                return response($data, 422);
            }
        }

        $salary = array_sum(request('income_amount'));
        $other_loan_deduction = 0;
        if (request('other_amount')) {
            $other_loan_deduction = array_sum(request('other_amount'));
        }

        $loan_type = LoanType::find($inputs['loan_type']);

        $max_amount = round((($salary - $other_loan_deduction) * $loan_type->loan_component) / 100, 2);

        if ($max_amount < 0) {
            $data = [];
            $data['other_loan_amount'] = [
                'The other loan deduction may not be greater than ' . $salary . '.',
            ];
            return response($data, 422);
        }

        if ($max_amount < $inputs['amount'] && $user->web_registered == null) {
            $data = [];
            $data['amount'] = [
                'Amount should be less than suggested loan amount.'
                // Lang::get('validation.min.custom', ['attribute' => 'Amount','min'=>'Suggested Loan amount'])
            ];
            return response($data, 422);
        }

        $date = \DateTime::createFromFormat($format, request('date_of_payment')[0]);
        $deadline_date = $date->format('Y-m-d');
        DB::transaction(function () use ($format, $inputs, $user, $loan_type, $max_amount, $salary, $other_loan_deduction, $deadline_date) {
            $inputs += [
                'loan_component'             => $loan_type->loan_component,
                'origination_type'           => $loan_type->origination_type,
                'origination_amount'         => $loan_type->origination_amount,
                'renewal_amount'             => $loan_type->renewal_amount,
                'renewal_type'               => $loan_type->renewal_type,
                'debt_type'                  => $loan_type->debt_type,
                'debt_amount'                => $loan_type->debt_amount,
                'debt_collection_type'       => $loan_type->debt_collection_type,
                'debt_collection_percentage' => $loan_type->debt_collection_percentage,
                'debt_collection_tax_type'   => $loan_type->debt_collection_tax_type,
                'debt_collection_tax_value'  => $loan_type->debt_collection_tax_value,
                'debt_tax_type'              => $loan_type->debt_tax_type,
                'debt_tax_amount'            => $loan_type->debt_tax_amount,
                'period'                     => $loan_type->number_of_days,
                'interest'                   => $loan_type->interest,
                'cap_period'                 => $loan_type->cap_period,
            ];


            $country = Country::find($user->country);

            //tax calculation
            $inputs += [
                'tax_percentage' => $country->tax_percentage,
                'tax_name'       => $country->tax,
            ];

            $origination_fee = 0;
            if ($loan_type->origination_type == 1) {
                $origination_fee = request('amount') * $loan_type->origination_amount / 100;
            } else {
                if ($loan_type->origination_type == 2) {
                    $origination_fee = $loan_type->origination_amount;
                }
            }

            $origination_fee = round($origination_fee, 2);

            $tax = round($origination_fee * $country->tax_percentage / 100, 2);

            $interest_amount = round(request('amount') * $loan_type->interest / 100, 2);

            $inputs += [
                'origination_fee'      => $origination_fee,
                'tax'                  => $tax,
                'interest_amount'      => $interest_amount,
                'max_amount'           => $max_amount,
                'salary'               => $salary,
                'other_loan_deduction' => $other_loan_deduction,
                'deadline_date'        => $deadline_date,
            ];


            if (request('id') == '' || request('id') == '0') {
                $inputs['loan_status'] = 1;
                unset($inputs['signature']);
                $loanApplication = LoanApplication::create($inputs);
                $loanApplication->update([
                    'signature' => Helper::base64ToJpeg(request('signature'), 'signature_' . $loanApplication->id),
                ]);
                LoanApplication::addLoanStatusHistory($loanApplication->id, '1');
            } else {
                $loanApplication = LoanApplication::find(request('id'));
                if ($loanApplication['loan_status'] == "2" || $loanApplication['loan_status'] == "3") {
                    $inputs['loan_status'] = 1;
                    LoanApplication::addLoanStatusHistory($loanApplication->id, '1');
                }
                $loanApplication->update($inputs);
            }

            $inputs = [];
            $inputs['signature_pdf'] = $loanApplication->generateSignaturePdf();
            $loanApplication->update($inputs);


            if (request('id') == '0') {
                if (request()->hasFile('income_proof_image')) {
                    $proof = request()->file('income_proof_image');
                    foreach ($proof as $key => $image) {
                        $name = str_slug(pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME), '_');
                        $ext = pathinfo($image->getClientOriginalName(), PATHINFO_EXTENSION);
                        $imageName = time() . '_' . base64_encode(rand(1, 100)) . '_' . $name . '.' . $ext;
                        $image->move(public_path('storage/loan_applications/' . $loanApplication->id), $imageName);

                        $loanProof = LoanProof::create([
                            'file_name' => $imageName,
                        ]);
                        $date = null;
                        if (request()->date_of_payment[$key] != '') {
                            $dateObj = \DateTime::createFromFormat($format, request()->date_of_payment[$key]);
                            $date = $dateObj->format('Y-m-d');
                        }

                        LoanAmounts::create([
                            'loan_id'       => $loanApplication->id,
                            'attachment_id' => $loanProof->id,
                            'type'          => '1',
                            'amount'        => request()->income_amount[$key],
                            'amount_type'   => request()->income_type[$key],
                            'date'          => $date,
                        ]);
                    }
                }

                if (request('other_amount')) {
                    foreach (request()->other_amount as $key => $value) {
                        LoanAmounts::create([
                            'loan_id'     => $loanApplication->id,
                            'type'        => '2',
                            'amount'      => request()->other_amount[$key],
                            'amount_type' => request()->expense_type[$key],
                            'date'        => null,
                        ]);

                    }
                }
            } else {
                $newIds = request('income_id');
                $oldIds = LoanAmounts::where(['loan_id' => request('id')])->where(['type' => '1'])->get()->pluck(['id'])->toArray();
                $diff = array_diff($oldIds, $newIds);
                $toDeleteItems = LoanAmounts::whereIN('id', $diff)->get();
                foreach ($toDeleteItems as $item) {
                    $loanProof = LoanProof::find($item->attachment_id);
                    if ($loanProof) {
                        $path = public_path('storage/loan_applications/' . $loanApplication->id . '/' . $loanProof->file_name);
                        Storage::delete($path);
                        $loanProof->forceDelete();
                    }
                    $item->delete();
                }

                foreach (request()->income_amount as $key => $value) {
                    $loanAmount = LoanAmounts::find(request()->income_id[$key]);
                    $loanProof = '';
                    $date = null;
                    if (isset(request()->date_of_payment[$key]) && request()->date_of_payment[$key] != '') {
                        $dateObj = \DateTime::createFromFormat($format, request()->date_of_payment[$key]);
                        $date = $dateObj->format('Y-m-d');
                    }
                    if (isset(request()->income_proof_image[$key])) {
                        if ($loanAmount) {
                            $loanProof = LoanProof::find($loanAmount->attachment_id);
                        }

                        $image = request()->income_proof_image[$key];
                        $name = str_slug(pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME), '_');
                        $ext = pathinfo($image->getClientOriginalName(), PATHINFO_EXTENSION);
                        $imageName = time() . '_' . base64_encode(rand(1, 100)) . '_' . $name . '.' . $ext;
                        $path = public_path('storage/loan_applications/' . $loanApplication->id);
                        $image->move($path, $imageName);

                        if ($loanProof) {
                            $path = public_path('storage/loan_applications/' . $loanApplication->id . '/' . $loanProof->file_name);
                            Storage::delete($path);
                            $loanProof->update([
                                'file_name' => $imageName,
                            ]);
                        } else {
                            $loanProof = LoanProof::create([
                                'file_name' => $imageName,
                            ]);
                        }

                        if ($loanAmount) {
                            $loanAmount->update([
                                'type'          => '1',
                                'amount'        => request()->income_amount[$key],
                                'attachment_id' => $loanProof->id,
                                'amount_type'   => request()->income_type[$key],
                                'date'          => $date,
                            ]);
                        } else {
                            LoanAmounts::create([
                                'loan_id'       => $loanApplication->id,
                                'attachment_id' => $loanProof->id,
                                'type'          => '1',
                                'amount'        => request()->income_amount[$key],
                                'amount_type'   => request()->income_type[$key],
                                'date'          => $date,
                            ]);
                        }
                    } else {
                        if ($loanAmount) {
                            $loanAmount->update([
                                'type'        => '1',
                                'amount'      => request()->income_amount[$key],
                                'amount_type' => request()->income_type[$key],
                                'date'        => $date,
                            ]);
                        } else {
                            // validation
                        }
                    }
                }

                $newIds = request()->expense_id;
                $oldIds = LoanAmounts::where(['loan_id' => request()->id])
                    ->where(['type' => '2'])
                    ->pluck('id')
                    ->toArray();
                if (request('expense_id') && count($newIds) > 0) {
                    $diff = array_diff($oldIds, $newIds);
                } else {
                    $diff = $oldIds;
                }
                $toDeleteItems = LoanAmounts::whereIN('id', $diff)->get();
                foreach ($toDeleteItems as $item) {
                    $loanProof = LoanProof::find($item->attachment_id);
                    if ($loanProof) {
                        $path = public_path('storage/loan_applications/' . $loanApplication->id . '/' . $loanProof->file_name);
                        Storage::delete($path);
                        $loanProof->forceDelete();
                    }
                    $item->delete();
                }
                if (request('other_amount')) {
                    foreach (request()->other_amount as $key => $value) {
                        $loanAmountExp = LoanAmounts::find(request()->expense_id[$key]);
                        $loanProof = '';
                        if (isset(request()->expense_proof_image[$key])) {
                            if ($loanAmountExp) {
                                $loanProof = LoanProof::find($loanAmountExp->attachment_id);
                            }

                            $image = request()->expense_proof_image[$key];
                            $name = str_slug(pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME), '_');
                            $ext = pathinfo($image->getClientOriginalName(), PATHINFO_EXTENSION);
                            $imageName = time() . '_' . base64_encode(rand(1, 100)) . '_' . $name . '.' . $ext;
                            $path = public_path('storage/loan_applications/' . $loanApplication->id);
                            $image->move($path . '/', $imageName);

                            if ($loanProof) {
                                $path = public_path('storage/loan_applications/' . $loanApplication->id . '/' . $loanProof->file_name);
                                Storage::delete($path);
                                $loanProof->update([
                                    'file_name' => $imageName,
                                ]);
                            } else {
                                $loanProof = LoanProof::create([
                                    'file_name' => $imageName,
                                ]);
                            }

                            if ($loanAmountExp) {
                                $loanAmountExp->update([
                                    'type'          => '2',
                                    'attachment_id' => $loanProof->id,
                                    'amount'        => request()->other_amount[$key],
                                    'amount_type'   => request()->expense_type[$key],
                                    'date'          => null,
                                ]);
                            } else {
                                LoanAmounts::create([
                                    'loan_id'       => $loanApplication->id,
                                    'type'          => '2',
                                    'attachment_id' => $loanProof->id,
                                    'amount'        => request()->other_amount[$key],
                                    'amount_type'   => request()->expense_type[$key],
                                    'date'          => null,
                                ]);
                            }
                        } else {
                            if ($loanAmountExp) {
                                $loanAmountExp->update([
                                    'type'        => '2',
                                    'amount'      => request()->other_amount[$key],
                                    'amount_type' => request()->expense_type[$key],
                                    'date'        => null,
                                ]);
                            } else {
                                $loanAmount = LoanAmounts::create([
                                    'loan_id'     => $loanApplication->id,
                                    'type'        => '2',
                                    'amount'      => request()->other_amount[$key],
                                    'amount_type' => request()->expense_type[$key],
                                    'date'        => null,
                                ]);
                            }
                        }
                    }
                }
            }
        });

        $data = [];

        $data['status'] = true;

        return $data;
    }

    public function edit(LoanApplication $loan)
    {
        $format = config('site.date_format.php');
        $data = [];
        $data['loan'] = [
            'id'          => ['type' => 'hidden', 'value' => $loan->id],
            'client_id'   => ['type' => 'select2', 'value' => $loan->client_id],
            'loan_reason' => ['type' => 'select2', 'value' => $loan->loan_reason],
            'loan_type'   => ['type' => 'select2', 'value' => $loan->loan_type],
            'salary_date' => ['type' => 'text', 'value' => date($format, strtotime($loan->deadline_date))],
            'amount'      => ['type' => 'select2', 'value' => $loan->amount],
        ];

        $data['amounts'] = LoanAmounts::where('loan_id', '=', $loan->id)->get();

        $files = LoanProof::whereIn('id', $data['amounts']->pluck('attachment_id'));

        foreach ($data['amounts'] as $amount) {
            if ($amount->date != null) {
                $amount->date = date($format, strtotime($amount->date));
            }
            if ($amount->attachment_id != null) {
                $file = $files->where('id', '=', $amount->attachment_id)->first();
                if ($file != null) {
                    $amount->file_name = $file->file_name;
                }
            }
        }
        $data["folder"] = asset('storage/loan_applications/' . $loan->id) . '/';

        return $data;
    }

    public function show($id)
    {
        $selection = [
            'loan_applications.*',
            'users.firstname',
            'users.lastname',
            'loan_reasons.title as loan_reason_title',
            'loan_decline_reasons.title as loan_decline_reasons_title',
            'loan_on_hold_reasons.title as loan_on_hold_reasons_title',
            'loan_types.title as loan_types_title',
            'loan_status.title as loan_status_title',
        ];

        $loan = LoanApplication::select($selection)
            ->leftJoin('users', 'users.id', '=', 'loan_applications.client_id')
            ->leftJoin('loan_reasons', 'loan_reasons.id', '=', 'loan_applications.loan_reason')
            ->leftJoin('loan_types', 'loan_types.id', '=', 'loan_applications.loan_type')
            ->leftJoin('loan_status', 'loan_status.id', '=', 'loan_applications.loan_status')
            ->leftJoin('loan_decline_reasons', 'loan_decline_reasons.id', '=', 'loan_applications.loan_decline_reason')
            ->leftJoin('loan_on_hold_reasons', 'loan_on_hold_reasons.id', '=', 'loan_applications.loan_decline_reason')
            ->where('loan_applications.id', '=', $id)
            ->first();
        if ($loan == null) {
            abort(404);
        }

        $amounts = LoanAmounts::select('loan_amounts.*', 'loan_proofs.file_name')
            ->where('loan_amounts.loan_id', '=', $id)
            ->leftJoin('loan_proofs', 'loan_proofs.id', '=', 'loan_amounts.attachment_id')
            ->get();


        if ($loan->start_date != null) {
            $loan->start_date = Helper::date_time_to_current_timezone($loan->start_date);
        }
        if ($loan->end_date != null) {
            $loan->end_date = Helper::date_time_to_current_timezone($loan->end_date);
        }
        if ($loan->deadline_date && $loan->deadline_date != null) {
            $loan->deadline_date = Helper::datebaseToFrontDate($loan->deadline_date);
        }

        $loan->uploaded = 0;
        $loan->otherLoan = 0;
        foreach ($amounts as $key => $value) {
            if ($value->type == '1') {
                if ($value->amount_type == '1') {
                    $value->title = 'Gross salary';
                } else {
                    if ($value->amount_type == '2') {
                        $value->title = 'Other Income';
                    }
                }
                $loan->uploaded++;
            } else {
                if ($value->type == '2') {
                    $existing_loan_type = ExistingLoanType::find($value->amount_type);
                    $value->title = '';
                    if ($existing_loan_type != null) {
                        $value->title = $existing_loan_type->title;
                    }
                    $loan->otherLoan++;
                }
            }
        }
        $loan->amounts = $amounts;
        $data['loan'] = $loan->toArray();
        $user = User::find($loan->client_id);
        $country = Country::find($user->country);
        $data['loan']['user_documents'] = [];

        $array = [];
        $array[0]['key'] = "Address Proof";
        $array[0]['value'] = $user->address_proof != '' ? asset('uploads/' . $user->address_proof) : null;

        $array[2]['key'] = "Scan ID";
        $array[2]['value'] = $user->scan_id != '' ? asset('uploads/' . $user->scan_id) : null;
        if ($user->exp_date < date('Y-m-d')) {
            $array[2]['expires'] = Helper::datebaseToFrontDate($user->exp_date);
        } else {
            $array[2]['expires'] = null;
        }

        $array[3]['key'] = "Paysilp1";
        $array[3]['value'] = $user->payslip1 != '' ? asset('uploads/' . $user->payslip1) : null;

        $array[4]['key'] = "Paysilp2";
        $array[4]['value'] = $user->payslip2 != '' ? asset('uploads/' . $user->payslip2) : null;

        $other_documents = Documents::where('main_id', '=', $user->id)->where('type', '=', 1)->get();

        foreach ($other_documents as $key => $value) {
            $array[] = [
                'key'   => $value->name,
                'value' => $value->document != '' ? asset('uploads/' . $value->document) : null,
            ];
        }

        foreach ($array as $key => $value) {
            $data['loan']['user_documents'][] = $value;
        }
        $data['numbers'] = UserInfo::where('user_id', '=', $user->id)
            ->whereIn('type', [1, 2])
            ->pluck('value');

        $data['user_work'] = UserWork::where('user_id', '=', $user->id)
            ->where('employed_since', '<=', date('Y-m-d'))
            ->where('contract_expires', '>=', date('Y-m-d'))
            ->first();

        $data['last_history'] = LoanCalculationHistory::where('loan_id', '=', $id)
            ->orderBy('id', 'desc')
            ->first();

        foreach ($data['numbers'] as $key => $value) {
            $data['numbers'][$key] = $country->country_code . $value;
        }

        $data['user'] = $user;

        if (request('rst') && request('rst') == "json") {
            return $data;
        }
        return view('admin1.pages.loans.view', $data);
    }

    public function destroy($id)
    {
        $loanApplication = LoanApplication::find($id);
        $loanApplication->update([
            'deleted_by' => auth()->user()->id,
        ]);
        $loanApplication->delete();
        return response()->json([
            "status"  => "success",
            "message" => "Deleted successfully.",
        ]);
    }

    public function indexDatatable()
    {
        $selection = [
            'loan_applications.*',
            'users.id_number as user_id_number',
            'users.firstname',
            'users.lastname',
            'coll.firstname as collector_first_name',
            'coll.lastname as collector_last_name',
            'users.web_registered',
            'users.country as country_id',
            'deleted_users.firstname as deleted_user_firstname',
            'deleted_users.lastname as deleted_user_lastname',
            'loan_reasons.title as reason_title',
            'loan_types.title as loan_type_title',
            'loan_types.number_of_days',
            'loan_status.title as loan_status_title',
            DB::raw('TIMESTAMPADD(WEEK,loan_types.number_of_days,loan_applications.start_date) as original_due_date'),
            DB::raw('(case when 
                    (select ch.total from loan_calculation_histories as ch where ch.loan_id=loan_applications.id and ch.deleted_at is null order by ch.id desc limit 1) is null then loan_applications.amount 
                    else 
                    (select ch.total from loan_calculation_histories as ch where ch.loan_id=loan_applications.id and ch.deleted_at is null order by ch.id desc limit 1) end) as outstanding_balance'),
            DB::raw('(select notes.follow_up from loan_notes as notes where notes.loan_id=loan_applications.id and notes.deleted_at is null order by notes.follow_up desc limit 1) as follow_up_date'),
            DB::raw('(select loan_transactions.created_at from loan_transactions where loan_transactions.loan_id = loan_applications.id order by loan_transactions.payment_date desc limit 1) as last_payment_date'),
        ];

        $applications = LoanApplication::select($selection)
            ->join('users', 'users.id', '=', 'loan_applications.client_id')
            ->leftJoin('users as deleted_users', 'deleted_users.id', '=', 'loan_applications.deleted_by')
            ->leftJoin('users as coll', 'coll.id', '=', 'loan_applications.employee_id')
            ->leftJoin('loan_reasons', 'loan_reasons.id', '=', 'loan_applications.loan_reason')
            ->leftJoin('loan_types', 'loan_types.id', '=', 'loan_applications.loan_type')
            ->leftJoin('loan_status', 'loan_status.id', '=', 'loan_applications.loan_status');

        $country = session()->has('country') ? session()->get('country') : '';
        if (auth()->user()->hasRole('super admin') && $country != '') {
            $applications->where('users.country', '=', $country);
        } else {
            if (!auth()->user()->hasRole('super admin') && !auth()->user()->hasRole('client')) {
                if (!auth()->user()->hasRole('admin')) {
                    if (request('history') != '1') {
                        $applications->whereIn('loan_applications.loan_status', LoanStatus::userWiseStatus(auth()->user()));
                    }
                }
                $applications->where('users.country', '=', auth()->user()->country);
            } else {
                if (auth()->user()->hasRole('client')) {
                    $applications->where('users.id', '=', auth()->user()->id);
                }
            }
        }

        if (request('user_id')) {
            $applications->where('users.id', '=', request('user_id'));
        }

        if (request('not_id')) {
            $applications->where('loan_applications.id', '!=', request('not_id'));
        }

        if (request('status')) {
            if (request('status') == 'deleted') {
                $applications->withTrashed()->whereNotNull('loan_applications.deleted_at');
            } else {
                $applications->where('loan_applications.loan_status', '=', request('status'));
            }
        }

        if (request('assign') && request('assign') == 1) {
            $applications->whereNull('loan_applications.employee_id');
            $applications->whereIn('loan_applications.loan_status', [4, 5, 6]);
        }

        if (request('my_client') && request('my_client') == 1) {
            if (auth()->user()->hasAnyRole(['super admin', 'admin'])) {
                $applications->whereNotNull('loan_applications.employee_id');
                if (auth()->user()->hasAnyRole(['admin'])) {
                    $admins = User::where('role_id', '=', 1)->pluck('id');
                    $applications->whereNotIn('loan_applications.employee_id', $admins);
                }
                if (request('employee_id')) {
                    $applications->where('loan_applications.employee_id', '=', request('employee_id'));
                }
            } else {
                $applications->where('loan_applications.employee_id', '=', auth()->user()->id);
            }
            if (request('my_client_status')) {
                $applications->where('loan_applications.loan_status', '=', request('my_client_status'));
            }
        }

        if (request('order')) {
            foreach (request('order') as $key => $element) {
                if (isset($element['column']) && $element['column'] == '6') {
                    $applications->orderBy('follow_up_date', $element['dir']);
                }
                if (isset($element['column']) && $element['column'] == '7') {
                    $applications->orderBy('original_due_date', $element['dir']);
                }
                if (isset($element['column']) && $element['column'] == '14') {
                    $applications->orderBy('outstanding_balance', $element['dir']);
                }
            }
        }
        $employee = User::getEmployees($country);
        return DataTables::of($applications)
            ->addColumn('checkbox', function ($row) {
                return '<input type="checkbox" class="js--assign" data-id="' . $row->id . '" value="1">';
            })
            ->editColumn('user_first_name', function ($row) {
                return '<a target="_blank" href="' . url()->route('admin1.users.show', ['user_id' => $row['client_id']]) . '">
                        ' . $row->firstname . ' ' . $row->lastname . '
                        </a>';
            })
            ->addColumn('collector_first_name', function ($row) use ($employee) {
                if ($row['employee_id'] != null) {
                    $options = '';
                    foreach ($employee as $key => $value) {
                        $selected = '';
                        if ($key == $row['employee_id']) {
                            $selected = '<i class="fa fa-check"></i>';
                        }
                        $options .= '<a class="dropdown-item js--employee-change" href="javascript;" data-id="' . $row['id'] . '" data-user-id="' . $key . '">' . $selected . $value . '</a>';
                    }
                    return '<div class="btn-group">
                            <a href="javascript;" class="dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false">
                                ' . $row['collector_first_name'] . ' ' . $row['collector_last_name'] . '
                            </a>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="btnGroupDrop1" style="height: 200px;overflow-y: auto;overflow-x: hidden;">
                                ' . $options . '
                            </div>
                        </div>';
                }
            })
            ->addColumn('deleted_user_name', function ($row) {
                $str = '';
                if ($row->deleted_by == '0') {
                    $str .= 'System <br>';
                } else {
                    if ($row->deleted_by == null) {
                        $str .= '- <br>';
                    } else {
                        $str .= '<b>' . $row->deleted_user_firstname . ' ' . $row->deleted_user_lastname . '</b><br>';
                    }
                }
                $str .= Helper::date_time_to_current_timezone($row->deleted_at);
                return $str;
            })
            ->addColumn('follow_up_date', function ($row) {
                if ($row->follow_up_date != null) {
                    return Helper::datebaseToFrontDate($row->follow_up_date);
                }
            })
            ->addColumn('last_payment_date', function ($row) {
                if ($row->last_payment_date != null) {
                    return Helper::datebaseToFrontDate($row->last_payment_date);
                }
            })
            ->addColumn('original_due_date', function ($row) {
                if ($row->start_date != null) {
                    $date = date('Y-m-d H:i:s', strtotime($row->start_date . " +" . $row->number_of_days . ' weeks'));
                    $date = Helper::date_to_current_timezone($date);
                    return Helper::datebaseToFrontDate($date);
                }
            })
            ->editColumn('created_at', function ($row) {
                return Helper::date_to_current_timezone($row->created_at);
            })
            ->editColumn('start_date', function ($row) {
                if ($row->start_date != null) {
                    return Helper::date_time_to_current_timezone($row->start_date);
                }
            })
            ->editColumn('end_date', function ($row) {
                if ($row->end_date != null) {
                    return Helper::date_time_to_current_timezone($row->end_date);
                }

            })
            ->addColumn('amount', function ($row) {
                return Helper::decimalShowing($row->amount, $row->country_id);
            })
            ->addColumn('outstanding_balance', function ($row) {
                return Helper::decimalShowing($row->outstanding_balance, $row->country_id);
            })
            ->addColumn('action', function ($row) {
                if ($row->deleted_at == null) {
                    $index = 0;
                    $html = "";
                    $html .= '<div class="loan-actions">';
                    if (collect([1, 2, 3, 12])->contains($row->loan_status)) {
                        if (auth()->user()->hasRole('super admin|admin|loan approval')) {
                            if (collect([1, 2, 12])->contains($row->loan_status)) {
                                $notes = '';
                                if ($row->max_amount < $row->amount) {
                                    $notes = 'data-notes="1" data-amount="' . $row->amount . '" data-suggest="' . $row->max_amount . '"';
                                }
                                if ($row->web_registered != null) {
                                    $html .= '<a href="javascript:;" data-toggle="tooltip" data-id="' . $row->id . '" data-status="3"
                                    class="btn btn-sm waves-effect btn-success changeStatus" title="Approve" ' . $notes . '>
                                        <i class="fa fa-check"></i>
                                </a>';
                                } else {
                                    if ($row->loan_status != 12) {
                                        $html .= '<a href="javascript:;" data-toggle="tooltip" data-id="' . $row->id . '" data-status="12"
                                        class="btn btn-sm waves-effect btn-success changeStatus" title="Pre Approve">
                                            <i class="fa fa-check"></i>
                                    </a>';
                                    }
                                }
                                $index++;
                                if ($index == 4) {
                                    $html .= '<br>';
                                    $index = 0;
                                }
                            }
                        }
                        if (auth()->user()->hasRole('super admin|admin|loan approval|credit and processing')) {
                            $html .= '<a href="javascript:;" data-toggle="tooltip" data-id="' . $row->id . '" data-status="11"
                                    class="btn btn-sm waves-effect btn-danger changeStatus" title="Reject">
                                        <i class="fa fa-ban"></i>
                                </a>';
                            $index++;
                            if ($index == 4) {
                                $html .= '<br>';
                                $index = 0;
                            }
                        }
                    }

                    if (($row->loan_status == 1 || $row->loan_status == 12) && auth()->user()->hasRole('super admin|admin|loan approval')) {
                        $html .= '<a href="javascript:;" data-toggle=tooltip data-id="' . $row->id . '" data-status="2" 
                               class="btn btn-sm waves-effect btn-success changeStatus" title="On Hold">
                                    <i class="fa fa-pause"></i>
                            </a>';
                        $index++;
                        if ($index == 4) {
                            $html .= '<br>';
                            $index = 0;
                        }
                    }

                    if (collect([1, 2, 11, 12])->contains($row->loan_status)
                        && auth()->user()->hasRole('super admin')) {
                        $html .= '<a href="javascript:;" data-id="' . $row->id . '" data-toggle="tooltip" 
                                class="btn btn-sm waves-effect btn-info deleteLoan" title="Delete">
                                    <i class="fa fa-trash"></i>
                            </a>';
                        $index++;
                        if ($index == 4) {
                            $html .= '<br>';
                            $index = 0;
                        }
                        $html .= '<a href="javascript:;" class="btn btn-sm waves-effect btn-info editLoanApplication" 
                                title="Edit" data-id="' . $row->id . '" data-toggle="tooltip">
                                    <i class="fa fa-pencil"></i>
                            </a>';
                        $index++;
                        if ($index == 4) {
                            $html .= '<br>';
                            $index = 0;
                        }
                    }

                    if ($row->loan_status == 3 && auth()->user()->hasRole('super admin|credit and processing')) {
                        $html .= '<a href="javascript:;" data-id="' . $row->id . '" data-toggle="tooltip" title="Current" data-status="4"
                                data-modal-id="currentLoanModal"  class="btn btn-sm waves-effect btn-success changeStatus">
                                    <i class="fa fa-line-chart"></i>
                            </a>';
                        $index++;
                        if ($index == 4) {
                            $html .= '<br>';
                            $index = 0;
                        }
                    }

                    $html .= '<a target="_blank" href="' . url()->route('admin1.loans.show', ['id' => $row->id]) . '" 
                            class="btn btn-sm waves-effect btn-info" title="View" data-toggle="tooltip">
                                <i class="fa fa-eye"></i>
                        </a>';
                    $index++;
                    if ($index == 4) {
                        $html .= '<br>';
                        $index = 0;
                    }

                    if (collect([4, 5, 6, 7, 8, 9, 10])->contains($row->loan_status) && auth()->user()->hasRole('super admin|admin|auditor|processor')) {
                        $html .= '<a href="javascript:;" data-status="' . $row->loan_status . '" data-id="' . $row->id . '" data-toggle="tooltip" class="btn btn-sm waves-effect btn-info showTransaction" title="Loan Transactions">
                                    <i class="fa fa-dollar"></i>
                            </a>';
                        $index++;
                        if ($index == 4) {
                            $html .= '<br>';
                            $index = 0;
                        }
                    }

                    if ($row->loan_status > 3 && $row->loan_status != 11 && $row->loan_status != 12) {
                        $html .= '<a class="btn btn-sm waves-effect btn-info" data-id="' . $row->id . '" target="_blank" 
                                title="Transaction history" data-toggle="tooltip" 
                                href="' . url()->route('admin1.loans.calculation-history', $row->id) . '">
                                     <i class="fa fa-history"></i>
                            </a>';
                        $index++;
                        if ($index == 4) {
                            $html .= '<br>';
                            $index = 0;
                        }
                    }
                    if (auth()->user()->hasRole('super admin|admin|processor|debt collector|loan approval|credit and processing')) {
                        $html .= '<button class="btn btn-sm waves-effect btn-info showLoanStatusHistory" data-toggle="tooltip" 
                            data-id="' . $row->id . '" title="Loan Status history">
                                <i class="fa fa-list-alt"></i>
                          </button>';
                        $index++;
                        if ($index == 4) {
                            $html .= '<br>';
                            $index = 0;
                        }
                    }

                    if ($row->signature_pdf != null && auth()->user()->hasRole('super admin|admin|processor|debt collector|loan approval|credit and processing')) {
                        $html .= '<a href="' . asset('pdf/' . $row->signature_pdf) . '" target="_blank" download="" 
                                class="btn btn-sm waves-effect btn-info" title="Loan Agreement PDF" data-toggle="tooltip">
                                    <i class="fa fa-paperclip"></i>
                              </a>';
                        $index++;
                        if ($index == 4) {
                            $html .= '<br>';
                            $index = 0;
                        }
                    }
                    $html .= '</div>';
                    return $html;
                }
            })
            ->setRowClass(function ($row) {
                if ($row->web_registered == null) {
                    return 'web_registered';
                }
            })
            ->setRowData([
                'data-toggle' => function ($row) {
                    if ($row->deleted_at != null) {
                        return 'tooltip';
                    }
                },
                'title'       => function ($row) {
                    if ($row->deleted_at != null) {
                        return Helper::date_time_to_current_timezone($row->deleted_at);
                    }
                },
            ])
            ->rawColumns(['deleted_user_name', 'action', 'user_first_name', 'checkbox', 'collector_first_name'])
            ->make(true);
    }

    public function loansMasterData(User $user)
    {
        $data = [];
        $data['user'] = $user;
        $data['country'] = Country::find($user->country);
        $data['loan_types'] = LoanType::activeLoanTypesViaUserId($user)->pluck('title', 'id');
        return $data;
    }

    public function loanTypeInfo(LoanType $type)
    {
        $data = [];
        $data['type'] = $type;
        return $data;
    }

    public function changeStatus(LoanApplication $loan, $status)
    {
        $data = [];
        if (Cache::has('loan_' . $loan->id)) {
            $data['status'] = false;
            $data['message'] = "This loan is already in process.";
            return $data;
        }
        Cache::rememberForever('loan_' . $loan->id, function () {
            return date('Y-m-d H:i:s');
        });
        if ($status == 3 && request('description_required') == 1 && (request('description') == '' || request('description') == null)) {
            Cache::pull('loan_' . $loan->id);
            $data['status'] = false;
            return $data;
        }
        $data = DB::transaction(function () use ($loan, $status) {
            if ($loan->loan_status != $status) {
                if (collect([2, 3, 4, 11, 12])->contains($status)) {

                    $check_status = true;
                    if ($loan->loan_status == 1 && !in_array($status, [2, 3, 11, 12])) {
                        $check_status = false;
                    } else {
                        if ($loan->loan_status == 2 && !in_array($status, [3, 11, 12])) {
                            $check_status = false;
                        } else {
                            if ($loan->loan_status == 3 && !in_array($status, [4, 11])) {
                                $check_status = false;
                            } else {
                                if ($loan->loan_status == 12 && !in_array($status, [2, 3, 11])) {
                                    $check_status = false;
                                } else {
                                    if (in_array($loan->loan_status, [4, 5, 6, 7, 8, 9, 10, 11])) {
                                        $check_status = false;
                                    }
                                }
                            }
                        }
                    }

                    if ($check_status) {
                        $email_status = true;
                        if ($loan->loan_status == 3 && $status == 11) {
                            $email_status = false;
                        }
                        if ($status == 4) {
                            $tax_on_interest = round(($loan->interest_amount * $loan->tax_percentage) / 100, 2);
                            $credit_amount = $loan->amount - $loan->origination_fee - $loan->tax - $loan->interest_amount - $tax_on_interest;
                            Wallet::create([
                                'user_id'                  => $loan->client_id,
                                'amount'                   => $credit_amount,
                                'notes'                    => 'Approved Loan',
                                'transaction_payment_date' => date('Y-m-d'),
                            ]);
                            $loan->update([
                                'start_date' => date('Y-m-d H:i:s'),
                            ]);
                            ReferralHistory::storeHistory($loan, 1);
                        }
                        $inputs = [];
                        if ($status == 11 || $status == 2 || $status == 3) {
                            if ($status == 11) {
                                if (request('decline_reason')) {
                                    $inputs['loan_decline_reason'] = request('decline_reason');
                                }
                            }
                            if ($status == 2) {
                                if (request('hold_reason')) {
                                    $inputs['loan_decline_reason'] = request('hold_reason');
                                }
                            }
                            if (request('description')) {
                                $inputs['decline_description'] = request('description');
                            }
                        }
                        if ($status == 11 && $loan->loan_status == 3) {
                            $status = 1;
                        }
                        $inputs['loan_status'] = $status;
                        $inputs['employee_id'] = request('employee_id');
                        LoanApplication::addLoanStatusHistory($loan->id, $status, request('description'));
                        $loan->update($inputs);

                        if ($status == 4) {
                            Artisan::call('loan:calculate', ['id' => $loan->id]);
                        }
                        if ($status != 3) {
                            if (auth()->user()->hasRole('admin|super admin|processor|loan approval|credit and processing')) {
                                if ($email_status) {
                                    $user = User::find($loan->client_id);
                                    try {
                                        Mail::to($user->email)->send(new \App\Mail\LoanStatus($user, $loan));
                                    } catch (\Exception $e) {
                                        Log::info($e);
                                    }
                                    $loan->notificationSend();
                                }

                            }
                        }
                        $data['status'] = true;
                    } else {
                        $data['status'] = true;
                        $data['message'] = "Something went wrong. Please try again later.";
                    }
                } else {
                    $data['status'] = false;
                    $data['message'] = "Something went wrong. Please try again later.";
                }
            } else {
                $loan_status = LoanStatus::find($loan->loan_status);
                $status_name = '';
                if ($loan_status != null) {
                    $status_name = $loan_status->title;
                }
                $data['status'] = false;
                $data['message'] = "This loan is already in the \"" . $status_name . "\" state.";
            }
            return $data;
        });
        Cache::pull('loan_' . $loan->id);
        return $data;
    }

    public function notesListing(LoanApplication $loan)
    {
        $data = [];
        $data['notes'] = LoanNotes::select('loan_notes.*', DB::raw('concat(users.firstname," ",users.lastname) as user_name'))
            ->leftJoin('users', 'users.id', '=', 'loan_notes.user_id')
            ->where('loan_notes.loan_id', '=', $loan->id)
            ->orderBy('loan_notes.priority', 'desc')
            ->orderBy('loan_notes.id', 'desc')
            ->limit(2)
            ->get();
        foreach ($data['notes'] as $key => $value) {
            $value->date = Helper::datebaseToFrontDate($value->date);
            $value->follow_up = Helper::datebaseToFrontDate($value->follow_up);
            if (strlen($value->details) > 100) {
                $value->details = '<span class="teaser">' . substr($value->details, 0, 100) . '</span>' .
                    '<span class="complete" style="display:none;">' . $value->details . '</span>  <a href="#nogo" class="more" data-type="more"> more...</button >';
            }
        }
        return $data;
    }

    public function notesDatatable(LoanApplication $loan)
    {
        $notes = LoanNotes::select('loan_notes.*', DB::raw('concat(users.firstname," ",users.lastname) as user_name'))
            ->leftJoin('users', 'users.id', '=', 'loan_notes.user_id')
            ->where('loan_notes.loan_id', '=', $loan->id)
            ->orderBy('loan_notes.id', 'desc');
        return DataTables::of($notes)
            ->addColumn('date', function ($row) {
                return Helper::datebaseToFrontDate($row->date);
            })
            ->addColumn('follow_up', function ($row) {
                return Helper::datebaseToFrontDate($row->follow_up);
            })
            ->addColumn('action', function ($row) {
                if (auth()->user()->hasRole('super admin|admin')) {
                    $str = '<button class="btn btn-primary editNote" title="Edit" data-id="' . $row->id . '"><i class="fa fa-pencil"></i></button>' .
                        '<button class="btn btn-danger deleteNote" title="Delete" data-id="' . $row->id . '"><i class="fa fa-trash"></i></button>';
                    return $str;
                }
            })
            ->make(true);
    }

    public function notesEdit(LoanApplication $loan, LoanNotes $note)
    {
        $filteredArr = [
            'id'        => ["type" => "hidden", 'value' => $note->id],
            'date'      => ["type" => "text", 'value' => Helper::databaseToFrontEditDate($note->date)],
            'follow_up' => ["type" => "text", 'value' => Helper::databaseToFrontEditDate($note->follow_up)],
            'details'   => ["type" => "textarea", 'value' => $note->details],
            'priority'  => ["type" => "checkbox", 'value' => $note->priority],
        ];

        return response()->json([
            "status" => "success",
            "inputs" => $filteredArr,
        ]);
    }

    public function notesStore(LoanApplication $loan)
    {
        $this->validate(request(), [
            'date'      => 'required|date_format:d/m/Y',
            'follow_up' => 'required|date_format:d/m/Y',
        ]);
        $format = config('site.date_format.php');
        $inputs = request()->only('date', 'follow_up', 'details');
        if (request('priority') == 1) {
            $inputs['priority'] = 1;
        } else {
            $inputs['priority'] = null;
        }
        $date = \DateTime::createFromFormat($format, request('date'));
        $inputs['date'] = $date->format('Y-m-d');
        $date = \DateTime::createFromFormat($format, request('follow_up'));
        $inputs['follow_up'] = $date->format('Y-m-d');

        $loan_note = LoanNotes::find(request('id'));
        if ($loan_note != null) {
            $loan_note->update($inputs);
        } else {
            $inputs['loan_id'] = $loan->id;
            $inputs['user_id'] = auth()->user()->id;
            LoanNotes::create($inputs);
        }

        $data = [];
        $data['status'] = true;

        return $data;
    }

    public function loanNotesDestroy(LoanApplication $loan, LoanNotes $note)
    {
        $data = [];
        $note->update([
            'deleted_by' => auth()->user()->id,
        ]);
        $data['status'] = $note->delete();
        return $data;
    }

    public function transactionDatatable(LoanApplication $loan)
    {
        $transactions = LoanTransaction::select('loan_transactions.*', 'users.firstname', 'users.lastname', 'transaction_types.title as transaction_type_name',
            'merchants.name as merchant', 'created_by.first_name', 'created_by.last_name')
            ->leftJoin('users', 'users.id', '=', 'loan_transactions.created_by')
            ->leftJoin('merchants', 'merchants.id', '=', 'loan_transactions.merchant_id')
            ->leftJoin('merchants as created_by', 'created_by.id', '=', 'loan_transactions.created_by')
            ->leftJoin('transaction_types', 'transaction_types.id', '=', 'loan_transactions.transaction_type')
            ->where('loan_transactions.loan_id', '=', $loan->id)
            ->where(function ($query) {
                $query->where('loan_transactions.amount', '>', 0)
                    ->orwhere('loan_transactions.cash_back_amount', '>', 0);
            });
        return DataTables::of($transactions)
            ->addColumn('payment_type', function ($row) {
                if ($row->payment_type != null && ($row->transaction_type == 1 || $row->transaction_type == 2)) {
                    return config('site.payment_types') [$row->payment_type];
                }
            })
            ->addColumn('amount', function ($row) {
                return Helper::decimalShowing($row->amount, Helper::getCountryId());
            })
            ->addColumn('cash_back_amount', function ($row) {
                return Helper::decimalShowing($row->cash_back_amount, Helper::getCountryId());
            })
            ->editColumn('created_user_name', function ($row) {
                if ($row->merchant_id != null) {
                    return $row->first_name . ' ' . $row->last_name . ' (' . $row->merchant . ')';
                } else {
                    return $row->firstname . ' ' . $row->lastname;
                }
            })
            ->editColumn('payment_date', function ($row) {
                return Helper::datebaseToFrontDate($row->payment_date);
            })
            ->editColumn('created_at', function ($row) {
                return Helper::date_time_to_current_timezone($row->updated_at);
            })
            ->make();
    }

    public function loanUserBranches(LoanApplication $loan)
    {
        $data = [];
        $user = User::find($loan->client_id);
        $data['branches'] = [];
        if ($user != null) {
            $data['branches'] = Branch::where('country_id', '=', $user->country)->pluck('title', 'id');
        }
        return $data;
    }

    public function saveTransaction(LoanApplication $loan)
    {
        if (in_array($loan->loan_status, [4, 5, 6])) {
            $data = [];

            $last_history = LoanCalculationHistory::where('loan_id', '=', $loan->id)
                ->orderBy('id', 'desc')
                ->first();

            $rules = [
                'payment_date' => 'required|date_format:d/m/Y',
            ];

            if (auth()->user()->hasRole('super admin|admin')) {
                $rules += [
                    'branch_id' => 'required|numeric|exists:branches,id',
                ];
            }
            $rules += [
                'next_payment_date' => 'nullable|date_format:d/m/Y',
                //                'payment_amount.*'  => 'required|numeric',
                //                'cashback_amount.*' => 'required|numeric',
                'transaction_type'  => 'required|numeric',
            ];

            $this->validate(request(), $rules);

            $format = config('site.date_format.php');
            $payment_date = null;
            if (request('payment_date') != '') {
                $dateObj = \DateTime::createFromFormat($format, request('payment_date'));
                $payment_date = $dateObj->format('Y-m-d');
            }

            if (request('write_off') && request('write_off') == 'true') {
                if ($payment_date < $last_history->date) {
                    return response()->json([
                        'payment_date' => [
                            'Payment date should be after ' . Helper::databaseToFrontEditDate($last_history->date) . '.',
                        ],
                    ], 422);
                }
            }


            $next_payment_date = null;
            if (request('next_payment_date') != '') {
                $dateObj = \DateTime::createFromFormat($format, request('next_payment_date'));
                $next_payment_date = $dateObj->format('Y-m-d');
            }

            $total_amount = 0;


            $payment_amount = array_sum(request('payment_amount')) - array_sum(request('cashback_amount'));

            if (Helper::decimalRound2($last_history->total) >= Helper::decimalRound2($payment_amount)) {
                $transaction_ids = [];
                foreach (request('payment_amount') as $key => $value) {
                    $cashback_amount = 0;
                    if (isset(request('cashback_amount')[$key]) && request('cashback_amount')[$key] != '') {
                        $cashback_amount = request('cashback_amount')[$key];
                    }
                    if ($value == '') {
                        $value = 0;
                    }
                    $transaction_type = request('transaction_type');
                    if (request('write_off') == 'true') {
                        $transaction_type = 2;
                    }
                    $total_amount = $total_amount + $value;

                    $inputs = [
                        'loan_id'           => $loan->id,
                        'client_id'         => $loan->client_id,
                        'amount'            => $value,
                        'transaction_type'  => $transaction_type,
                        'payment_type'      => $key,
                        'cash_back_amount'  => $cashback_amount,
                        'notes'             => request('notes'),
                        'created_by'        => auth()->user()->id,
                        'payment_date'      => $payment_date,
                        'next_payment_date' => $next_payment_date,
                    ];

                    if (auth()->user()->hasRole('super admin|admin')) {
                        $inputs['branch_id'] = request('branch_id');
                    } else {
                        $inputs['branch_id'] = session('branch_id');
                    }

                    $transaction = LoanTransaction::create($inputs);
                    $transaction_ids[] = $transaction->id;
                }


                $loan_histories = LoanCalculationHistory::where('loan_id', '=', $loan->id)
                    ->where('date', '>', $payment_date)
                    ->orderBy('id', 'asc')
                    ->get();

                $loan_histories_id = $loan_histories->pluck('id');

                LoanCalculationHistory::whereIn('id', $loan_histories_id)->update([
                    'deleted_by' => auth()->user()->id,
                ]);

                LoanCalculationHistory::whereIn('id', $loan_histories_id)->delete();

                $entry = true;
                $main_entry = '';
                $history_transactions = LoanTransaction::whereIn('id', $transaction_ids)->get();
                $collector = User::find($loan->employee_id);
                $commission = 0;
                $commission_percent = null;
                if ($collector != null && $collector->commission != null) {
                    $all_amount = $history_transactions->sum('amount') - $history_transactions->sum('cash_back_amount');
                    $commission = $all_amount * $collector->commission / 100;
                    $commission_percent = $collector->commission;
                }
                $history = collect([
                    'date'               => $payment_date,
                    'loan_id'            => $loan->id,
                    'employee_id'        => $loan->employee_id,
                    'commission_percent' => $commission_percent,
                    'commission'         => $commission,
                ]);
                if (count($loan_histories) > 0) {
                    foreach ($loan_histories as $key => $value) {
                        $loan_transactions = LoanTransaction::where('used', '=', $value->id)->get();
                        if ($value->date <= $payment_date || !$entry) {
                            if ($value->payment_amount != null) {
                                LoanCalculationHistory::calculationHistoryChange($value, 'payment', $loan_transactions->pluck('id'));
                            } else {
                                LoanCalculationHistory::calculationHistoryChange($value, 'week', []);
                            }
                        } else {
                            $main_entry = LoanCalculationHistory::calculationHistoryChange($history, 'payment', $history_transactions->pluck('id'), $payment_date,
                                request('write_off'));
                            if ($value->payment_amount != null) {
                                LoanCalculationHistory::calculationHistoryChange($value, 'payment', $loan_transactions->pluck('id'));
                            } else {
                                LoanCalculationHistory::calculationHistoryChange($value, 'week', []);
                            }
                            $entry = false;
                        }
                    }
                } else {
                    $main_entry = LoanCalculationHistory::calculationHistoryChange($history, 'payment', $history_transactions->pluck('id'), $payment_date, request('write_off'));
                }
                $main_entry = LoanCalculationHistory::find($main_entry->id);
                $receipt = LoanTransaction::createReceipt($main_entry, null, request('write_off'));
                if (request('receipt') == 1) {
                    $data['receipt_pdf'] = $receipt;
                }
                LoanTransaction::sendMailAndNotification($loan, $receipt);
                $data['status'] = true;
            } else {
                $data['status'] = false;
            }

            $data['loan_id'] = $loan->id;
            return $data;
        } else {
            abort(404);
        }
    }

    public function loanHistory(LoanApplication $loan)
    {
        $data = [];
        $data['loan'] = $loan;
        $data['user'] = User::find($loan->client_id);
        $data['country'] = Country::find($data['user']->country);
        $data['payment_types'] = config('site.payment_types');
        $data['cash_back_payment_types'] = config('site.cash_back_payment_types');
        return view('admin1.pages.loans.calculation', $data);
    }

    public function loanLastCalculationHistory(LoanApplication $loan)
    {
        if (request('newEntry')) {
            Artisan::call('loan:calculate', ['id' => $loan->id]);
        }
        $data = [];
        $user = User::find($loan->client_id);
        $country_id = $user->country;
        $selections = [
            'loan_calculation_histories.*',
            'users.firstname',
            'users.lastname',
        ];
        $history = LoanCalculationHistory::select($selections)
            ->leftJoin('users', 'users.id', '=', 'loan_calculation_histories.employee_id')
            ->where('loan_calculation_histories.loan_id', '=', $loan->id)
            ->orderBy('loan_calculation_histories.id', 'desc')
            ->get();
        $history = $history->map(function ($item, $key) use ($country_id) {
            if ($item->payment_amount == null) {
                $item->date = Helper::date_to_current_timezone($item->created_at);
            } else {
                $item->date = Helper::datebaseToFrontDate($item->date);
            }
            if ($item->payment_amount == null) {
                $item->payment_amount = '';
            }
            if ($item->week_iterations == 0 && ($item->total_e_tax == 0.00 || $item->total_e_tax == null)) {
                $item->total_e_tax = '';
            }
            if ($item->payment_amount != null) {
                $loan_transaction = LoanTransaction::select('loan_transactions.updated_at', 'loan_transactions.merchant_id', 'users.firstname', 'users.lastname',
                    'merchants.first_name', 'merchants.last_name',
                    'merchants.type as merchant_type', 'merchants.name as merchant', 'main.name as merchant_name')
                    ->leftJoin('users', function ($join) {
                        $join->on('users.id', '=', 'loan_transactions.created_by')->whereNull('users.deleted_at');
                    })
                    ->leftJoin('merchants', function ($join) {
                        $join->on('merchants.id', '=', 'loan_transactions.created_by')->whereNull('merchants.deleted_at');
                    })
                    ->leftJoin('merchants as main', function ($join) {
                        $join->on('main.id', '=', 'merchants.merchant_id')->whereNull('main.deleted_at');
                    })
                    ->where('loan_transactions.used', '=', $item->id)
                    ->first();

                if ($loan_transaction->merchant_id != null) {
                    if ($loan_transaction['merchant_type'] == 1) {
                        $item->user_info = $loan_transaction['first_name'] . ' ' . $loan_transaction['last_name'] . ' - ' . $loan_transaction['merchant'] . ' (' . Helper::date_time_to_current_timezone_without_seconds($loan_transaction['updated_at']) . ')';
                    } else {
                        $item->user_info = $loan_transaction['first_name'] . ' ' . $loan_transaction['last_name'] . ' - ' . $loan_transaction['merchant_name'] . ' (' . Helper::date_time_to_current_timezone_without_seconds($loan_transaction['updated_at']) . ')';
                    }
                } else {
                    $item->user_info = $loan_transaction['firstname'] . ' ' . $loan_transaction['lastname'] . ' (' . Helper::date_time_to_current_timezone_without_seconds($loan_transaction['updated_at']) . ')';
                }


            } else {
                $item->user_info = '';
            }
            if ($item->payment_amount != '') {
                $item->payment_amount = Helper::decimalShowing($item->payment_amount, $country_id);
            }
            if (request('not_country_related') && request('not_country_related') == 1) {
                $item->admin_fees_tax = $item->debt + $item->debt_tax;
            } else {
                $item->principal = Helper::decimalShowing($item->principal, $country_id);
                $item->origination = Helper::decimalShowing($item->origination, $country_id);
                $item->renewal = Helper::decimalShowing($item->renewal, $country_id);
                $item->interest = Helper::decimalShowing($item->interest, $country_id);
                $item->tax = Helper::decimalShowing($item->tax, $country_id);
                $item->admin_fees_tax = Helper::decimalShowing($item->debt + $item->debt_tax, $country_id);
                $item->debt = Helper::decimalShowing($item->debt, $country_id);
                $item->debt_tax = Helper::decimalShowing($item->debt_tax, $country_id);
                $item->debt_collection_value = Helper::decimalShowing($item->debt_collection_value, $country_id);
                $item->debt_collection_tax = Helper::decimalShowing($item->debt_collection_tax, $country_id);
                $item->total = Helper::decimalShowing($item->total, $country_id);
            }


            $collector = '';
            if ($item->employee_id != null) {
                $collector = $item->firstname . ' ' . $item->lastname;
            }
            $item->collector = $collector;
            return $item;
        });
        $data['history'] = $history;
        return $data;
    }

    public function ajaxHistoryEdit(LoanCalculationHistory $history)
    {
        $data = [];
        $data['history'] = $history;
        $data['before'] = LoanCalculationHistory::where('loan_id', '=', $history->loan_id)
            ->where('id', '<', $history->id)
            ->orderBy('id', 'desc')
            ->first();
        $data['first_history'] = LoanCalculationHistory::where('loan_id', '=', $history->loan_id)
            ->orderBy('id', 'asc')
            ->first();
        $data['last_history'] = LoanCalculationHistory::where('loan_id', '=', $history->loan_id)
            ->orderBy('id', 'desc')
            ->first();
        $data['payments'] = LoanTransaction::where('used', '=', $history->id)->get();
        return $data;
    }

    public function ajaxHistoryDelete(LoanCalculationHistory $history)
    {
        $data = [];

        $loan_histories = LoanCalculationHistory::where('loan_id', '=', $history->loan_id)
            ->where('id', '>=', $history->id)
            ->orderBy('id', 'asc')
            ->where('id', '!=', $history->id)
            ->get();

        $loan_histories_id = $loan_histories->pluck('id')->toArray();

        LoanCalculationHistory::whereIn('id', $loan_histories_id)->orwhere('id', '=', $history->id)->update([
            'deleted_by' => auth()->user()->id,
        ]);

        LoanCalculationHistory::whereIn('id', $loan_histories_id)->orwhere('id', '=', $history->id)->delete();

        LoanTransaction::where('used', '=', $history->id)->delete();

        foreach ($loan_histories as $key => $value) {
            $loan_transactions = LoanTransaction::where('used', '=', $value->id)->get();
            if ($value->payment_amount != null) {
                LoanCalculationHistory::calculationHistoryChange($value, 'payment', $loan_transactions->pluck('id'));
            } else {
                LoanCalculationHistory::calculationHistoryChange($value, 'week', []);
            }
        }

        if ($loan_histories->count() == 0) {
            LoanCalculationHistory::changeStatusAsPerLast($history->loan_id);
        }

        return $data;
    }

    public function calculationHistoryUpdate(LoanCalculationHistory $history)
    {
        $data = [];
        $rules = [
            'date' => 'required|date_format:d/m/Y',
        ];

        $loan = LoanApplication::find($history->loan_id);
        $rules += [
            'payment_amount'  => 'required',
            'cashback_amount' => 'required',
        ];

        $this->validate(request(), $rules);
        $loan_transactions = LoanTransaction::where('used', '=', $history->id)->get();
        $format = config('site.date_format.php');
        $date = request('date');
        $date = \DateTime::createFromFormat($format, $date);
        $date = $date->format('Y-m-d');

        $before = LoanCalculationHistory::where('loan_id', '=', $history->loan_id)
            ->where('id', '<', $history->id)
            ->orderBy('id', 'desc')
            ->first();

        if ((array_sum(request('payment_amount')) - array_sum(request('cashback_amount'))) > $before->total) {
            $data['status'] = false;
            return $data;
        }

        foreach (request('payment_amount') as $key => $value) {
            $transaction = $loan_transactions->where('payment_type', '=', $key)->first();
            $cashback_amount = 0;
            if (isset(request('cashback_amount')[$key]) && request('cashback_amount')[$key] != '') {
                $cashback_amount = request('cashback_amount')[$key];
            }
            if ($value == '') {
                $value = 0;
            }
            if ($transaction != null) {
                $transaction->update([
                    'amount'           => $value,
                    'cash_back_amount' => $cashback_amount,
                    'payment_date'     => $date,
                    'notes'            => request('notes'),
                ]);
            } else {
                LoanTransaction::create([
                    'loan_id'           => $history->loan_id,
                    'client_id'         => $loan->client_id,
                    'amount'            => $value,
                    'transaction_type'  => '1',
                    'payment_type'      => $key,
                    'cash_back_amount'  => $cashback_amount,
                    'notes'             => request('notes'),
                    'created_by'        => auth()->user()->id,
                    'payment_date'      => $date,
                    'next_payment_date' => $loan_transactions->pluck('next_payment_date')->first(),
                    'used'              => $history->id,
                ]);
            }
        }

        $loan_histories = LoanCalculationHistory::where('loan_id', '=', $history->loan_id);
        if ($date < $history->date) {
            $loan_histories->where('date', '>=', $date);
        } else {
            $loan_histories->where('id', '>=', $history->id);
        }
        $loan_histories = $loan_histories->orderBy('id', 'asc')
            ->where('id', '!=', $history->id)
            ->get();

        $loan_histories_id = $loan_histories->pluck('id')->toArray();

        LoanCalculationHistory::whereIn('id', $loan_histories_id)->orwhere('id', '=', $history->id)->update([
            'deleted_by' => auth()->user()->id,
        ]);

        LoanCalculationHistory::whereIn('id', $loan_histories_id)->orwhere('id', '=', $history->id)->delete();

        LoanStatusHistory::where('loan_id', '=', $history->loan_id)->where('created_at', '>=', date('Y-m-d H:i:s', strtotime($before->created_at)))->delete();

        $entry = true;
        foreach ($loan_histories as $key => $value) {
            $loan_transactions = LoanTransaction::where('used', '=', $value->id)->get();
            if ($value->date <= $date || !$entry) {
                if ($value->payment_amount != null) {
                    LoanCalculationHistory::calculationHistoryChange($value, 'payment', $loan_transactions->pluck('id'));
                } else {
                    LoanCalculationHistory::calculationHistoryChange($value, 'week', []);
                }
            } else {
                $history_transactions = LoanTransaction::where('used', '=', $history->id)->get();
                //                LoanCalculationHistory::calculationHistoryManual($history, $history_transactions->pluck('id'), request('transaction'), $date);
                if ($history->commission_percent != null) {
                    $all_amount = $history_transactions->sum('amount') - $history_transactions->sum('cash_back_amount');
                    $history->commission = $all_amount * $history->commission_percent / 100;
                }
                LoanCalculationHistory::calculationHistoryChange($history, 'payment', $history_transactions->pluck('id'), $date);
                if ($value->id != $history->id) {
                    if ($value->payment_amount != null) {
                        LoanCalculationHistory::calculationHistoryChange($value, 'payment', $loan_transactions->pluck('id'));
                    } else {
                        LoanCalculationHistory::calculationHistoryChange($value, 'week', []);
                    }
                }
                $entry = false;
            }
        }
        if ($entry) {
            $history_transactions = LoanTransaction::where('used', '=', $history->id)->get();
            LoanCalculationHistory::calculationHistoryChange($history, 'payment', $history_transactions->pluck('id'), $date);
            //            LoanCalculationHistory::calculationHistoryManual($history, $history_transactions->pluck('id'), request('transaction'), $date);
        }
        return $data;
    }

    public function calculationHistoryReceiptDownload(LoanCalculationHistory $history)
    {
        $data = [];
        if ($history->transaction_name == 'Write off') {
            $data['receipt_pdf'] = LoanTransaction::createReceipt($history, null, 'true');
        } else {
            $data['receipt_pdf'] = LoanTransaction::createReceipt($history);
        }
        return $data;
    }

    public function loanStatusHistory(LoanApplication $loan)
    {
        $data = [];
        $data['history'] = LoanStatusHistory::select('loan_status_histories.*', DB::raw('concat(users.firstname," ",users.lastname) as user_name'),
            'loan_status.title as loan_status')
            ->leftJoin('users', 'users.id', '=', 'loan_status_histories.user_id')
            ->leftJoin('loan_status', 'loan_status.id', '=', 'loan_status_histories.status_id')
            ->where('loan_status_histories.loan_id', '=', $loan->id)
            ->orderBy('loan_status_histories.id', 'desc')
            ->get();
        foreach ($data['history'] as $key => $value) {
            if ($value->note == null) {
                $value->note = '';
            }
            $value->date = Helper::date_time_to_current_timezone($value->created_at);
        }
        return $data;
    }

    public function loansApplicationExcel()
    {
        $selection = [
            'loan_applications.*',
            'users.id_number as user_id_number',
            'users.firstname',
            'users.lastname',
            'users.country',
            'users.web_registered',
            'loan_reasons.title as reason_title',
            'loan_types.title as loan_type_title',
            'loan_status.title as loan_status_title',
            DB::raw('(select ch.total from loan_calculation_histories as ch where ch.loan_id=loan_applications.id order by ch.id desc limit 1) as outstanding_balance'),
        ];

        $applications = LoanApplication::select($selection)
            ->leftJoin('users', 'users.id', '=', 'loan_applications.client_id')
            ->leftJoin('loan_reasons', 'loan_reasons.id', '=', 'loan_applications.loan_reason')
            ->leftJoin('loan_types', 'loan_types.id', '=', 'loan_applications.loan_type')
            ->leftJoin('loan_status', 'loan_status.id', '=', 'loan_applications.loan_status');

        $country = session()->has('country') ? session()->get('country') : '';
        if (auth()->user()->hasRole('super admin') && $country != '') {
            $applications->where('users.country', '=', $country);
        } else {
            if (!auth()->user()->hasRole('super admin') && !auth()->user()->hasRole('client')) {
                if (!auth()->user()->hasRole('admin')) {
                    if (request('history') != '1') {
                        $statuses = [];
                        if (auth()->user()->hasRole('processor')) {
                            $statuses = [4, 5, 6, 7, 8, 9, 10, 11, 12];
                        } else {
                            if (auth()->user()->hasRole('auditor')) {
                                $statuses = [];
                            } else {
                                if (auth()->user()->hasRole('debt collector')) {
                                    $statuses = [6, 9];
                                } else {
                                    if (auth()->user()->hasRole('loan approval')) {
                                        $statuses = [1, 2, 3, 4, 11, 12];
                                    } else {
                                        if (auth()->user()->hasRole('credit and processing')) {
                                            $statuses = [3, 4, 11, 12];
                                        }
                                    }
                                }
                            }
                        }
                        $applications->whereIn('loan_applications.loan_status', $statuses);
                    }
                }
                $applications->where('users.country', '=', auth()->user()->country);
            } else {
                if (auth()->user()->hasRole('client')) {
                    $applications->where('users.id', '=', auth()->user()->id);
                }
            }
        }

        if (request('user_id')) {
            $applications->where('users.id', '=', request('user_id'));
        }

        if (request('not_id')) {
            $applications->where('loan_applications.id', '!=', request('not_id'));
        }

        if (request('type')) {
            $applications->where('loan_applications.loan_status', '=', request('type'));
        }

        $applications = $applications->get();

        $data = [];
        foreach ($applications as $key => $loan) {
            $data[$key]['Client'] = $loan->firstname . ' ' . $loan->lastname;
            $data[$key]['Id Client'] = $loan->user_id_number;
            $data[$key]['Loan ID'] = $loan->id;
            $data[$key]['Type'] = $loan->loan_type_title;
            $data[$key]['Amount'] = Helper::decimalShowing($loan->amount, $loan->country);
            $data[$key]['Requested Date'] = Helper::date_to_current_timezone($loan->created_at);
            $data[$key]['Start Date'] = '';
            if ($loan->start_date != null) {
                $data[$key]['Start Date'] = Helper::date_time_to_current_timezone($loan->start_date);
            }
            $data[$key]['Outstanding Balance'] = $loan->outstanding_balance;
            $data[$key]['Completed Date'] = '';
            if ($loan->end_date != null) {
                $data[$key]['Completed Date'] = Helper::date_time_to_current_timezone($loan->end_date);
            }
            $data[$key]['Status'] = $loan->loan_status_title;
        }
        $filename = 'Loans -' . date('Ymd') . '-' . time();
        Excel::create($filename, function ($excel) use ($data) {
            $excel->setTitle('Report OF ' . date('d-m-Y H:i:s'));
            //Chain the setters
            $excel->sheet('Report', function ($sheet) use ($data) {
                $sheet->fromArray($data);
            });
        })->download('xlsx');
    }

    public function assignEmployee()
    {
        $data = [];
        $ids = explode(',', request('ids'));
        $data['status'] = LoanApplication::whereIn('id', $ids)->update([
            'employee_id' => request('employee_id'),
        ]);
        return $data;
    }

}
