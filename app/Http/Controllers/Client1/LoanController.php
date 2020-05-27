<?php

namespace App\Http\Controllers\Client1;

use App\Http\Controllers\Controller;
use App\Library\Helper;
use App\Models\Country;
use App\Models\ExistingLoanType;
use App\Models\LoanAmounts;
use App\Models\LoanApplication;
use App\Models\LoanCalculationHistory;
use App\Models\LoanProof;
use App\Models\LoanReason;
use App\Models\LoanTransaction;
use App\Models\LoanType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class LoanController extends Controller
{
    public function index()
    {
        $data = [];
        $data['country'] = Country::find(auth()->user()->country);
        $data['reasons'] = LoanReason::getAllReasons()->pluck('title', 'id');
        $data['otherLoanTypes'] = ExistingLoanType::getType();
        $data['types'] = LoanType::activeLoanTypesViaUserId(auth()->user())->pluck('title', 'id');
        return view('client1.pages.loans.index', $data);
    }

    public function create()
    {
        $data = [];
        $data['has_active_loan'] = LoanApplication::hasActiveLoan(auth()->user());
        $data['has_active_loan_error'] = Lang::get('validation.active_loan', [], App::getLocale());
        $data['country'] = Country::find(auth()->user()->country);
        $data['reasons'] = LoanReason::getAllReasons()->pluck('title', 'id');
        $data['otherLoanTypes'] = ExistingLoanType::getType();
        $data['types'] = LoanType::activeLoanTypesViaUserId(auth()->user())->pluck('title', 'id');
        return view('client1.pages.loans.create', $data);
    }

    public function store()
    {
        http_response_code(500);
        $format = config('site.date_format.php');

        //validation messages set
        $this->validate(request(), LoanApplication::clientValidationRules(request()->all()), LoanApplication::validationMessage());

        if (request('id') == '' || request('id') == '0') {
            if (LoanApplication::hasActiveLoan(auth()->user())) {
                $data = [];
                $data['client_id'] = [
                    Lang::get('validation.active_loan', [], App::getLocale())
                ];
                return response($data, 422);
            }
        }

        $inputs = request()->only(['loan_type', 'amount', 'loan_reason']);
        $inputs['amount'] = floatval($inputs['amount']);
        $user = auth()->user();

        $salary = array_sum(request('income_amount'));
        $other_loan_deduction = 0;
        if (request('other_amount')) {
            $other_loan_deduction = array_sum(request('other_amount'));
        }

        $loan_type = LoanType::find($inputs['loan_type']);

        $max_amount = round((($salary - $other_loan_deduction) * $loan_type->loan_component) / 100, 2);

        if ($max_amount < 0) {
            $data = [];
            $data['errors'] = [
                'other_loan_amount' => [Lang::get('keywords.other_loan_amount_greater', ['salary' => $salary], App::getLocale())]
            ];
            return response($data, 422);
        }

        if ($max_amount < $inputs['amount'] && $user->web_registered == null) {
            $data = [];
            $data['amount'] = [
                Lang::get('validation.min.custom', [
                    'attribute' => Lang::get('keywords.Requested Amount', [], App::getLocale()),
                    'min'       => Lang::get('keywords.Suggested Loan Amount', [], App::getLocale())
                ], App::getLocale())
            ];
            return response($data, 422);
        }

        $date = \DateTime::createFromFormat($format, request('date_of_payment')[0]);
        $deadline_date = $date->format('Y-m-d');

        DB::transaction(function () use ($format, $inputs, $loan_type, $user, $max_amount, $salary, $other_loan_deduction, $deadline_date) {
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
                'client_id'                  => $user->id
            ];

            $country = Country::find($user->country);

            //tax calculation
            $inputs += [
                'tax_percentage' => $country->tax_percentage,
                'tax_name'       => $country->tax
            ];

            $origination_fee = 0;
            if ($loan_type->origination_type == 1) {
                $origination_fee = request('amount') * $loan_type->origination_amount / 100;
            } else if ($loan_type->origination_type == 2) {
                $origination_fee = $loan_type->origination_amount;
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

            if (request('id') == '0') {
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
                $inputs['signature'] = Helper::base64ToJpeg(request('signature'), 'signature_' . $loanApplication->id);
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
                        $date = NULL;
                        if (isset(request()->date_of_payment[$key]) && request()->date_of_payment[$key] != '') {
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
                            'date'        => null
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
                    $date = NULL;
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
                                'file_name' => $imageName
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
                                'date'          => $date
                            ]);
                        } else {
                            LoanAmounts::create([
                                'loan_id'       => $loanApplication->id,
                                'attachment_id' => $loanProof->id,
                                'type'          => '1',
                                'amount'        => request()->income_amount[$key],
                                'amount_type'   => request()->income_type[$key],
                                'date'          => $date
                            ]);
                        }
                    } else {
                        if ($loanAmount) {
                            $loanAmount->update([
                                'type'        => '1',
                                'amount'      => request()->income_amount[$key],
                                'amount_type' => request()->income_type[$key],
                                'date'        => $date
                            ]);
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
                                    'file_name' => $imageName
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
                                    'date'          => null
                                ]);
                            } else {
                                LoanAmounts::create([
                                    'loan_id'       => $loanApplication->id,
                                    'type'          => '2',
                                    'attachment_id' => $loanProof->id,
                                    'amount'        => request()->other_amount[$key],
                                    'amount_type'   => request()->expense_type[$key],
                                    'date'          => null
                                ]);
                            }
                        } else {
                            if ($loanAmountExp) {
                                $loanAmountExp->update([
                                    'type'        => '2',
                                    'amount'      => request()->other_amount[$key],
                                    'amount_type' => request()->expense_type[$key],
                                    'date'        => null
                                ]);
                            } else {
                                $loanAmount = LoanAmounts::create([
                                    'loan_id'     => $loanApplication->id,
                                    'type'        => '2',
                                    'amount'      => request()->other_amount[$key],
                                    'amount_type' => request()->expense_type[$key],
                                    'date'        => null
                                ]);
                            }
                        }
                    }
                }
            }
        });

        return redirect()->back()->with('success', "Applied");
    }

    public function show($id)
    {
        $lang = config('site.lang');
        $application = LoanApplication::with('user')
            ->with('reason')
            ->with('type')
            ->with('status')
            ->with('declineReason')
            ->with('onHoldReason')
            ->with('amounts.documents')
            ->find($id);

        if ($application->reason) {
            if (\App::getLocale() == $lang["esp"] && auth()->user()->hasRole('client')) {
                $application->reason->title = $application->reason->title_es;
            } else if (\App::getLocale() == $lang["pap"] && auth()->user()->hasRole('client')) {
                $application->reason->title = $application->reason->title_nl;
            }
        }
        if ($application->declineReason) {
            if (\App::getLocale() == $lang["esp"] && auth()->user()->hasRole('client')) {
                $application->declineReason->title = $application->declineReason->title_es;
            } else if (\App::getLocale() == $lang["pap"] && auth()->user()->hasRole('client')) {
                $application->declineReason->title = $application->declineReason->title_nl;
            }
        }
        if ($application->onHoldReason) {
            if (\App::getLocale() == $lang["esp"] && auth()->user()->hasRole('client')) {
                $application->onHoldReason->title = $application->onHoldReason->title_es;
            } else if (\App::getLocale() == $lang["pap"] && auth()->user()->hasRole('client')) {
                $application->onHoldReason->title = $application->onHoldReason->title_nl;
            }
        }
        if ($application->type) {
            if (\App::getLocale() == $lang["esp"] && auth()->user()->hasRole('client')) {
                $application->type->title = $application->type->title_es;
            } else if (\App::getLocale() == $lang["pap"] && auth()->user()->hasRole('client')) {
                $application->type->title = $application->type->title_nl;
            }
        }
        if ($application->start_date) {
            $application->start_date = Helper::date_time_to_current_timezone($application->start_date);
        }
        if ($application->end_date) {
            $application->end_date = Helper::date_time_to_current_timezone($application->end_date);
        }
        if ($application->deadline_date && $application->deadline_date != '') {
            $application->deadline_date = Helper::datebaseToFrontDate($application->deadline_date);
        }

        $application->uploaded = 0;
        $application->otherLoan = 0;
        foreach ($application->amounts as $key => $value) {
            if ($value->type == '1') {
                if ($value->amount_type == '1') {
                    $value->title = 'Gross salary';
                } else if ($value->amount_type == '2') {
                    $value->title = 'Other Income';
                }
                $application->uploaded++;
            } else if ($value->type == '2') {
                $existing_loan_type = ExistingLoanType::find($value->amount_type);
                if ($existing_loan_type != null) {
                    if (\App::getLocale() == $lang["esp"] && auth()->user()->hasRole('client')) {
                        $value->title = $existing_loan_type->title_es;
                    } else if (\App::getLocale() == $lang["pap"] && auth()->user()->hasRole('client')) {
                        $value->title = $existing_loan_type->title_nl;
                    } else {
                        $value->title = $existing_loan_type->title;
                    }
                }
                $application->otherLoan++;
            }
        }
        $data['loan'] = $application->toArray();
        $user = User::find($application->client_id);
        $data['loan']['user_documents'] = [];

        $array = [];
        $array[0]['key'] = "AddressProof";
        $array[0]['value'] = $user->address_proof != '' ? asset('uploads/' . $user->address_proof) : NULL;

        $array[2]['key'] = "ScanId";
        $array[2]['value'] = $user->scan_id != '' ? asset('uploads/' . $user->scan_id) : NULL;
        if ($user->exp_date < date('Y-m-d')) {
            $array[2]['expires'] = Helper::datebaseToFrontDate($user->exp_date);
        } else {
            $array[2]['expires'] = null;
        }

        $array[3]['key'] = "Paysilp1";
        $array[3]['value'] = $user->payslip1 != '' ? asset('uploads/' . $user->payslip1) : NULL;

        $array[4]['key'] = "Paysilp2";
        $array[4]['value'] = $user->payslip2 != '' ? asset('uploads/' . $user->payslip2) : NULL;

        foreach ($array as $key => $value) {
            $data['loan']['user_documents'][] = $value;
        }
        $data['loan'] = $data['loan'];
        if (request('rst') && request('rst') == "json") {
            return $data;
        }
        return view('client1.pages.loans.show', $data);
    }

    public function edit($id)
    {
        $format = config('site.date_format.php');
        $application = LoanApplication::with('user')
            ->with('reason')
            ->with('type')
            ->with('status')
            ->with('declineReason')
            ->with('amounts.documents')
            ->find($id);
        $amounts = [];
        foreach ($application->amounts as $amount) {
            if ($amount->date != null) {
                $amount->date = Carbon::parse($amount->date)->format($format);
            }
            $amounts[] = $amount;
        }
        $filteredArr = [
            'id'                   => ["type" => "hidden", 'value' => $application->id],
            'salary'               => ["type" => "text", 'value' => $application->salary],
            'salary_date'          => [
                "type"  => "text",
                'value' => Carbon::parse($application->deadline_date)->format('d/m/Y')
            ],
            'loan_duration'        => ["type" => "text", 'value' => $application->loan_duration],
            'loan_interest'        => ["type" => "text", 'value' => $application->loan_interest],
            'other_loan_deduction' => ["type" => "text", 'value' => $application->other_loan_deduction],
            'amount'               => ["type" => "select2", 'value' => floatval($application->amount)],
            'loan_reason'          => ["type" => "select2", 'value' => $application->loan_reason],
            'loan_type'            => ["type" => "select2", 'value' => $application->loan_type],
        ];

        return response()->json([
            "status"     => "success",
            "inputs"     => $filteredArr,
            "amounts"    => $amounts,
            "fileFolder" => asset('storage/loan_applications') . '/' . $application->id . '/',
        ]);
    }

    public function transactionDatatable($loan)
    {
        $selection = [
            'loan_transactions.*',
            'transaction_types.title as transaction_type_eng',
            'transaction_types.title_es as transaction_type_esp',
            'transaction_types.title_nl as transaction_type_pap',
        ];

        $transactions = LoanTransaction::select($selection)
            ->leftJoin('transaction_types', 'transaction_types.id', '=', 'loan_transactions.transaction_type')
            ->where('loan_transactions.loan_id', '=', $loan)
            ->where(function ($query) {
                $query->where('loan_transactions.amount', '>', 0)
                    ->orwhere('loan_transactions.cash_back_amount', '>', 0);
            });
        return DataTables::of($transactions)
            ->addColumn('payment_type', function ($row) {
                if ($row->payment_type != null && $row->transaction_type == 1) {
                    return config('site.payment_types') [$row->payment_type];
                }
            })
            ->addColumn('transaction_type', function ($row) {
                if (App::getLocale() == 'esp') {
                    return $row->transaction_type_esp;
                } else if (App::getLocale() == 'pap') {
                    return $row->transaction_type_pap;
                } else {
                    return $row->transaction_type_eng;
                }
            })
            ->addColumn('amount', function ($row) {
                return number_format($row->amount, 2);
            })
            ->addColumn('cash_back_amount', function ($row) {
                return number_format($row->cash_back_amount, 2);
            })
            ->editColumn('payment_date', function ($row) {
                return Helper::datebaseToFrontDate($row->payment_date);
            })
            ->editColumn('created_at', function ($row) {
                return Helper::date_time_to_current_timezone($row->created_at);
            })
            ->make();
    }

    public function indexDatatable()
    {
        $user = auth()->user();

        $selection = [
            'loan_applications.*',
            'loan_reasons.title as reason_title',
            'loan_reasons.title_es as reason_title_es',
            'loan_reasons.title_nl as reason_title_nl',
            'loan_types.title as loan_type_title',
            'loan_types.title_es as loan_type_title_es',
            'loan_types.title_nl as loan_type_title_nl',
            'loan_status.title as loan_status_title',
            'loan_status.title_es as loan_status_title_es',
            'loan_status.title_nl as loan_status_title_nl',
            'loan_decline_reasons.title as loan_decline_reasons_title',
            'loan_decline_reasons.title_es as loan_decline_reasons_title_es',
            'loan_decline_reasons.title_nl as loan_decline_reasons_title_nl',
            'loan_on_hold_reasons.title as loan_on_hold_reasons_title',
            'loan_on_hold_reasons.title_es as loan_on_hold_reasons_title_es',
            'loan_on_hold_reasons.title_nl as loan_on_hold_reasons_title_nl',
        ];

        $applications = LoanApplication::select($selection)
            ->where(['client_id' => $user->id])
            ->leftJoin('loan_reasons', function ($left) {
                $left->on('loan_reasons.id', '=', 'loan_applications.loan_reason');
            })
            ->leftJoin('loan_types', function ($left) {
                $left->on('loan_types.id', '=', 'loan_applications.loan_type');
            })
            ->leftJoin('loan_status', function ($left) {
                $left->on('loan_status.id', '=', 'loan_applications.loan_status');
            })
            ->leftJoin('loan_decline_reasons', function ($left) {
                $left->on('loan_decline_reasons.id', '=', 'loan_applications.loan_decline_reason');
            })
            ->leftJoin('loan_on_hold_reasons', function ($left) {
                $left->on('loan_on_hold_reasons.id', '=', 'loan_applications.loan_decline_reason');
            });
        return DataTables::of($applications)
            ->editColumn('decline_reason', function ($row) {
                if ($row->loan_status == 3) {
                    if (App::getLocale() == 'esp') {
                        return $row->loan_decline_reasons_title_es;
                    } else if (App::getLocale() == 'pap') {
                        return $row->loan_decline_reasons_title_nl;
                    } else {
                        return $row->loan_decline_reasons_title;
                    }
                } else if ($row->loan_status == 2) {
                    if (App::getLocale() == 'esp') {
                        return $row->loan_on_hold_reasons_title_es;
                    } else if (App::getLocale() == 'pap') {
                        return $row->loan_on_hold_reasons_title_nl;
                    } else {
                        return $row->loan_on_hold_reasons_title;
                    }
                } else {
                    return '';
                }
            })
            ->editColumn('type', function ($row) {
                if ($row->type) {
                    if (App::getLocale() == 'esp') {
                        return $row->loan_type_title_es;
                    } else if (App::getLocale() == 'pap') {
                        return $row->loan_type_title_nl;
                    } else {
                        return $row->loan_type_title;
                    }
                }
                return '';
            })
            ->editColumn('status', function ($row) {
                if ($row->status) {
                    if (App::getLocale() == 'esp') {
                        return $row->loan_status_title_es;
                    } else if (App::getLocale() == 'pap') {
                        return $row->loan_status_title_nl;
                    } else {
                        return $row->loan_status_title;
                    }
                }
                return '';
            })
            ->editColumn('reason', function ($row) {
                if ($row->reason) {
                    if (App::getLocale() == 'esp') {
                        return $row->reason_title_es;
                    } else if (App::getLocale() == 'pap') {
                        return $row->reason_title_nl;
                    } else {
                        return $row->reason_title;
                    }
                }
                return '';
            })
            ->addColumn('amount', function ($row) {
                return number_format($row->amount, 2);
            })
            ->editColumn('created_at', function ($row) {
                return Helper::date_to_current_timezone($row->created_at);
            })
            ->editColumn('start_date', function ($row) {
                if ($row->start_date) {
                    return Helper::date_time_to_current_timezone($row->start_date);
                } else {
                    return "";
                }
            })
            ->editColumn('end_date', function ($row) {
                if ($row->end_date) {
                    return Helper::date_time_to_current_timezone($row->end_date);
                } else {
                    return "";
                }

            })
            ->addColumn('action', function ($row) {
                $html = "";
                $url = route('loan-applications.edit', $row->id);
                if ($row->loan_status == 1 || $row->loan_status == 2) {
                    $html .= '<a href="javascript:;"  data-toggle="tooltip" title="' . __('keywords.delete') . '" 
                                   data-id="' . $row->id . '" class="action-button deleteLoan">
                                <i class="material-icons">delete</i>
                            </a>';

                    $html .= '<a href="javascript:;"  data-toggle="tooltip" title="' . __('keywords.edit') . '" 
                                    data-url="' . $url . '" class="action-button editLoan" data-id="' . $row->id . '">
                                <i class="material-icons">edit</i>
                            </a>';
                }
                if (!in_array($row->loan_status, [1, 2, 3, 11])) {
                    $url = url()->route('client1.loans.calculation', ['id' => $row->id]);
                    $html .= '<a  data-toggle="tooltip" title="' . __('keywords.LoanHistory') . '" target="_blank" 
                                href="' . $url . '"  class="action-button">
                            <i class="material-icons">history</i>
                        </a>';
                }
                $url = url()->route('client1.loans.show', ['id' => $row->id]);
                $html .= '<a  data-toggle="tooltip" title="' . __('keywords.view') . '" target="_blank" 
                                href="' . $url . '"  class="action-button">
                            <i class="material-icons">remove_red_eye</i>
                        </a>';
                return $html;
            })
            ->make(true);
    }

    public function loanTypeInfo(LoanType $type)
    {
        $data = [];
        $data['type'] = $type;
        return $data;
    }

    public function destroy($id)
    {
        $loanApplication = LoanApplication::find($id);
        $loanApplication->update([
            'deleted_by' => auth()->user()->id
        ]);
        $loanApplication->delete();
        return response()->json([
            "status"  => "success",
            "message" => Lang::get('keywords.deleted_successfully', [], App::getLocale()),
        ]);
    }

    public function calculationShow(LoanApplication $loan)
    {
        $data = [];

        $data['loan'] = $loan;

        return view('client1.pages.loans.calculation', $data);
    }

    public function getLoanHistory(LoanApplication $loan)
    {
        $data = [];
        if ($loan->client_id == auth()->user()->id) {
            $history = LoanCalculationHistory::select('*')
                ->where('loan_id', '=', $loan->id)
                ->orderBy('id', 'desc')
                ->get();
            $history = $history->map(function ($item, $key) {
                $item->date = Helper::datebaseToFrontDate($item->date);
                if ($item->payment_amount == null) {
                    $item->payment_amount = '';
                }
                if ($item->week_iterations == 0 && ($item->total_e_tax == 0.00 || $item->total_e_tax == null)) {
                    $item->total_e_tax = '';
                }
                return $item;
            });
            $data['history'] = $history;
        }
        return $data;
    }

    public function getTransactionReceipt(LoanApplication $loan, LoanCalculationHistory $history)
    {
        $data = [];
        if ($loan->client_id == auth()->user()->id && $history->loan_id == $loan->id) {
            $data['receipt_pdf'] = LoanTransaction::createReceipt($history);
        }
        return $data;
    }
}
