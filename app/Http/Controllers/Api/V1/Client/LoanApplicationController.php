<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Http\Controllers\Controller;
use App\Library\Api;
use App\Models\Country;
use App\Models\ExistingLoanType;
use App\Models\LoanAmounts;
use App\Models\LoanApplication;
use App\Models\LoanProof;
use App\Models\LoanReason;
use App\Models\LoanType;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoanApplicationController extends Controller
{
    /**
     * @SWG\Get(
     *   path="/loan-contract/{loantype_id}",
     *   summary="loan contract page",
     *     tags={"loans"},
     *     @SWG\Parameter(name="loantype_id",in="query",description="loantype id to pass",type="integer"),
     *     @SWG\Parameter(name="Language",in="query",description="values will be en,es,nl",type="string"),
     *     @SWG\Parameter(name="user_id",in="query",description="client id",type="string"),
     *     @SWG\Parameter(name="loan_amount",in="query",description="loans amount",type="string"),
     *     @SWG\Response(response=200, description="{""data"":{{""loan_applications"": ""Loans objects."",""message"":""""}}}"),
     *     @SWG\Response(response=500, description="internal server error")
     * )
     */
    /**
     * @SWG\Get(
     *   path="/client/loan-applications",
     *   summary="Loans listing api",
     *     tags={"loans"},
     *     @SWG\Parameter(name="Authorization",in="header",description="Bearer (token here)",type="string"),
     *     @SWG\Parameter(name="Language",in="header",description="values will be en,es,nl",type="string"),
     *     @SWG\Parameter(name="page",in="query",description="page id to pass",type="integer"),
     *     @SWG\Response(response=200, description="{""data"":{{""loan_applications"": ""Loans objects."",""message"":""""}}}"),
     *     @SWG\Response(response=500, description="internal server error")
     * )
     */
    public function index()
    {
        $data = [];
        $status_code = 200;
        $user = JWTAuth::toUser(request()->header('token'));
        $user = User::find($user->id);
        $country = Country::find($user->country);

        $selection = [
            'loan_applications.id',
            'loan_reasons.title as reason_title',
            'loan_reasons.title_es as reason_title_es',
            'loan_reasons.title_nl as reason_title_nl',
            'loan_applications.amount',
            'loan_applications.loan_status',
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
            'loan_applications.created_at',
            'loan_applications.start_date',
            'loan_applications.end_date',
        ];

        $loans = LoanApplication::select($selection)
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
            })
            ->where('loan_applications.client_id', '=', $user->id)
            ->orderBy('loan_applications.updated_at', 'desc');

        if (request('page') == 0) {
            $loans = $loans->get();
        } else {
            $loans = $loans->simplePaginate(50);
        }

        foreach ($loans as $key => $value) {
            if ($value->loan_status == 3) {
                if (request()->header('Language') == 'es') {
                    $value->decline_reason = $value->loan_decline_reasons_title_es;
                } else if (request()->header('Language') == 'nl') {
                    $value->decline_reason = $value->loan_decline_reasons_title_nl;
                } else {
                    $value->decline_reason = $value->loan_decline_reasons_title;
                }
            } else if ($value->loan_status == 2) {
                if (request()->header('Language') == 'es') {
                    $value->decline_reason = $value->loan_on_hold_reasons_title_es;
                } else if (request()->header('Language') == 'nl') {
                    $value->decline_reason = $value->loan_on_hold_reasons_title_nl;
                } else {
                    $value->decline_reason = $value->loan_on_hold_reasons_title;
                }
            } else {
                $value->decline_reason = '';
            }


            if (request()->header('Language') == 'es') {
                $value->loan_reason_text = $value->reason_title_es;
                $value->loan_type_text = $value->loan_type_title_es;
                $value->status_text = $value->loan_status_title_es;
            } else if (request()->header('Language') == 'nl') {
                $value->loan_reason_text = $value->reason_title_nl;
                $value->loan_type_text = $value->loan_type_title_nl;
                $value->status_text = $value->loan_status_title_nl;
            } else {
                $value->loan_reason_text = $value->reason_title;
                $value->loan_type_text = $value->loan_type_title;
                $value->status_text = $value->loan_status_title;
            }
            $value->status = $value->loan_status;
            unset($value->loan_status);
            unset($value->reason_title);
            unset($value->reason_title_es);
            unset($value->reason_title_nl);
            unset($value->loan_decline_reasons_title_es);
            unset($value->loan_decline_reasons_title_nl);
            unset($value->loan_decline_reasons_title);
            unset($value->loan_on_hold_reasons_title_es);
            unset($value->loan_on_hold_reasons_title_nl);
            unset($value->loan_on_hold_reasons_title);
            unset($value->loan_type_title_es);
            unset($value->loan_type_title_nl);
            unset($value->loan_type_title);
            unset($value->loan_status_title_es);
            unset($value->loan_status_title_nl);
            unset($value->loan_status_title);


            $value->id = $value->id != null ? $value->id : '';
            $value->amount = $value->amount != null ? $value->amount : '';
            $value->created_at = $value->created_at != null ? $value->created_at : '';
            $value->start_date = $value->start_date != null ? $value->start_date : '';
            $value->end_date = $value->end_date != null ? $value->end_date : '';
            $value->requested_date = $value->requested_date != null ? $value->requested_date : '';
            $value->decline_reason = $value->decline_reason != null ? $value->decline_reason : '';
            $value->loan_reason_text = $value->loan_reason_text != null ? $value->loan_reason_text : '';
            $value->loan_type_text = $value->loan_type_text != null ? $value->loan_type_text : '';
            $value->status = $value->status != null ? $value->status : '';
        }
        if (request('page') == 0) {
            $data['data']['loan_applications']['data'] = $loans;
        } else {
            $data['data']['loan_applications'] = $loans;
        }
        $data['data']['message'] = '';

        return Api::ApiResponse($data, $status_code);
    }


    /**
     * @SWG\Get(
     *   path="/client/loan-applications/create",
     *   summary="applying for new loan related data",
     *   tags={"loans"},
     *     @SWG\Parameter(name="Authorization",in="header",description="Bearer (token here)",type="string"),
     *     @SWG\Parameter(name="Language",in="header",description="values will be en,es,nl",type="string"),
     *     @SWG\Response(response=200, description="{""data"":{{""country"": ""User country object."",""reasons"": ""Loans reasons objects."",""other_loan_types"": ""Existing loan types objects."",""types"": ""Loan types objects."",""message"":""""}}}"),
     *     @SWG\Response(response=500, description="internal server error")
     * )
     */
    public function create()
    {
        $data = [];
        $status_code = 200;
        $user = JWTAuth::toUser(request()->header('token'));
        $user = User::find($user->id);
        $country_selection = [
            'id',
            'name',
            'tax',
            'tax_percentage',
        ];
        $data['data']['country'] = Country::select($country_selection)->where('id', '=', $user->country)->first();

        $lang = '';
        if (request()->header('Language') == 'es') {
            $lang = 'esp';
        } else if (request()->header('Language') == 'nl') {
            $lang = 'pap';
        } else {
            $lang = 'eng';
        }

        $data['data']['reasons'] = LoanReason::getAllReasons($lang);
        $data['data']['other_loan_types'] = ExistingLoanType::getType($lang);
        $loantypes = [];
        if ($user->country != null) {
            $selection = [
                'loan_types.id',
                'loan_types.minimum_loan',
                'loan_types.maximum_loan',
                'loan_types.unit',
                "loan_types.loan_component",
                "loan_types.apr",
                "loan_types.origination_type",
                "loan_types.origination_amount",
                "loan_types.renewal_type",
                "loan_types.renewal_amount",
                "loan_types.debt_type",
                "loan_types.debt_amount",
                "loan_types.debt_collection_type",
                "loan_types.debt_collection_percentage",
                "loan_types.debt_collection_tax_type",
                "loan_types.debt_collection_tax_value",
                "loan_types.debt_tax_type",
                "loan_types.debt_tax_amount",
                "loan_types.number_of_days",
                "loan_types.interest",
                "loan_types.cap_period",
            ];
            $loantypes = LoanType::leftJoin('loan_type_user_statuses', 'loan_type_user_statuses.loan_type_id', '=', 'loan_types.id')
                ->where('loan_types.country_id', '=', $user->country)
                ->where('loan_types.status', '=', 1)
                ->where(function ($query) use ($user) {
                    $query->where('loan_type_user_statuses.user_status_id', '=', 0)
                        ->orWhere('loan_type_user_statuses.user_status_id', '=', $user->status);
                });
            if ($lang == 'esp') {
                $loantypes->orderBy('loan_types.title_es', 'asc');
                $selection[] = 'loan_types.title_es as title';
            } else if ($lang == 'pap') {
                $loantypes->orderBy('loan_types.title_nl', 'asc');
                $selection[] = 'loan_types.title_nl as title';
            } else {
                $loantypes->orderBy('title', 'asc');
                $selection[] = 'loan_types.title';
            }
            $loantypes->select($selection);
            $loantypes->groupBy('loan_types.id');
            $loantypes = $loantypes->get();
        }
        $data['data']['types'] = $loantypes;

        $data['data']['message'] = '';
        return Api::ApiResponse($data, $status_code);
    }

    /**
     * @SWG\Post(
     *   path="/client/loan-applications",
     *   summary="Apply for new loan store related api",
     *   tags={"loans"},
     *     @SWG\Parameter(name="Authorization",in="header",description="Bearer (token here)",type="string"),
     *     @SWG\Parameter(name="Language",in="header",description="values will be en,es,nl",type="string"),
     *
     *     @SWG\Parameter(name="loan_reason",in="query",description="loan reason selection",type="integer"),
     *     @SWG\Parameter(name="loan_type",in="query",description="loan type selection",type="integer"),
     *     @SWG\Parameter(name="amount",in="query",description="amount selection make from getting loan type",type="integer"),
     *
     *     @SWG\Parameter(name="income_type[0]",in="query",description="first income object income_type in:1,2 q for main income 2 for other",type="integer"),
     *     @SWG\Parameter(name="income_amount[0]",in="query",description="first income object income amount",type="number"),
     *     @SWG\Parameter(name="date_of_payment[0]",in="query",description="date of income payment format should be Y-m-d",type="string"),
     *     @SWG\Parameter(name="income_proof_image[0]",in="formData",description="first income object proof file png,gif,jpg,jpeg,doc,docx,pdf",type="file"),
     *
     *     @SWG\Parameter(name="income_type[1]",in="query",description="second income object income_type in:1,2 q for main income 2 for other",type="integer"),
     *     @SWG\Parameter(name="income_amount[1]",in="query",description="second income object income amount",type="number"),
     *     @SWG\Parameter(name="income_proof_image[1]",in="formData",description="second income object proof file png,gif,jpg,jpeg,doc,docx,pdf",type="file"),
     *
     *     @SWG\Parameter(name="expense_type[0]",in="query",description="expence type selection",type="integer"),
     *     @SWG\Parameter(name="other_amount[0]",in="query",description="expence amount",type="number"),
     *
     *     @SWG\Parameter(name="signature",in="formData",description="signature file png,gif,jpg,jpeg",type="file"),
     *
     *     @SWG\Response(response=200, description="{""data"":{{""country"": ""User country object."",""reasons"": ""Loans reasons objects."",""other_loan_types"": ""Existing loan types objects."",""types"": ""Loan types objects."",""message"":""""}}}"),
     *     @SWG\Response(response=500, description="internal server error")
     * )
     */
    public function store()
    {
        if (request()->header('Language') == 'es') {
            $lang = 'esp';
        } else if (request()->header('Language') == 'nl') {
            $lang = 'pap';
        } else {
            $lang = 'eng';
        }
        $data = [];
        $status_code = 200;
        $validator = Validator::make(request()->all(), [
            'loan_reason'          => 'required|exists:loan_reasons,id',
            'loan_type'            => 'required|exists:loan_types,id',
            'amount'               => 'required|numeric',
            'signature'            => 'required|mimes:png,gif,jpg,jpeg',
            'income_type.*'        => 'required|in:1,2',
            'income_amount.*'      => 'required|numeric',
            'date_of_payment.0'    => 'required|date_format:Y-m-d',
            'income_proof_image.*' => 'required|mimes:png,gif,jpg,jpeg,doc,docx,pdf',
            'expense_type.*'       => 'required|exists:existing_loan_types,id',
            'other_amount.*'       => 'required|numeric',
        ]);

        if ($validator->fails()) {
            $status_code = 422;
            $data['data']['message'] = __('api.validation_error_occurred', [], request('lang'));
            $data['data']['errors'] = $validator->errors();
        } else {
            $user = JWTAuth::toUser(request()->header('token'));
            $user = User::find($user->id);

            if (LoanApplication::hasActiveLoan($user)) {
                $data = [];
                $data['data']['message'] = __('api.validation_error_occurred', [], request('lang'));
                $data['data']['errors']['client_id'] = [
                    Lang::get('validation.active_loan', [], $lang)
                ];
                return response($data, 422);
            }

            $inputs = request()->only([
                'loan_reason',
                'loan_type',
                'amount',
            ]);
            $inputs['client_id'] = $user->id;

            $loan_type = LoanType::find($inputs['loan_type']);

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
                'tax_name'       => $country->tax
            ];

            $salary = array_sum(request('income_amount'));
            $other_loan_deduction = 0;
            if (request('other_amount')) {
                $other_loan_deduction = array_sum(request('other_amount'));
            }

            $max_amount = round((($salary - $other_loan_deduction) * $loan_type->loan_component) / 100, 2);

            $deadline_date = request('date_of_payment')[0];

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
                'loan_status'          => 1,
                'deadline_date'        => $deadline_date,
            ];

            $loan = LoanApplication::create($inputs);
            LoanApplication::addLoanStatusHistory($loan->id, '1', null, $user);
            if (request()->hasFile('signature')) {
                $signature = request()->file('signature');
                $name = str_slug(pathinfo($signature->getClientOriginalName(), PATHINFO_FILENAME), '_');
                $ext = pathinfo($signature->getClientOriginalName(), PATHINFO_EXTENSION);
                $imageName = time() . '_' . base64_encode(rand(1, 100)) . '_' . $name . '.' . $ext;
                $signature->move(public_path('uploads'), $imageName);
                $loan->update([
                    'signature' => $imageName,
                ]);
                $loan->update([
                    'signature_pdf' => $loan->generateSignaturePdf($country->timezone)
                ]);
            }
            if (request('income_amount')) {
                foreach (request('income_amount') as $key => $value) {
                    $loanProofId = null;
                    if (request()->hasFile('income_proof_image.' . $key)) {
                        $image = request()->file('income_proof_image.' . $key);
                        $name = str_slug(pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME), '_');
                        $ext = pathinfo($image->getClientOriginalName(), PATHINFO_EXTENSION);
                        $imageName = time() . '_' . base64_encode(rand(1, 100)) . '_' . $name . '.' . $ext;
                        $image->move(public_path('storage/loan_applications/' . $loan->id), $imageName);

                        $loanProof = LoanProof::create([
                            'file_name' => $imageName,
                        ]);
                        $loanProofId = $loanProof->id;
                    }
                    $date = null;
                    if (request('date_of_payment.' . $key) != '') {
                        $date = request('date_of_payment.' . $key);
                    }

                    LoanAmounts::create([
                        'loan_id'       => $loan->id,
                        'attachment_id' => $loanProofId,
                        'type'          => '1',
                        'amount'        => request('income_amount.' . $key),
                        'amount_type'   => request('income_type.' . $key),
                        'date'          => $date,
                    ]);
                }
            }
            if (request('other_amount')) {
                foreach (request()->other_amount as $key => $value) {
                    LoanAmounts::create([
                        'loan_id'     => $loan->id,
                        'type'        => '2',
                        'amount'      => request('other_amount.' . $key),
                        'amount_type' => request('expense_type.' . $key),
                    ]);
                }
            }

            $data['data']['message'] = __('api.submitted_successfully', ['name' => __('api.loan_application', [], request('lang'))], request('lang'));
        }
        return Api::ApiResponse($data, $status_code);
    }

    // **
    //  * SWG\Post(
    //  *   path="/client/loan-types/{id}",
    //  *   summary="loan type information passing",
    //  *   tags={"loans"},
    //  *     SWG\Parameter(name="Authorization",in="header",description="Bearer (token here)",type="string"),
    //  *     SWG\Parameter(name="Language",in="header",description="values will be en,es,nl",type="string"),
    //  *     SWG\Parameter(parameter="id_in_path", name="id", type="integer", in="path"),
    //  *     SWG\Response(response=200, description="{""data"":{{""loan_type"": ""loan type object."",""message"":""""}}}"),
    //  *     SWG\Response(response=500, description="internal server error")
    //  * )
    //  *

    // public function loanTypeInfo($id)
    // {
    //     $data = [];

    //     $status_code = 200;

    //     $selection = [
    //         "id",
    //         "minimum_loan",
    //         "maximum_loan",
    //         "unit",
    //         "loan_component",
    //         "apr",
    //         "origination_type",
    //         "origination_amount",
    //         "renewal_type",
    //         "renewal_amount",
    //         "debt_type",
    //         "debt_amount",
    //         "debt_collection_percentage",
    //         "debt_tax_type",
    //         "debt_tax_amount",
    //         "number_of_days",
    //         "interest",
    //         "cap_period",
    //         "country_id",
    //         "status",
    //     ];

    //     if (request()->header('Language') == 'es') {
    //         $selection[] = "title_es as title";
    //         $selection[] = 'loan_agreement_esp as loan_agreement';
    //     } elseif (request()->header('Language') == 'nl') {
    //         $selection[] = "title_nl as title";
    //         $selection[] = 'loan_agreement_pap as loan_agreement';
    //     } else {
    //         $selection[] = "title";
    //         $selection[] = 'loan_agreement_eng as loan_agreement';
    //     }

    //     $loan_type = LoanType::select($selection)
    //         ->where('id', '=', $id)
    //         ->first();

    //     $data['data']['loan_type'] = $loan_type;

    //     $data['data']['message'] = '';

    //     return Api::ApiResponse($data, $status_code);
    // }


    /**
     * @SWG\Get(
     *   path="/client/loan-applications/{loan_application_id}",
     *   summary="loan application data showing api",
     *   tags={"loans"},
     *     @SWG\Parameter(name="Authorization",in="header",description="Bearer (token here)",type="string"),
     *     @SWG\Parameter(name="Language",in="header",description="values will be en,es,nl",type="string"),
     *     @SWG\Parameter(parameter="id_in_path", name="loan_application_id", type="integer", in="path"),
     *     @SWG\Response(response=200, description="{""data"":{{""loan_application"": ""loan application related data."",""message"":""""}}}"),
     *     @SWG\Response(response=500, description="internal server error")
     * )
     */
    public function show($id)
    {
        $data = [];
        $status_code = 200;
        $user = JWTAuth::toUser(request()->header('token'));
        $user = User::find($user->id);

        $country = Country::find($user->country);

        $selection = [
            "loan_applications.id",
            "loan_applications.loan_reason",
            "loan_applications.loan_type",
            "loan_applications.loan_status",
            "loan_applications.deadline_date",
            "loan_applications.amount",
            "loan_applications.max_amount",
            "loan_applications.salary",
            "loan_applications.origination_type",
            "loan_applications.origination_amount",
            "loan_applications.origination_fee",
            "loan_applications.tax",
            "loan_applications.tax_name",
            "loan_applications.tax_percentage",
            "loan_applications.interest",
            "loan_applications.interest_amount",
            "loan_applications.start_date",
            "loan_applications.end_date",
            "loan_applications.created_at",
            "loan_applications.signature_pdf",
            "loan_applications.decline_description",
            "loan_applications.other_loan_deduction",
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


        $loan = LoanApplication::select($selection)
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
            })
            ->where('loan_applications.id', '=', $id)
            ->where('loan_applications.client_id', '=', $user->id)
            ->first();

        $loan->signature_pdf = asset('uploads/' . $loan->signature_pdf);

        $loan->decline_reason = '';
        $loan->status = $loan->loan_status;
        if (request()->header('Language') == 'es') {
            $loan->loan_reason_text = $loan->reason_title_es;
            $loan->loan_type_text = $loan->loan_type_title_es;
            $loan->status_text = $loan->loan_status_title_es;
            if ($loan->loan_status == 3) {
                $loan->decline_reason = $loan->loan_decline_reasons_title_es;
            } else if ($loan->loan_status == 2) {
                $loan->decline_reason = $loan->loan_on_hold_reasons_title_es;
            }
            $lang = 'esp';
        } else if (request()->header('Language') == 'nl') {
            $loan->loan_reason_text = $loan->reason_title_nl;
            $loan->loan_type_text = $loan->loan_type_title_nl;
            $loan->status_text = $loan->loan_status_title_nl;
            if ($loan->loan_status == 3) {
                $loan->decline_reason = $loan->loan_decline_reasons_title_nl;
            } else if ($loan->loan_status == 2) {
                $loan->decline_reason = $loan->loan_on_hold_reasons_title_nl;
            }
            $lang = 'pap';
        } else {
            $loan->loan_reason_text = $loan->reason_title;
            $loan->loan_type_text = $loan->loan_type_title;
            $loan->status_text = $loan->loan_status_title;
            if ($loan->loan_status == 3) {
                $loan->decline_reason = $loan->loan_decline_reasons_title;
            } else if ($loan->loan_status == 2) {
                $loan->decline_reason = $loan->loan_on_hold_reasons_title;
            }
            $lang = 'eng';
        }
        App::setLocale($lang);
        if ($loan->loan_reason_text == null) {
            $loan->loan_reason_text = '';
        }
        if ($loan->loan_type_text == null) {
            $loan->loan_type_text = '';
        }
        if ($loan->status_text == null) {
            $loan->status_text = '';
        }
        if ($loan->decline_description == null) {
            $loan->decline_description = '';
        }

        if ($loan->start_date == null) {
            $loan->start_date = '';
        }

        if ($loan->end_date == null) {
            $loan->end_date = '';
        }
        $loan->requested_date = $loan->requested_date != null ? $loan->requested_date : '';
        $loan->tax_on_interest = round($loan->interest_amount * $loan->tax_percentage / 100, 2);
        $loan->credit_amount = round($loan->amount - $loan->origination_fee - $loan->tax - $loan->interest_amount - $loan->tax_on_interest, 2);

        unset($loan->reason_title);
        unset($loan->reason_title_es);
        unset($loan->reason_title_nl);
        unset($loan->loan_type_title);
        unset($loan->loan_type_title_es);
        unset($loan->loan_type_title_nl);
        unset($loan->loan_status);
        unset($loan->loan_status_title);
        unset($loan->loan_status_title_es);
        unset($loan->loan_status_title_nl);
        unset($loan->loan_decline_reasons_title);
        unset($loan->loan_decline_reasons_title_es);
        unset($loan->loan_decline_reasons_title_nl);
        unset($loan->loan_on_hold_reasons_title);
        unset($loan->loan_on_hold_reasons_title_es);
        unset($loan->loan_on_hold_reasons_title_nl);

        $selection = [
            'loan_amounts.id',
            'loan_amounts.type',
            'loan_amounts.loan_id',
            'loan_amounts.amount_type',
            'loan_amounts.amount',
            'loan_amounts.date',
            'loan_proofs.file_name',
            'existing_loan_types.title',
            'existing_loan_types.title_es',
            'existing_loan_types.title_nl'
        ];
        $amounts = LoanAmounts::select($selection)
            ->leftJoin('loan_proofs', 'loan_proofs.id', '=', 'loan_amounts.attachment_id')
            ->leftJoin('existing_loan_types', 'existing_loan_types.id', '=', 'loan_amounts.amount_type')
            ->where('loan_amounts.loan_id', '=', $loan->id)
            ->get();

        $income_amounts = $amounts->where('type', '=', 1)->map(function ($item, $key) {
            if ($item->amount_type == 1) {
                $item->income_type_text = __('keywords.Gross salary');
            } else if ($item->amount_type == 2) {
                $item->income_type_text = __('keywords.Other Income');
            }
            if ($item->file_name != null) {
                $item->income_proof_image = asset('storage/loan_applications/' . $item->loan_id . '/' . $item->file_name);
            } else {
                $item->income_proof_image = '';
            }
            if ($item->date == null) {
                $item->date = '';
            }
            $item->income_id = $item->id;
            $item->income_type = $item->amount_type;
            $item->income_amount = $item->amount;
            $item->date_of_payment = $item->date;

            unset($item->id);
            unset($item->amount_type);
            unset($item->date);
            unset($item->amount);
            unset($item->file_name);
            unset($item->attachment_id);
            unset($item->type);
            unset($item->created_at);
            unset($item->loan_id);
            unset($item->updated_at);
            unset($item->title);
            unset($item->title_es);
            unset($item->title_nl);
            return $item;
        })->flatten();

        $loan->income_amount = $income_amounts;

        $expense_amounts = $amounts->where('type', '=', 2)->map(function ($item, $key) {
            if (request()->header('Language') == 'es') {
                $item->expense_type_text = $item->title_es;
            } else if (request()->header('Language') == 'nl') {
                $item->expense_type_text = $item->title_nl;
            } else {
                $item->expense_type_text = $item->title;
            }
            $item->expense_id = $item->id;
            $item->expense_type = $item->amount_type;
            $item->other_amount = $item->amount;

            unset($item->id);
            unset($item->amount_type);
            unset($item->amount);
            unset($item->attachment_id);
            unset($item->type);
            unset($item->created_at);
            unset($item->loan_id);
            unset($item->updated_at);
            unset($item->date);
            unset($item->file_name);
            unset($item->title);
            unset($item->title_es);
            unset($item->title_nl);
            return $item;
        })->flatten();
        $loan->expense_amount = $expense_amounts;

        $data['data']['loan_application'] = $loan;

        $data['data']['message'] = '';
        return Api::ApiResponse($data, $status_code);
    }

    // **
    //  * SWG\Get(
    //  *   path="/client/loan-applications/{loan_application_id}/edit",
    //  *   summary="edit related for data",
    //  *   tags={"loans"},
    //  *     SWG\Parameter(name="Authorization",in="header",description="Bearer (token here)",type="string"),
    //  *     SWG\Parameter(name="Language",in="header",description="values will be en,es,nl",type="string"),
    //  *     SWG\Parameter(parameter="id_in_path", name="loan_application_id", type="integer", in="path"),
    //  *     SWG\Response(response=200, description="{""data"":{{""country"": ""User country object."",""reasons"": ""Loans reasons objects."",""other_loan_types"": ""Existing loan types objects."",""types"": ""Loan types objects."",""loan_application"":""loan application related object"",""message"":""""}}}"),
    //  *     SWG\Response(response=500, description="internal server error")
    //  * )
    //  *
    // public function edit($id)
    // {
    //     $data = [];
    //     $status_code = 200;
    //     $user = JWTAuth::toUser(request()->header('token'));
    //     $user = User::find($user->id);

    //     $selection = [
    //         "id",
    //         "name",
    //         "tax",
    //         "tax_percentage",
    //     ];

    //     $data['data']['country'] = Country::select($selection)
    //         ->where('id', '=', $user->country)
    //         ->first();

    //     $lang = '';
    //     if (request()->header('Language') == 'es') {
    //         $lang = 'esp';
    //     } elseif (request()->header('Language') == 'nl') {
    //         $lang = 'pap';
    //     } else {
    //         $lang = 'eng';
    //     }

    //     $data['data']['reasons'] = LoanReason::getAllReasons($lang);
    //     $data['data']['other_loan_types'] = ExistingLoanType::getType($lang);
    //     $data['data']['types'] = LoanType::activeLoanTypesViaUserId($user, $lang);


    //     $selection = [
    //         "loan_applications.id",
    //         "loan_applications.loan_reason",
    //         "loan_applications.loan_type",
    //         "loan_applications.amount",
    //     ];

    //     $loan = LoanApplication::select($selection)
    //         ->where('loan_applications.id', '=', $id)
    //         ->where('client_id', '=', $user->id)
    //         ->whereIn('loan_applications.status',[1,2])
    //         ->first();
    //     if($loan==null){
    //         abort(404);
    //     }
    //     $selection = [
    //         'loan_amounts.id',
    //         'loan_amounts.type',
    //         'loan_amounts.amount_type',
    //         'loan_amounts.amount',
    //         'loan_amounts.date',
    //         'loan_proofs.file_name',
    //     ];
    //     $amounts = LoanAmounts::select($selection)
    //         ->leftJoin('loan_proofs', 'loan_proofs.id', '=', 'loan_amounts.attachment_id')
    //         ->where('loan_amounts.loan_id', '=', $loan->id)
    //         ->get();

    //     $income_amounts = $amounts->where('type', '=', 1)->map(function ($item, $key) {
    //         if ($item->file_name != null) {
    //             $item->income_proof_image = asset('storage/loan_applications/' . $item->loan_id . '/' . $item->file_name);
    //         } else {
    //             $item->income_proof_image = '';
    //         }
    //         if ($item->date == null) {
    //             $item->date = '';
    //         }
    //         $item->income_id = $item->id;
    //         $item->income_type = $item->amount_type;
    //         $item->income_amount = $item->amount;
    //         $item->date_of_payment = $item->date;
    //         unset($item->id);
    //         unset($item->amount_type);
    //         unset($item->date);
    //         unset($item->amount);
    //         unset($item->attachment_id);
    //         unset($item->file_name);
    //         unset($item->type);
    //         unset($item->created_at);
    //         unset($item->loan_id);
    //         unset($item->updated_at);
    //         return $item;
    //     })->flatten();

    //     $loan->income_amount = $income_amounts;

    //     $expense_amounts = $amounts->where('type', '=', 2)->map(function ($item, $key) {
    //         $item->expense_id = $item->id;
    //         $item->expense_type = $item->amount_type;
    //         $item->other_amount = $item->amount;
    //         unset($item->id);
    //         unset($item->amount_type);
    //         unset($item->amount);
    //         unset($item->attachment_id);
    //         unset($item->type);
    //         unset($item->created_at);
    //         unset($item->loan_id);
    //         unset($item->updated_at);
    //         unset($item->date);
    //         unset($item->file_name);
    //         return $item;
    //     })->flatten();
    //     $loan->expense_amount = $expense_amounts;

    //     $data['data']['loan_application'] = $loan;

    //     $data['data']['message'] = '';
    //     return Api::ApiResponse($data, $status_code);
    // }

    /**
     * @SWG\Put(
     *   path="/client/loan-applications/{loan_application_id}",
     *   summary="updating data related for data",
     *   tags={"loans"},
     *     @SWG\Parameter(name="Authorization",in="header",description="Bearer (token here)",type="string"),
     *     @SWG\Parameter(name="Language",in="header",description="values will be en,es,nl",type="string"),
     *     @SWG\Parameter(parameter="id_in_path", name="loan_application_id", type="integer", in="path"),
     *
     *     @SWG\Parameter(name="loan_reason",in="query",description="loan reason selection",type="integer"),
     *     @SWG\Parameter(name="loan_type",in="query",description="loan type selection",type="integer"),
     *     @SWG\Parameter(name="amount",in="query",description="amount selection make from getting loan type",type="integer"),
     *
     *     @SWG\Parameter(name="income_id[0]",in="query",description="first income object id for update",type="integer"),
     *     @SWG\Parameter(name="income_type[0]",in="query",description="first income object income_type in:1,2 q for main income 2 for other",type="integer"),
     *     @SWG\Parameter(name="income_amount[0]",in="query",description="first income object income amount",type="number"),
     *     @SWG\Parameter(name="date_of_payment[0]",in="query",description="date of income payment format should be Y-m-d",type="string"),
     *     @SWG\Parameter(name="income_proof_image[0]",in="formData",description="first income object proof file png,gif,jpg,jpeg,doc,docx,pdf",type="file"),
     *
     *     @SWG\Parameter(name="income_id[1]",in="query",description="second income object id for update",type="integer"),*
     *     @SWG\Parameter(name="income_type[1]",in="query",description="second income object income_type in:1,2 q for main income 2 for other",type="integer"),
     *     @SWG\Parameter(name="income_amount[1]",in="query",description="second income object income amount",type="number"),
     *     @SWG\Parameter(name="income_proof_image[1]",in="formData",description="second income object proof file png,gif,jpg,jpeg,doc,docx,pdf",type="file"),
     *
     *     @SWG\Parameter(name="expense_id[0]",in="query",description="expence id for update",type="integer"),*
     *     @SWG\Parameter(name="expense_type[0]",in="query",description="expence type selection",type="integer"),
     *     @SWG\Parameter(name="other_amount[0]",in="query",description="expence amount",type="number"),
     *
     *     @SWG\Parameter(name="signature",in="formData",description="signature file png,gif,jpg,jpeg",type="file"),
     *
     *     @SWG\Response(response=200, description="{""data"":{{""country"": ""User country object."",""reasons"": ""Loans reasons objects."",""other_loan_types"": ""Existing loan types objects."",""types"": ""Loan types objects."",""loan_application"":""loan application related object"",""message"":""""}}}"),
     *     @SWG\Response(response=500, description="internal server error")
     * )
     */
    public function update($id)
    {
        $data = [];
        $status_code = 200;

        $loan = LoanApplication::where('id', '=', $id)
            ->whereIn('loan_status', [1, 2])
            ->first();

        if ($loan == null) {
            abort(404);
        }

        $validator = Validator::make(request()->all(), [
            'loan_reason'          => 'required|exists:loan_reasons,id',
            'loan_type'            => 'required|exists:loan_types,id',
            'amount'               => 'required|numeric',
            'signature'            => 'nullable|mimes:png,gif,jpg,jpeg',
            'income_id.*'          => 'nullable|exists:loan_amounts,id,type,1',
            'income_type.*'        => 'required|in:1,2',
            'income_amount.*'      => 'required|numeric',
            'date_of_payment.0'    => 'required|date_format:Y-m-d',
            'income_proof_image.*' => 'nullable|mimes:png,gif,jpg,jpeg,doc,docx,pdf',
            'expense_id.*'         => 'nullable|exists:loan_amounts,id,type,2',
            'expense_type.*'       => 'required|exists:existing_loan_types,id',
            'other_amount.*'       => 'required|numeric',
        ]);

        if ($validator->fails()) {
            $status_code = 422;
            $data['data']['message'] = __('api.validation_error_occurred', [], request('lang'));
            $data['data']['errors'] = $validator->errors();
        } else {

            $user = JWTAuth::toUser(request()->header('token'));
            $user = User::find($user->id);

            $inputs = request()->only([
                'loan_reason',
                'loan_type',
                'amount',
            ]);
            $inputs['client_id'] = $user->id;

            $loan_type = LoanType::find($inputs['loan_type']);

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
                'tax_name'       => $country->tax
            ];
            $salary = array_sum(request('income_amount'));
            $other_loan_deduction = 0;
            if (request('other_amount')) {
                $other_loan_deduction = array_sum(request('other_amount'));
            }

            $max_amount = round((($salary - $other_loan_deduction) * $loan_type->loan_component) / 100, 2);

            $deadline_date = request('date_of_payment')[0];

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
                'loan_status'          => 1
            ];

            $loan->update($inputs);
            if (request()->hasFile('signature')) {
                $signature = request()->file('signature');
                $name = str_slug(pathinfo($signature->getClientOriginalName(), PATHINFO_FILENAME), '_');
                $ext = pathinfo($signature->getClientOriginalName(), PATHINFO_EXTENSION);
                $imageName = time() . '_' . base64_encode(rand(1, 100)) . '_' . $name . '.' . $ext;
                $signature->move(public_path('uploads'), $imageName);
                $loan->update([
                    'signature' => $imageName,
                ]);
                $loan->update([
                    'signature_pdf' => $loan->generateSignaturePdf($country->timezone)
                ]);
            }
            if (request('income_amount')) {
                LoanAmounts::where('type', '=', 1)->where('loan_id', '=', $loan->id)->whereNotIn('id', request('income_id'))->delete();
                foreach (request('income_amount') as $key => $value) {
                    $loan_amount = null;
                    if (request('income_id.' . $key) != '') {
                        $loan_amount = LoanAmounts::where('id', '=', request('income_id.' . $key))
                            ->where('loan_id', '=', $loan->id)
                            ->first();
                    }
                    $loanProofId = null;
                    if ($loan_amount != null) {
                        $loanProofId = $loan_amount->attachment_id;
                    }
                    if (request()->hasFile('income_proof_image.' . $key)) {
                        $image = request()->file('income_proof_image.' . $key);
                        $name = str_slug(pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME), '_');
                        $ext = pathinfo($image->getClientOriginalName(), PATHINFO_EXTENSION);
                        $imageName = time() . '_' . base64_encode(rand(1, 100)) . '_' . $name . '.' . $ext;
                        $image->move(public_path('storage/loan_applications/' . $loan->id), $imageName);

                        $loanProof = LoanProof::create([
                            'file_name' => $imageName,
                        ]);
                        $loanProofId = $loanProof->id;
                    }
                    $date = null;
                    if (request('date_of_payment.' . $key) != '') {
                        $date = request('date_of_payment.' . $key);
                    }
                    if ($loan_amount != null) {
                        $loan_amount->update([
                            'attachment_id' => $loanProofId,
                            'amount'        => request('income_amount.' . $key),
                            'amount_type'   => request('income_type.' . $key),
                            'date'          => $date,
                        ]);
                    } else {
                        LoanAmounts::create([
                            'loan_id'       => $loan->id,
                            'attachment_id' => $loanProofId,
                            'type'          => '1',
                            'amount'        => request('income_amount.' . $key),
                            'amount_type'   => request('income_type.' . $key),
                            'date'          => $date,
                        ]);
                    }
                }
            }
            if (request('other_amount')) {
                $exprese_id = [];

                if (request('expense_id')) {
                    $exprese_id = request('expense_id');
                }

                LoanAmounts::where('type', '=', 2)->where('loan_id', '=', $loan->id)->whereNotIn('id', $exprese_id)->delete();
                foreach (request()->other_amount as $key => $value) {
                    if (request('expense_id.' . $key)) {
                        LoanAmounts::where('id', '=', request('expense_id.' . $key))
                            ->where('loan_id', '=', $loan->id)
                            ->update([
                                'amount'      => request('other_amount.' . $key),
                                'amount_type' => request('expense_type.' . $key),
                            ]);
                    } else {
                        LoanAmounts::create([
                            'loan_id'     => $loan->id,
                            'type'        => '2',
                            'amount'      => request('other_amount.' . $key),
                            'amount_type' => request('expense_type.' . $key),
                        ]);
                    }
                }
            } else {
                LoanAmounts::where('type', '=', 2)->where('loan_id', '=', $loan->id)->delete();
            }

            $data['data']['message'] = __('api.updated_successfully', ['name' => __('api.loan_application', [], request('lang'))], request('lang'));
        }
        return Api::ApiResponse($data, $status_code);
    }

    /**
     * @SWG\Delete(
     *   path="/client/loan-applications/{loan_application_id}",
     *   summary="Loans delete api only if loan is in status requested, approved, rejected, onhold",
     *     tags={"loans"},
     *     @SWG\Parameter(name="Authorization",in="header",description="Bearer (token here)",type="string"),
     *     @SWG\Parameter(name="Language",in="header",description="values will be en,es,nl",type="string"),
     *     @SWG\Parameter(parameter="id_in_path", name="loan_application_id", type="integer", in="path"),
     *
     *     @SWG\Response(response=200, description="{""data"":{{""message"":""Loan Application successfully deleted.""}}}"),
     *     @SWG\Response(response=401, description="{""data"":{{""message"":""Loan Application not found.""}}},{""data"":{{""message"":""Something went wrong.""}}}"),
     *     @SWG\Response(response=500, description="internal server error")
     * )
     */
    public function destroy($loan_application)
    {
        $data = [];
        $status_code = 200;
        $user = JWTAuth::toUser(request()->header('token'));

        $loan_application = LoanApplication::where('id', '=', $loan_application)
            ->where('client_id', '=', $user->id)
            ->whereIn('loan_status', [1, 2])
            ->first();

        if ($loan_application != null) {
            $loan_application->update([
                'deleted_by' => $user->id
            ]);
            if (!$loan_application->delete()) {
                $data['data']['message'] = __('api.something_went_wrong', [], request('lang'));
            } else {
                $data['data']['message'] = __('api.deleted_successfully', ['name' => __('api.loan_application', [], request('lang'))], request('lang'));
            }
        } else {
            $status_code = 401;
            $data['data']['message'] = __('api.not_found', ['name' => __('api.loan_application', [], request('lang'))], request('lang'));
        }


        return Api::ApiResponse($data, $status_code);
    }
}
