<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Library\EmailHelper;
use App\Library\Helper;
use App\Models\Branch;
use App\Models\Country;
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
use App\Models\ProofType;
use App\Models\TransactionType;
use App\Models\User;
use App\Models\UserInfo;
use App\Models\UserWork;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\Facades\DataTables;

class LoanApplicationController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:super admin|admin|processor|auditor|debt collector|loan approval|credit and processing|client')
            ->only([
                'index',
                'getList',
                'ajaxStatusHistory',
                'getTransactions',
                'ajaxHostory',
                'show',
            ]);
        $this->middleware('role:super admin|admin|auditor|client|processor')
            ->only([
                'saveTransaction',
                'transactionDetail',
                'transactionUpdate',
                'loanHistory',
            ]);
        $this->middleware('role:super admin|admin|processor|debt collector|loan approval|credit and processing|client')
            ->only([
                'update',
            ]);
        $this->middleware('role:super admin|client')
            ->only([
                'ajaxHistoryEdit',
                'ajaxHistoryUpdate',
            ]);

        $this->middleware('role:super admin|admin|processor|debt collector|loan approval|credit and processing|client')
            ->only([
                'getTerritory',
                'getLoanTypeInfo',
            ]);

        $this->middleware('role:super admin|admin|processor|client')
            ->only([
                'validateLoanForm',
                'store',
                'edit',
                'destroy',
            ]);
    }

    /**
     * @desc loan crud
     * @date 18 Jun 2018 17:49
     */
    public function index()
    {
        $data = [];
        $data['reasons'] = LoanReason::getAllReasons()->pluck('title', 'id');
        $data['prooftypes'] = ProofType::orderBy('title', 'ASC')->get();
        $data['otherLoanTypes'] = ExistingLoanType::orderBy('title', 'ASC')->get();

        $country = session()->has('country') ? session()->get('country') : '';

        $client = User::select(DB::raw('concat(firstname," ",lastname) as name'), 'id')
            ->where('role_id', '=', '3');
        if (auth()->user()->hasRole('processor')) {
            $data['transactiontypes'] = TransactionType::orderBy('id', 'ASC')
                ->where('id', 1)
                ->where('id', '!=', 2)
                ->where('id', '!=', 3)
                ->get();
            $client->where(['users.country' => auth()->user()->country]);
        } else {
            $data['transactiontypes'] = TransactionType::orderBy('id', 'ASC')
                ->where('id', '!=', 4)
                ->where('id', '!=', 2)
                ->where('id', '!=', 3)
                ->get();
        }

        if (auth()->user()->hasRole('super admin') && $country != '') {
            $client->where(['users.country' => $country]);
        }

        $data['clients'] = $client->orderBy('firstname', 'asc')
            ->pluck('name', 'id');
        $data['declineReasons'] = LoanDeclineReason::get();
        $data['onHoldReasons'] = LoanOnHoldReason::get();
        $data['payment_types'] = config('site.payment_types');
        $data['cash_back_payment_types'] = config('site.cash_back_payment_types');
        if (request('status')) {
            $loanstatus = LoanStatus::find(request('status'));
            if ($loanstatus != null) {
                $data['status_name'] = $loanstatus->title;
            }
        }
        return view('common.loanapplication.index', $data);
    }

    public function getList()
    {
        $selection = [
            'loan_applications.*',
            'users.id_number as user_id_number',
            'users.firstname',
            'users.lastname',
            'loan_reasons.title as reason_title',
            'loan_types.title as loan_type_title',
            'loan_status.title as loan_status_title',
            'loan_calculation_histories.total as outstanding_balance'
        ];
        $applications = LoanApplication::select($selection)
            ->leftJoin('users', 'users.id', '=', 'loan_applications.client_id')
            ->leftJoin('loan_reasons', 'loan_reasons.id', '=', 'loan_applications.loan_reason')
            ->leftJoin('loan_types', 'loan_types.id', '=', 'loan_applications.loan_type')
            ->leftJoin('loan_status', 'loan_status.id', '=', 'loan_applications.loan_status')
            ->leftJoin('loan_calculation_histories', function ($left) {
                $left->on('loan_calculation_histories.loan_id', '=', 'loan_applications.id')
                    ->where('loan_calculation_histories.id', '=', DB::raw('(select max(lch.id) from loan_calculation_histories as lch where lch.loan_id=loan_calculation_histories.loan_id)'));
            });

        $country = session()->has('country') ? session()->get('country') : '';
        if (auth()->user()->hasRole('super admin') && $country != '') {
            $applications->where('users.country', '=', $country);
        } elseif (!auth()->user()->hasRole('super admin') && !auth()->user()->hasRole('client')) {
            if (!auth()->user()->hasRole('admin')) {
                $statuses = [];
                if (auth()->user()->hasRole('processor')) {
                    $statuses = [4, 5, 6, 7, 8, 9, 10, 11];
                } elseif (auth()->user()->hasRole('auditor')) {
                    $statuses = [];
                } elseif (auth()->user()->hasRole('debt collector')) {
                    $statuses = [6, 9];
                } elseif (auth()->user()->hasRole('loan approval')) {
                    $statuses = [1, 2, 3];
                } elseif (auth()->user()->hasRole('credit and processing')) {
                    $statuses = [3];
                }
                $applications->whereIn('loan_applications.loan_status', $statuses);
            }
            $applications->where('users.country', '=', auth()->user()->country);
        } elseif (auth()->user()->hasRole('client')) {
            $applications->where('users.id', '=', auth()->user()->id);
        }

        if (request('user_id')) {
            $applications->where('users.id', '=', request('user_id'));
        }

        if (request('not_id')) {
            $applications->where('users.id', '!=', request('not_id'));
        }

        if (request('status')) {
            $applications->where('loan_applications.loan_status', '=', request('status'));
        }

        return DataTables::of($applications)
            ->editColumn('user_first_name', function ($data) {
                return $data->firstname . ' ' . $data->lastname;
            })
            ->editColumn('created_at', function ($data) {
                return Helper::date_to_current_timezone($data->created_at);
            })
            ->editColumn('start_date', function ($data) {
                if ($data->start_date) {
                    return Helper::datebaseToFrontDate($data->start_date);
                } else {
                    return "";
                }
            })
            ->editColumn('end_date', function ($data) {
                if ($data->end_date) {
                    return Helper::datebaseToFrontDate($data->end_date);
                } else {
                    return "";
                }

            })
            ->addColumn('amount', function ($data) {
                return number_format($data->amount, 2);
            })
            ->addColumn('outstanding_balance', function ($data) {
                if ($data->outstanding_balance == '') {
                    return number_format($data->amount, 2);
                } else {
                    return number_format($data->outstanding_balance, 2);
                }
            })
            ->addColumn('action', function ($data) {
                $index = 0;
                $html = "";
                $url = route('loan-applications.edit', $data->id);
                $html .= '<div class="loan-actions">';
                if (collect([1, 2, 3])->contains($data->loan_status)) {
                    if (auth()->user()->hasRole('super admin|loan approval')) {
                        if (collect([1, 2])->contains($data->loan_status)) {
                            $html .= '<a href="javascript:;" onclick="confirmUpdateStatus(this)" data-id="' . $data->id . '" data-modal-id="approveLoanModal"  class="btn btn-sm waves-effect btn-success" title="Approve"><i class="fa fa-check"></i></a>';
                            $index++;
                            if ($index == 4) {
                                $html .= '<br>';
                                $index = 0;
                            }
                        }
                    }
                    if (auth()->user()->hasRole('super admin|loan approval|credit and processing')) {
                        if ($data->loan_status == 3 || !auth()->user()->hasRole('super admin|admin')) {
                            $html .= '<a href="javascript:;" onclick="confirmUpdateStatus(this,false)" data-id="' . $data->id . '" data-modal-id="rejectLoanModal"  class="btn btn-sm waves-effect btn-danger" title="Reject"><i class="fa fa-ban"></i></a>';
                        } else {
                            $html .= '<a href="javascript:;" onclick="confirmUpdateStatus(this)" data-id="' . $data->id . '" data-modal-id="rejectLoanModal"  class="btn btn-sm waves-effect btn-danger" title="Reject"><i class="fa fa-ban"></i></a>';
                        }
                        $index++;
                        if ($index == 4) {
                            $html .= '<br>';
                            $index = 0;
                        }
                    }
                }

                if ($data->loan_status == 1 && auth()->user()->hasRole('super admin|loan approval')) {
                    $html .= '<a href="javascript:;" onclick="confirmUpdateStatus(this)" data-id="' . $data->id . '" data-modal-id="notApprovedLoanModal"  class="btn btn-sm waves-effect btn-success" title="On Hold"><i class="fa fa-pause"></i></a>';
                    $index++;
                    if ($index == 4) {
                        $html .= '<br>';
                        $index = 0;
                    }
                }

                if (collect([1, 2, 10, 11])->contains($data->loan_status)
                    && auth()->user()->hasRole('super admin|client')) {
                    $html .= '<a href="javascript:;" data-modal-id="deleteLoanApplication" data-id="' . $data->id . '" onclick="DeleteConfirm(this)" class="btn btn-sm waves-effect btn-info" title="Delete"><i class="fa fa-trash"></i></a>';
                    $index++;
                    if ($index == 4) {
                        $html .= '<br>';
                        $index = 0;
                    }
                    $html .= '<a href="javascript:;" data-url="' . $url . '" onclick="editApplication(this)" class="btn btn-sm waves-effect btn-info" title="Edit"><i class="fa fa-pencil"></i></a>';
                    $index++;
                    if ($index == 4) {
                        $html .= '<br>';
                        $index = 0;
                    }
                }

                if ($data->loan_status == 3 && auth()->user()->hasRole('super admin|admin|processor|credit and processing')) {
                    $html .= '<a href="javascript:;" onclick="confirmUpdateStatus(this)" data-id="' . $data->id . '" data-modal-id="currentLoanModal"  class="btn btn-sm waves-effect btn-success" title="Current"><i class="fa fa-line-chart"></i></a>';
                    $index++;
                    if ($index == 4) {
                        $html .= '<br>';
                        $index = 0;
                    }
                }

                $html .= '<a target="_blank" href="' . url()->route('loan-applications.show', ['id' => $data->id]) . '" class="btn btn-sm waves-effect btn-info" title="View"><i class="fa fa-eye"></i></a>';
                $index++;
                if ($index == 4) {
                    $html .= '<br>';
                    $index = 0;
                }

                if (collect([4, 5, 6])->contains($data->loan_status)
                    && auth()->user()->hasRole('super admin|admin|auditor|processor')) {
                    $html .= '<a href="javascript:;" onclick="showTransaction(' . $data->id . ')" class="btn btn-sm waves-effect btn-info" title="Loan Transactions"><i class="fa fa-dollar"></i></a>';
                    $index++;
                    if ($index == 4) {
                        $html .= '<br>';
                        $index = 0;
                    }
                }

                if ($data->loan_status > 3 && $data->loan_status != 11
                    && auth()->user()->hasRole('super admin|admin|processor')) {
                    $html .= '<div class="btn-group loan-actions"><a class="btn btn-sm waves-effect btn-info display"' . $data->id . '" data-id="' . $data->id . '" href="' . url()->route('loan-applications.calculation-history', $data->id) . '" target="_blank" title="Transaction history"><i class="fa fa-history"></i></a></div>';
                    $index++;
                    if ($index == 4) {
                        $html .= '<br>';
                        $index = 0;
                    }
                }

                $html .= '<button class="btn btn-sm waves-effect btn-info showLoanStatusHistory display"' . $data->id . '" data-id="' . $data->id . '" title="Loan Status history"><i class="fa fa-list-alt"></i></button>';
                $index++;
                if ($index == 4) {
                    $html .= '<br>';
                    $index = 0;
                }

                /*if ($data->signature != null) {
                    $html .= '<a href="' . asset('uploads/' . $data->signature) . '" target="_blank" download="" class="btn btn-sm waves-effect btn-info" title="Signature"><i class="fa fa-paperclip"></i></a>';
                }*/
                if ($data->signature_pdf != null) {
                    $html .= '<a href="' . asset('pdf/' . $data->signature_pdf) . '" target="_blank" download="" class="btn btn-sm waves-effect btn-info" title="Loan Agreement PDF"><i class="fa fa-paperclip"></i></a>';
                }
                $html .= '</div>';
                return $html;
            })
            ->make(true);
    }

    public function validateLoanForm(Request $request)
    {
        http_response_code(500);

        $max = request('max_loan_amount');
        $salaryMax = request('salary');
        $this->validate(request(), LoanApplication::validationRules($max, $salaryMax, request('id')), LoanApplication::validationMessage());
        $data['status'] = true;
        return $data;
    }

    public function store()
    {
        http_response_code(500);
        $format = config('site.date_format.php');
        $max = request('max_loan_amount');
        $salaryMax = request('salary');

        //validation messages set
        $this->validate(request(), LoanApplication::validationRules($max, $salaryMax, request('id')), LoanApplication::validationMessage());

        $inputs = request()->all();
        unset($inputs['max_loan_amount']);
        $loggedUser = Auth::user();
        if ($loggedUser->hasRole('client')) {
            $inputs['client_id'] = $loggedUser->id;
        }
        $date = \DateTime::createFromFormat($format, request('date_of_payment')[0]);
        $inputs['deadline_date'] = $date->format('Y-m-d');

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
            //
            // if (request()->hasFile('expense_proof_image')) {
            //     $proof = request()->file('expense_proof_image');
            //     foreach ($proof as $key => $image) {
            //         $name = str_slug(pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME), '_');
            //         $ext = pathinfo($image->getClientOriginalName(), PATHINFO_EXTENSION);
            //         $imageName = time() . '_' . base64_encode(rand(1, 100)) . '_' . $name . '.' . $ext;
            //         $path = public_path('storage/loan_applications/' . $loanApplication->id);
            //         $image->move($path, $imageName);
            //
            //         $loanProof = LoanProof::create([
            //             'file_name' => $imageName,
            //         ]);
            //
            //         LoanAmounts::create([
            //             'loan_id'       => $loanApplication->id,
            //             'attachment_id' => $loanProof->id,
            //             'type'          => '2',
            //             'amount'        => request()->other_amount[$key],
            //             'amount_type'   => request()->expense_type[$key],
            //             'date'          => null,
            //         ]);
            //     }
            // }

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
                    \Storage::delete($path);
                    $loanProof->forceDelete();
                }
                $item->delete();
            }

            foreach (request()->income_amount as $key => $value) {
                $loanAmount = LoanAmounts::find(request()->income_id[$key]);
                $loanProof = '';
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
                        \Storage::delete($path);
                        $loanProof->update([
                            'file_name' => $imageName
                        ]);
                    } else {
                        $loanProof = LoanProof::create([
                            'file_name' => $imageName,
                        ]);
                    }
                    $date = NULL;
                    if (request()->date_of_payment[$key] != '') {
                        $dateObj = \DateTime::createFromFormat($format, request()->date_of_payment[$key]);
                        $date = $dateObj->format('Y-m-d');
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
                    \Storage::delete($path);
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
                            \Storage::delete($path);
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

        return redirect()->back()->with('success', "Applied");
    }

    public function show($id)
    {
        $lang = config('site.lang');
        if (!\App::getLocale('lang')) {
            \App::setLocale('eng');
        } else if (request('lang') && request('lang') != '' && in_array(request('lang'), [
                'eng',
                'esp',
                'pap'
            ]) && auth()->user()->hasRole('client')) {
            \App::setLocale(request('lang'));
        }
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
            $application->start_date = Helper::datebaseToFrontDate($application->start_date);
        }
        if ($application->end_date) {
            $application->end_date = Helper::datebaseToFrontDate($application->end_date);
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
                } elseif ($value->amount_type == '2') {
                    $value->title = 'Other Income';
                }
                $application->uploaded++;
            } elseif ($value->type == '2') {
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
        $country = Country::find($user->country);
        $data['loan']['user_documents'] = [];

        $array = [];
        $array[0]['key'] = "AddressProof";
        $array[0]['value'] = $user->address_proof != '' ? asset('uploads/' . $user->address_proof) : NULL;

        $array[1]['key'] = "OtherDocument";
        $array[1]['value'] = $user->other_document != '' ? asset('uploads/' . $user->other_document) : NULL;

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

        if (request('rst') && request('rst') == "json") {
            return $data;
        }
        return view('common.loanapplication.view', $data);
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
            $amount->date = Carbon::parse($amount->date)->format($format);
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
            'amount'               => ["type" => "select", 'value' => floatval($application->amount)],
            'loan_reason'          => ["type" => "select", 'value' => $application->loan_reason],
            'client_id'            => ["type" => "select", 'value' => $application->client_id],
            'payment_option'       => ["type" => "select", 'value' => $application->payment_option],
            'loan_type'            => ["type" => "select", 'value' => $application->loan_type],
            'pickup_address'       => ["type" => "select", 'value' => $application->pickup_address],
        ];

        return response()->json([
            "status"     => "success",
            "inputs"     => $filteredArr,
            "amounts"    => $amounts,
            "fileFolder" => asset('storage/loan_applications') . '/' . $application->id . '/',
        ]);
    }

    public function destroy($id)
    {
        //
        $loanApplication = LoanApplication::find($id);
        $loanApplication->delete();
        return response()->json([
            "status"  => "success",
            "message" => "Deleted successfully.",
        ]);
    }

    /**
     * @desc update status
     * @date 18 Jun 2018 18:21
     */
    public function update(Request $request, $id)
    {
        $inputs = $request->all();
        $status = LoanStatus::where(['title' => $inputs['status']])->first();
        $inputs['loan_status'] = $status->id;
        $loanApplication = LoanApplication::find($id);

        if ($inputs['status'] == 'current') {
            $tax_on_interest = round(($loanApplication->interest_amount * $loanApplication->tax_percentage) / 100, 2);
            $credit_amount = $loanApplication->amount - $loanApplication->origination_fee - $loanApplication->tax - $loanApplication->interest_amount - $tax_on_interest;
            Wallet::create([
                'user_id' => $loanApplication->client_id,
                'amount'  => $credit_amount,
                'notes'   => 'Approved Loan'
            ]);
            $inputs['start_date'] = Carbon::now();
        }
        if ($inputs['status'] == 'Paid in full - in default' || $inputs['status'] == 'Paid in full - debt.coll') {
            $inputs['end_date'] = Carbon::now();
        }
        $email_status = true;
        if ($loanApplication->loan_status == 3 && $inputs['loan_status'] == 11) {
            $email_status = false;
        }

        if (auth()->user()->hasRole('admin|super admin|processor|credit and processing') && $inputs['loan_status'] == 11 && $loanApplication->loan_status != 1) {
            LoanApplication::addLoanStatusHistory($loanApplication->id, 1, request('note'));
            $loanApplication->update($inputs);
            $loanApplication->update(['loan_status' => 1]);
        } else {
            if ($inputs['status'] == 'Declined' || $inputs['status'] == 'On Hold') {
                if (isset($inputs['reason'])) {
                    $inputs['loan_decline_reason'] = $inputs['reason'];
                }
                if (isset($inputs['description'])) {
                    $inputs['decline_description'] = $inputs['description'];
                }
            }
            LoanApplication::addLoanStatusHistory($loanApplication->id, $inputs['loan_status'], request('note'));
            $loanApplication->update($inputs);
        }

        $reason = LoanReason::find($loanApplication->loan_reason);

        if ($reason != null) {
            $loanApplication->reason = $reason->title;
        }
        if ($inputs['status'] == 'Declined') {
            $decline_reason = LoanDeclineReason::find($loanApplication->loan_decline_reason);

            if ($decline_reason != null) {
                $loanApplication->decline_reason_title = $decline_reason->title;
            }
        }
        if ($inputs['status'] == 'On Hold') {
            $decline_reason = LoanOnHoldReason::find($loanApplication->loan_decline_reason);
            if ($decline_reason != null) {
                $loanApplication->decline_reason_title = $decline_reason->title;
            }
        }
        if ($inputs['status'] == 'current') {
            \Artisan::call('loan:calculate', ['id' => $id]);
        }
        if ($inputs['status'] != 'approved') {
            if (auth()->user()->hasRole('admin|super admin|processor|credit and processing')) {
                if ($email_status) {
                    $user = User::find($loanApplication->client_id);
                    $country = Country::find($user->country);
                    EmailHelper::emailConfigChanges($country->mail_smtp);
                    $subject = config('mail.from.name') .': Your loan application has been ' . $inputs['status'] . '.';
                    if ($inputs['status'] == 'On Hold') {
                        $subject = "Action required";
                    }
                    try {
                        Mail::send('emails.loan-status', [
                            'loan'   => $loanApplication,
                            'user'   => $user,
                            'status' => $inputs['status']
                        ],
                            function ($message) use ($user, $inputs, $subject) {
                                $message->from(config('mail.from.address'), config('mail.from.name'));
                                $message->to($user->email);
                                $message->bcc(config('site.bcc_users'));
                                $message->subject($subject);
                            });
                    } catch (\Exception $e) {
                        Log::info($e);
                    }
                }

                $loanApplication->notificationSend();
            }
        }
        return response()->json([
            "status"  => "success",
            "message" => "Saved successfully.",
        ]);
    }


    /**
     * @desc transaction related
     * @date 18 Jun 2018 18:59
     */

    public function saveTransaction(Request $request)
    {
        $rules = [
            'loan_id'      => 'required|numeric',
            'payment_date' => 'required|date_format:d/m/Y',
        ];

        if (auth()->user()->hasRole('super admin')) {
            $rules += [
                'branch_id' => 'required|numeric|exists:branches,id',
            ];
        }

        if (!request('write_off') || request('write_off') != true) {
            $rules += [
                'next_payment_date' => 'required|date_format:d/m/Y',
                'payment_amount'    => 'required',
                'cashback_amount'   => 'required',
                'transaction_type'  => 'required|numeric',
            ];
        }

        $this->validate($request, $rules);
        $application = LoanApplication::find($request->loan_id);
        $format = config('site.date_format.php');
        $payment_date = NULL;
        if ($request->payment_date != '') {
            $dateObj = \DateTime::createFromFormat($format, $request->payment_date);
            $payment_date = $dateObj->format('Y-m-d');
        }
        $next_payment_date = NULL;
        if ($request->next_payment_date != '') {
            $dateObj = \DateTime::createFromFormat($format, $request->next_payment_date);
            $next_payment_date = $dateObj->format('Y-m-d');
        }
        $total_amount = 0;
        if (!request('write_off') || request('write_off') != true) {
            $transaction_ids = [];
            foreach ($request->payment_amount as $key => $value) {
                $cashback_amount = 0;
                if (isset($request->cashback_amount[$key]) && $request->cashback_amount[$key] != '') {
                    $cashback_amount = $request->cashback_amount[$key];
                }
                if ($value == '') {
                    $value = 0;
                }
                $transaction_type = $request->transaction_type;
                if (request('write_off') == true) {
                    $transaction_type = 2;
                }
                $total_amount = $total_amount + $value;

                $inputs = [
                    'loan_id'           => $request->loan_id,
                    'client_id'         => $application->client_id,
                    'amount'            => $value,
                    'transaction_type'  => $transaction_type,
                    'payment_type'      => $key,
                    'cash_back_amount'  => $cashback_amount,
                    'notes'             => request('notes'),
                    'created_by'        => Auth::user()->id,
                    'payment_date'      => $payment_date,
                    'next_payment_date' => $next_payment_date
                ];

                if (auth()->user()->hasRole('super admin')) {
                    $inputs['branch_id'] = request('branch_id');
                } else {
                    $inputs['branch_id'] = session('branch_id');
                }

                $transaction = LoanTransaction::create($inputs);
                $transaction_ids[] = $transaction->id;
            }


            $loan_histories = LoanCalculationHistory::where('loan_id', '=', $request->loan_id)
                ->where('date', '>', $payment_date)
                ->orderBy('id', 'asc')
                ->get();

            \Log::info($loan_histories);

            $loan_histories_id = $loan_histories->pluck('id');

            LoanCalculationHistory::whereIn('id', $loan_histories_id)->delete();

            $entry = true;
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
                        $history_transactions = LoanTransaction::whereIn('id', $transaction_ids)->get();
                        $history = collect([
                            'date'    => $payment_date,
                            'loan_id' => $request->loan_id
                        ]);
                        LoanCalculationHistory::calculationHistoryChange($history, 'payment', $history_transactions->pluck('id'), $payment_date);
                        if ($value->payment_amount != null) {
                            LoanCalculationHistory::calculationHistoryChange($value, 'payment', $loan_transactions->pluck('id'));
                        } else {
                            LoanCalculationHistory::calculationHistoryChange($value, 'week', []);
                        }
                        $entry = false;
                    }
                }
            } else {
                $history_transactions = LoanTransaction::whereIn('id', $transaction_ids)->get();
                $history = collect([
                    'date'    => $payment_date,
                    'loan_id' => $request->loan_id
                ]);
                LoanCalculationHistory::calculationHistoryChange($history, 'payment', $history_transactions->pluck('id'), $payment_date);
            }

        }
        if (request('write_off') == true) {
            $total = 0;
            $total_e_tax = 0;
            $amount = 0;
            $origination_fee = 0;
            $interest = 0;
            $renewal = 0;
            $tax_for_origination = 0;
            $tax_for_renewal = 0;
            $tax_for_interest = 0;
            $tax = 0;
            $debt = 0;
            $debt_tax = 0;

            $last_histroy = LoanCalculationHistory::where('loan_id', '=', $request->loan_id)
                ->orderBy('id', 'desc')
                ->first();
            $week_iterations = 0;
            if ($last_histroy != null) {
                $week_iterations = $last_histroy['week_iterations'];
            }

            $last_histroy = LoanCalculationHistory::create([
                'loan_id'             => $request->loan_id,
                'week_iterations'     => $week_iterations,
                'payment_amount'      => $total_amount,
                'date'                => date('Y-m-d'),
                'transaction_name'    => 'Write off',
                'principal'           => $amount,
                'origination'         => $origination_fee,
                'interest'            => $interest,
                'renewal'             => $renewal,
                'tax_for_origination' => $tax_for_origination,
                'tax_for_renewal'     => $tax_for_renewal,
                'tax_for_interest'    => $tax_for_interest,
                'tax'                 => $tax,
                'debt'                => $debt,
                'debt_tax'            => $debt_tax,
                'total_e_tax'         => $total_e_tax,
                'total'               => $total,
            ]);

            $loan = LoanApplication::where('id', '=', $request->loan_id)->update([
                'loan_status' => 10
            ]);
            LoanApplication::addLoanStatusHistory($request->loan_id, '10');
        }

        // User::where('id', '=', $application->client_id)->update([
        //     'status' => 3
        // ]);
        return response()->json([
            "status" => "success",
        ]);
    }

    public function getTransactions($loanId)
    {
        $transactions = LoanTransaction::with('createdUser')->with('type')->select('loan_transactions.*')
            ->where('loan_id', '=', $loanId);
        return DataTables::of($transactions)
            ->addColumn('payment_type', function ($data) {
                if ($data->payment_type != null && $data->transaction_type == 1) {
                    return config('site.payment_types') [$data->payment_type];
                }
            })
            ->addColumn('amount', function ($data) {
                return number_format($data->amount, 2);
            })
            ->addColumn('cash_back_amount', function ($data) {
                return number_format($data->cash_back_amount, 2);
            })
            ->editColumn('created_user.firstname', function ($data) {
                return $data->createdUser->firstname . ' ' . $data->createdUser->lastname;
            })
            ->editColumn('created_at', function ($data) {
                return Helper::datebaseToFrontDate($data->payment_date);
            })
            ->addColumn('action', function ($data) {
                if ($data->type->id == 1 && auth()->user()->hasRole('admin|super admin')) {
//                    return "<button class='btn btn-info editTransaction' data-id='" . $data->id . "' title='Edit'><i class='fa fa-pencil'></i></button>";
                }
            })
            ->make();
    }

    public function transactionDetail(LoanTransaction $transaction)
    {
        $data = [];
        $data['transaction'] = $transaction;
        $data['payment_types'] = config('site.payment_types');
        return $data;
    }

    public function transactionUpdate(LoanTransaction $transaction)
    {
        $data = [];
        $data['status'] = $transaction->update(request()->all());
        return $data;
    }

    /**
     * @desc ajax needed
     * @date 18 Jun 2018 18:28
     */
    public function getTerritory(Request $request)
    {
        if ($request->user == 'currentUser') {
            $user = Auth::user();
        } else {
            $user = User::find($request->user);
        }

        $country = Country::where('id', '=', $user->country)->first();

        $types = LoanType::activeLoanTypesViaUserId($user)->pluck('title', 'id');

        return response()->json([
            "status"     => "success",
            "country"    => $country,
            "loan_types" => $types,
            "user"       => $user,
        ]);
    }

    public function getLoanTypeInfo()
    {
        $data = [];
        $data['status'] = false;
        if (request('loan_type')) {
            $data['loan_type'] = LoanType::find(request('loan_type'));
            if ($data['loan_type'] != null) {
                $data['status'] = true;
            }
        }
        return $data;
    }


    /**
     * @desc calculation history
     * @date 19 Jun 2018 10:31
     */
    public function ajaxHostory(LoanApplication $loan)
    {
        if (request('newEntry')) {
            \Artisan::call('loan:calculate', ['id' => $loan->id]);
            // return var_dump(exec('php /home/forge/ccash.testflight.in/caribbeancash_web/artisan loan:calculate'));
        }
        $data = [];
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
        return $data;
    }

    public function loanHistory(LoanApplication $loan)
    {
        $data = [];
        $data['loan'] = $loan;
        $data['payment_types'] = config('site.payment_types');
        $data['cash_back_payment_types'] = config('site.cash_back_payment_types');
        return view('common.loanapplication.calculation', $data);
    }

    public function ajaxHistoryEdit(LoanCalculationHistory $history)
    {
        $data = [];
        $data['history'] = $history;
        $data['first_history'] = LoanCalculationHistory::where('loan_id', '=', $history->loan_id)
            ->orderBy('id', 'asc')
            ->first();
        $data['last_history'] = LoanCalculationHistory::where('loan_id', '=', $history->loan_id)
            ->orderBy('id', 'desc')
            ->first();
        $data['payments'] = LoanTransaction::where('used', '=', $history->id)->get();
        return $data;
    }

    public function ajaxHistoryUpdate(LoanCalculationHistory $history)
    {
        $data = [];
        $loan = LoanApplication::find($history->loan_id);
        $loan_transactions = LoanTransaction::where('used', '=', $history->id)->get();
        $format = config('site.date_format.php');
        $date = request('date');
        $date = \DateTime::createFromFormat($format, $date);
        $date = $date->format('Y-m-d');
        foreach (request('payment_amount') as $key => $value) {
            $transaction = $loan_transactions->where('payment_type', '=', $key)->first();
            if ($transaction != null) {
                $transaction->update([
                    'amount'       => $value,
                    'payment_date' => $date,
                    'notes'        => request('notes')
                ]);
            } else {
                $cashback_amount = 0;
                if (isset(request('cashback_amount')[$key]) && request('cashback_amount')[$key] != '') {
                    $cashback_amount = request('cashback_amount')[$key];
                }
                if ($value == '') {
                    $value = 0;
                }
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

        LoanCalculationHistory::whereIn('id', $loan_histories_id)->orwhere('id', '=', $history->id)->delete();
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
        }
        return $data;
    }


    /**
     * @desc status history
     * @date 19 Jun 2018 10:32
     */

    public function ajaxStatusHistory($loan)
    {
        $data = [];
        $data['history'] = LoanStatusHistory::select('loan_status_histories.*', DB::raw('concat(users.firstname," ",users.lastname) as user_name'), 'loan_status.title as loan_status')
            ->leftJoin('users', 'users.id', '=', 'loan_status_histories.user_id')
            ->leftJoin('loan_status', 'loan_status.id', '=', 'loan_status_histories.status_id')
            ->where('loan_status_histories.loan_id', '=', $loan)
            ->get();
        foreach ($data['history'] as $key => $value) {
            if ($value->note == null) {
                $value->note = '';
            }
            $value->date = Helper::date_time_to_current_timezone($value->created_at);
        }
        return $data;
    }

    public function notesListing(LoanApplication $loan)
    {
        $data = [];
        $data['notes'] = LoanNotes::select('loan_notes.*', DB::raw('concat(users.firstname," ",users.lastname) as user_name'))
            ->leftJoin('users', 'users.id', '=', 'loan_notes.user_id')
            ->where('loan_notes.loan_id', '=', $loan->id)
            ->orderBy('loan_notes.id', 'desc')
            ->limit(3)
            ->get();
        foreach ($data['notes'] as $key => $value) {
            $value->date = Helper::datebaseToFrontDate($value->date);
            $value->follow_up = Helper::datebaseToFrontDate($value->follow_up);
        }
        return $data;
    }

    public function notesEdit(LoanApplication $loan, LoanNotes $note)
    {
        $filteredArr = [
            'id'        => ["type" => "hidden", 'value' => $note->id],
            'date'      => ["type" => "text", 'value' => date('d/m/Y', strtotime($note->date))],
            'follow_up' => ["type" => "text", 'value' => date('d/m/Y', strtotime($note->follow_up))],
            'details'   => ["type" => "textarea", 'value' => $note->details],
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

    public function loanNotesDestroy(LoanNotes $note)
    {
        $data = [];
        $data['status'] = $note->delete();
        return $data;
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
}
