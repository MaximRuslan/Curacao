<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Http\Controllers\Controller;
use App\Library\Api;
use App\Models\Bank;
use App\Models\Branch;
use App\Models\Country;
use App\Models\Credit;
use App\Models\User;
use App\Models\UserBank;
use App\Models\Wallet;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class CreditController extends Controller
{
    /**
     * @SWG\Get(
     *   path="/client/credits",
     *   summary="Credits listing api",
     *     tags={"credits"},
     *     @SWG\Parameter(name="Authorization",in="header",description="Bearer (token here)",type="string"),
     *     @SWG\Parameter(name="Language",in="header",description="values will be en,es,nl",type="string"),
     *     @SWG\Parameter(name="page",in="query",description="page id to pass",type="integer"),
     *     @SWG\Response(response=200, description="{""data"":{{""credits"": ""credits objects.""}}}"),
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
            'credits.id',
            'credits.payment_type',
            'credits.amount',
            'credits.notes',
            'credits.branch_id',
            'credits.status',
            'credits.created_at',
            'credits.transaction_charge',
            'credits.bank_id',
            'banks.name as bank_id_text',
        ];

        if (request()->header('Language') == 'es') {
            $selection[] = 'branches.title_es as branch_name';
        } else if (request()->header('Language') == 'nl') {
            $selection[] = 'branches.title_nl as branch_name';
        } else {
            $selection[] = 'branches.title as branch_name';
        }

        $credits = Credit::select($selection)
            ->leftJoin('user_banks', 'user_banks.id', '=', 'credits.bank_id')
            ->leftJoin('banks', 'banks.id', '=', 'user_banks.bank_id')
            ->leftJoin('branches', 'branches.id', '=', 'credits.branch_id')
            ->where('credits.user_id', '=', $user->id)
            ->orderBy('credits.updated_at', 'desc');

        if (request('page') == 0) {
            $credits = $credits->get();
        } else {
            $credits = $credits->simplePaginate(50);
        }

        if (request()->header('Language') == 'es') {
            App::setLocale('esp');
        } else if (request()->header('Language') == 'nl') {
            App::setLocale('pap');
        } else {
            App::setLocale('eng');
        }
        $keywords = Lang::get('keywords');

        foreach ($credits as $key => $value) {
            $value->payment_type_text = config('site.credit_payment_types.' . $value->payment_type);
            $value->payment_type_text = $keywords[$value->payment_type_text];
            $status = '';
            if ($value->status == 1) {
                $status = 'Requested';
            } else if ($value->status == 2) {
                if ($value->payment_type == 2) {
                    $status = 'In process';
                } else if ($value->payment_type == 1) {
                    $status = 'Approved';
                }
            } else if ($value->status == 3) {
                $status = 'Completed';
            } else if ($value->status == 4) {
                $status = 'Rejected';
            }
            if ($status != '') {
                $value->status_text = $keywords[$status];
            }

            $value->branch_id_text = $value->branch_name;
            unset($value->branch_name);


            $value->id = $value->id != null ? $value->id : '';
            $value->payment_type = $value->payment_type != null ? $value->payment_type : '';
            $value->amount = $value->amount != null ? $value->amount : '';
            $value->notes = $value->notes != null ? $value->notes : '';
            $value->status = $value->status != null ? $value->status : '';
            $value->transaction_charge = $value->transaction_charge != null ? $value->transaction_charge : '';
            $value->created_at = $value->created_at != null ? $value->created_at : '';
            $value->bank_id = $value->bank_id != null ? $value->bank_id : -1;
            $value->bank_id_text = $value->bank_id_text != null ? $value->bank_id_text : '';
            $value->payment_type_text = $value->payment_type_text != null ? $value->payment_type_text : '';
            $value->branch_id = $value->branch_id != null ? $value->branch_id : -1;
            $value->branch_id_text = $value->branch_id_text != null ? $value->branch_id_text : '';
        }

        if (request('page') == 0) {
            $data['data']['credits']['data'] = $credits;
        } else {
            $data['data']['credits'] = $credits;
        }

        $data['data']['message'] = "";

        return Api::ApiResponse($data, $status_code);
    }

    /**
     * @SWG\Get(
     *   path="/client/credits/create",
     *   summary="Create needed dropdown api",
     *     tags={"credits"},
     *     @SWG\Parameter(name="Authorization",in="header",description="Bearer (token here)",type="string"),
     *     @SWG\Parameter(name="Language",in="header",description="values will be en,es,nl",type="string"),
     *     @SWG\Response(response=200, description="{""data"":{{""wallet"": ""User Wallet amount."",""available_balance"":""available amount"",""terms"":""user country terms language wise"",""branches"":""branches dropdown"",""map_link"":""country map link"",""message"":""""}}}"),
     *     @SWG\Response(response=500, description="internal server error")
     * )
     */

    public function create()
    {
        $data = [];
        $status_code = 200;
        $user = JWTAuth::toUser(request()->header('token'));
        $wallet = Wallet::getUserWalletAmount($user->id);
        $data['data']['wallet'] = round($wallet, 2);
        $get_hold_balance = $user->getHoldBalance();
        $data['data']['available_balance'] = round($wallet - $get_hold_balance, 2);
        $country = Country::find($user->country);
        if ($country != null) {
            $data['data']['map_link'] = $country->map_link;
        } else {
            $data['data']['map_link'] = '';
        }
        $selection = [];
        if (request()->header('Language') == 'es') {
            $selection = ['title_es as title', 'id'];
            $data['data']['terms'] = $country->terms_esp;
        } else if (request()->header('Language') == 'nl') {
            $selection = ['title_nl as title', 'id'];
            $data['data']['terms'] = $country->terms_pap;
        } else {
            $selection = ['title', 'id'];
            $data['data']['terms'] = $country->terms_eng;
        }
        $data['data']['branches'] = Branch::select($selection)
            ->where('country_id', '=', $user->country)
            ->get();
        $data['data']['banks'] = UserBank::select(DB::raw('concat(banks.name,"-",user_banks.account_number) as name'), 'user_banks.id', 'banks.transaction_fee_type', 'banks.transaction_fee', 'banks.tax_transaction')
            ->leftJoin('banks', 'banks.id', '=', 'user_banks.bank_id')
            ->where('user_banks.user_id', '=', $user->id)
            ->get();
        $data['data']['message'] = "";
        return Api::ApiResponse($data, $status_code);
    }

    /**
     * @SWG\Post(
     *   path="/client/credits",
     *   summary="Apply for new credit request api",
     *   tags={"credits"},
     *     @SWG\Parameter(name="Authorization",in="header",description="Bearer (token here)",type="string"),
     *
     *     @SWG\Parameter(name="payment_type",in="query",description="""1""=>""cash payout request"",""2""=>""bank request""",type="integer"),
     *     @SWG\Parameter(name="amount",in="query",description="amount of credit request",type="integer"),
     *     @SWG\Parameter(name="bank_id",in="query",description="bank is selection from drop down",type="integer"),
     *     @SWG\Parameter(name="transaction_charge",in="query",description="transaction charge on the bases of bank selection transaction fee amount+tax_transaction",type="number"),
     *     @SWG\Parameter(name="branch_id",in="query",description="branch selection from drop down in case of cash payout request",type="integer"),
     *     @SWG\Parameter(name="notes",in="query",description="notes for request",type="integer"),
     *
     *     @SWG\Parameter(name="terms_accepted",in="query",description="1 if client accepted terms other wise 0",type="integer"),
     *
     *     @SWG\Response(response=200, description="{""data"":{{""message"": ""Credit request submitted successfully.""}}}"),
     *     @SWG\Response(response=500, description="internal server error")
     * )
     */

    public function store()
    {
        $data = [];
        $status_code = 200;
        $user = JWTAuth::toUser(request()->header('token'));
        $inputs = request()->all();
        $inputs['user_id'] = $user->id;
        $validator = Validator::make($inputs, Credit::apiValidationRules($inputs));

        if ($validator->fails()) {
            $status_code = 422;
            $data['data']['message'] = __('api.validation_error_occurred', [], request('lang'));
            $data['data']['errors'] = $validator->errors();
        } else {
            $inputs['transaction_charge'] = 0;
            if ($inputs['payment_type'] == 2) {
                $bank = UserBank::find($inputs['bank_id']);
                if ($bank != null) {
                    $bank = Bank::find($bank->bank_id);
                    if ($bank != null) {
                        if ($bank->transaction_fee_type == 1) {
                            $inputs['transaction_charge'] = ($bank->transaction_fee * $inputs['amount'] / 100) + $bank->tax_transaction;
                        } else if ($bank->transaction_fee_type == 2) {
                            $inputs['transaction_charge'] = $bank->transaction_fee + $bank->tax_transaction;
                        }
                    }
                }
            }
            $inputs['status'] = '1';
            Credit::create($inputs);

            $data['data']['message'] = __('api.submitted_successfully', ['name' => __('api.credit', [], request('lang'))], request('lang'));
        }

        return Api::ApiResponse($data, $status_code);
    }

    /**
     * SWG\Get(
     *   path="/client/credits/{credit_id}",
     *   summary="credits data showing api",
     *   tags={"credits"},
     *     SWG\Parameter(name="Authorization",in="header",description="Bearer (token here)",type="string"),
     *     SWG\Parameter(name="Language",in="header",description="values will be en,es,nl",type="string"),
     *     SWG\Parameter(parameter="id_in_path", name="credit_id", type="integer", in="path"),
     *     SWG\Response(response=200, description="{""data"":{{""credit"": ""credit related data."",""message"":""""}}}"),
     *     SWG\Response(response=500, description="internal server error")
     * )
     */
    /*public function show($id)
    {
        $data = [];
        $status_code = 200;
        $user = JWTAuth::toUser(request()->header('token'));
        $selection = [
            'credits.id',
            'credits.payment_type',
            'credits.amount',
            'credits.notes',
            'credits.status',
            'credits.created_at',
            'credits.bank_id',
            'credits.branch_id',
            'banks.name as bank_id_text',
        ];
        if (request()->header('Language') == 'es') {
            $selection[] = 'branches.title_es as branch_id_text';
        } elseif (request()->header('Language') == 'nl') {
            $selection[] = 'branches.title_nl as branch_id_text';
        } else {
            $selection[] = 'branches.title as branch_id_text';
        }
        $credit = Credit::select($selection)
            ->leftJoin('user_banks', 'user_banks.id', '=', 'credits.bank_id')
            ->leftJoin('banks', 'banks.id', '=', 'user_banks.bank_id')
            ->leftJoin('branches', 'branches.id', '=', 'credits.branch_id')
            ->where('credits.user_id', '=', $user->id)
            ->where('credits.id', '=', $id)
            ->first();

        if (request()->header('Language') == 'es') {
            App::setLocale('esp');
        } elseif (request()->header('Language') == 'nl') {
            App::setLocale('pap');
        } else {
            App::setLocale('eng');
        }
        $keywords = Lang::get('keywords');

        $status = '';
        if ($credit->status == 1) {
            $status = 'Requested';
        } else if ($credit->status == 2) {
            if ($credit->payment_type == 2) {
                $status = 'In process';
            } elseif ($credit->payment_type == 1) {
                $status = 'Approved';
            }
        } else if ($credit->status == 3) {
            $status = 'Completed';
        } else if ($credit->status == 4) {
            $status = 'Rejected';
        }

        $credit->payment_type_text = config('site.credit_payment_types.' . $credit->payment_type);
        $credit->payment_type_text = $keywords[$credit->payment_type_text];

        if ($status != '') {
            $credit->status_text = $keywords[$status];
        }

        $credit->id = $credit->id != null ? $credit->id : '';
        $credit->payment_type = $credit->payment_type != null ? $credit->payment_type : '';
        $credit->payment_type_text = $credit->payment_type_text != null ? $credit->payment_type_text : '';
        $credit->amount = $credit->amount != null ? $credit->amount : '';
        $credit->notes = $credit->notes != null ? $credit->notes : '';
        $credit->status = $credit->status != null ? $credit->status : '';
        $credit->created_at = $credit->created_at != null ? $credit->created_at : '';
        $credit->bank_id = $credit->bank_id != null ? $credit->bank_id : -1;
        $credit->bank_id_text = $credit->bank_id_text != null ? $credit->bank_id_text : '';
        $credit->branch_id = $credit->branch_id != null ? $credit->branch_id : -1;
        $credit->branch_id_text = $credit->branch_id_text != null ? $credit->branch_id_text : '';

        $data['data']['credit'] = $credit;
        $data['data']['message'] = '';
        return Api::ApiResponse($data, $status_code);
    }*/

    /**
     * SWG\Get(
     *   path="/client/credits/{credit_id}/edit",
     *   summary="edit related for data",
     *   tags={"credits"},
     *     SWG\Parameter(name="Authorization",in="header",description="Bearer (token here)",type="string"),
     *     SWG\Parameter(name="Language",in="header",description="values will be en,es,nl",type="string"),
     *     SWG\Parameter(parameter="id_in_path", name="credit_id", type="integer", in="path"),
     *     SWG\Response(response=200, description="{""data"":{{""wallet"": ""User Wallet amount."",""available_balance"":""available amount"",""terms"":""user country terms language wise"",""branches"":""branches dropdown"",""map_link"":""country map link"",""message"":""""}}}"),
     *     SWG\Response(response=500, description="internal server error")
     * )
     */
    /*public function edit($id)
    {
        $data = [];
        $status_code = 200;
        $user = JWTAuth::toUser(request()->header('token'));
        $wallet = Wallet::getUserWalletAmount($user->id);
        $data['data']['wallet'] = number_format($wallet, 2);
        $get_hold_balance = $user->getHoldBalance();
        $data['data']['available_balance'] = number_format($wallet - $get_hold_balance, 2);
        $country = Country::find($user->country);
        if ($country != null) {
            $data['data']['map_link'] = $country->map_link;
        } else {
            $data['data']['map_link'] = '';
        }
        $selection = [];
        if (request()->header('Language') == 'es') {
            $selection = ['title_es as title', 'id'];
            $data['data']['terms'] = $country->terms_esp;
        } elseif (request()->header('Language') == 'nl') {
            $selection = ['title_nl as title', 'id'];
            $data['data']['terms'] = $country->terms_pap;
        } else {
            $selection = ['title', 'id'];
            $data['data']['terms'] = $country->terms_eng;
        }
        $data['data']['branches'] = Branch::select($selection)
            ->where('country_id', '=', $user->country)
            ->get();
        $data['data']['banks'] = UserBank::select(DB::raw('concat(banks.name,"-",user_banks.account_number) as name'), 'user_banks.id', 'banks.transaction_fee_type', 'banks.transaction_fee', 'banks.tax_transaction')
            ->leftJoin('banks', 'banks.id', '=', 'user_banks.bank_id')
            ->where('user_banks.user_id', '=', $user->id)
            ->get();
        $selection = [
            'id',
            'payment_type',
            'amount',
            'bank_id',
            'transaction_charge',
            'notes',
            'branch_id'
        ];
        $data['data']['credit'] = Credit::select($selection)
            ->where('id', '=', $id)
            ->where('user_id', '=', $user->id)
            ->where('status', '=', 1)
            ->first();
        if ($data['data']['credit'] == null) {
            abort('404');
        }
        $data['data']['message'] = "";
        return Api::ApiResponse($data, $status_code);
    }*/

    /**
     * @SWG\Put(
     *   path="/client/credits/{credit_id}",
     *   summary="updating data related for data",
     *   tags={"credits"},
     *     @SWG\Parameter(name="Authorization",in="header",description="Bearer (token here)",type="string"),
     *     @SWG\Parameter(parameter="id_in_path", name="credit_id", type="integer", in="path"),
     *
     *     @SWG\Parameter(name="payment_type",in="query",description="""1""=>""cash payout request"",""2""=>""bank request""",type="integer"),
     *     @SWG\Parameter(name="amount",in="query",description="amount of credit request",type="integer"),
     *     @SWG\Parameter(name="bank_id",in="query",description="bank is selection from drop down",type="integer"),
     *     @SWG\Parameter(name="transaction_charge",in="query",description="transaction charge on the bases of bank selection transaction fee amount+tax_transaction",type="number"),
     *     @SWG\Parameter(name="branch_id",in="query",description="branch selection from drop down in case of cash payout request",type="integer"),
     *     @SWG\Parameter(name="notes",in="query",description="notes for request",type="integer"),
     *
     *     @SWG\Parameter(name="terms_accepted",in="query",description="1 if client accepted terms other wise 0",type="integer"),
     *
     *     @SWG\Response(response=200, description="{""data"":{{""message"": ""Credit request updated successfully.""}}}"),
     *     @SWG\Response(response=500, description="internal server error")
     * )
     */

    public function update($id)
    {
        $data = [];
        $status_code = 200;
        $credit = Credit::find($id);
        if ($credit == null) {
            abort(404);
        }
        $user = JWTAuth::toUser(request()->header('token'));
        $inputs = request()->all();
        $inputs['user_id'] = $user->id;
        $validator = Validator::make($inputs, Credit::apiValidationRules($inputs));

        if ($validator->fails()) {
            $status_code = 422;
            $data['data']['message'] = __('api.validation_error_occurred', [], request('lang'));
            $data['data']['errors'] = $validator->errors();
        } else {
            $inputs['transaction_charge'] = 0;
            if ($inputs['payment_type'] == 2) {
                $bank = UserBank::find($inputs['bank_id']);
                if ($bank != null) {
                    $bank = Bank::find($bank->bank_id);
                    if ($bank != null) {
                        if ($bank->transaction_fee_type == 1) {
                            $inputs['transaction_charge'] = ($bank->transaction_fee * $inputs['amount'] / 100) + $bank->tax_transaction;
                        } else if ($bank->transaction_fee_type == 2) {
                            $inputs['transaction_charge'] = $bank->transaction_fee + $bank->tax_transaction;
                        }
                    }
                }
            }
            $inputs['status'] = '1';
            $credit->update($inputs);

            $data['data']['message'] = __('api.updated_successfully', ['name' => __('api.credit', [], request('lang'))], request('lang'));
        }

        return Api::ApiResponse($data, $status_code);
    }

    /**
     * @SWG\Delete(
     *   path="/client/credits/{credit_id}",
     *   summary="Credit request delete api only if credit is in status requested",
     *     tags={"credits"},
     *     @SWG\Parameter(name="Authorization",in="header",description="Bearer (token here)",type="string"),
     *     @SWG\Parameter(name="Language",in="header",description="values will be en,es,nl",type="string"),
     *     @SWG\Parameter(parameter="id_in_path", name="credit_id", type="integer", in="path"),
     *
     *     @SWG\Response(response=200, description="{""data"":{{""message"":""Loan Application successfully deleted.""}}}"),
     *     @SWG\Response(response=401, description="{""data"":{{""message"":""Loan Application not found.""}}},{""data"":{{""message"":""Something went wrong.""}}}"),
     *     @SWG\Response(response=500, description="internal server error")
     * )
     */
    public function destroy($id)
    {
        $data = [];
        $status_code = 200;
        $user = JWTAuth::toUser(request()->header('token'));

        $credit = Credit::where('id', '=', $id)
            ->where('user_id', '=', $user->id)
            ->where('status', '=', '1')
            ->first();

        if ($credit != null) {
            if (!$credit->delete()) {
                $data['data']['message'] = __('api.something_went_wrong', [], request('lang'));
            } else {
                $data['data']['message'] = __('api.deleted_successfully', ['name' => __('api.credit', [], request('lang'))], request('lang'));
            }
        } else {
            $status_code = 401;
            $data['data']['message'] = __('api.not_found', ['name' => __('api.credit', [], request('lang'))], request('lang'));
        }

        return Api::ApiResponse($data, $status_code);
    }

}
