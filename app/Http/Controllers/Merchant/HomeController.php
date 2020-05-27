<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Library\Helper;
use App\Models\LoanTransaction;
use App\Models\Merchant;
use App\Models\MerchantReconciliation;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;

class HomeController extends Controller
{
    public function index()
    {
        $data = [];
        $merchant = Helper::authMerchantUser();

        $sub_users = Merchant::where('type', '=', 2)->where('merchant_id', '=', $merchant->id)->pluck('id');

        $total_balance = LoanTransaction::where('merchant_id', '=', $merchant->id);
        if (session()->has('branch_id')) {
            $total_balance->where('branch_id', '=', session('branch_id'));
        }
        $total_balance = $total_balance->sum('amount');

        $total_commission = LoanTransaction::where('merchant_id', '=', $merchant->id);
        if (session()->has('branch_id')) {
            $total_commission->where('branch_id', '=', session('branch_id'));
        }
        $total_commission = $total_commission->sum('commission_calculated');

        $reconcilied = MerchantReconciliation::where(function ($query) use ($sub_users, $merchant) {
            $query->whereIn('merchant_id', $sub_users)->orWhere('merchant_id', '=', $merchant->id);
        });
        if (session()->has('branch_id')) {
            $reconcilied->where('branch_id', '=', session('branch_id'));
        }
        $reconcilied = $reconcilied->sum('amount');

        $data['total_balance'] = Helper::decimalRound2($total_balance);
        $data['total_commission'] = Helper::decimalRound2($total_commission);
        $data['reconciled'] = Helper::decimalRound2($reconcilied);
        $data['account_payable'] = Helper::decimalRound2($total_balance - $total_commission - $reconcilied);
        return view('merchant.pages.home.index', $data);
    }

    public function branch($id)
    {
        $data = [];
        if ($id == 0) {
            session()->forget('branch_id');
        } else {
            session()->put([
                'branch_id' => $id
            ]);
        }
        return $data;
    }

    public function changePassword()
    {
        $this->validate(request(), [
            'old_password'     => 'required',
            'new_password'     => [
                'required',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*(_|[^\w])).+$/'
            ],
            'confirm_password' => 'required|same:new_password',
        ], [
            'new_password.regex' => Lang::get('keywords.password_with_special_char'),
            'new_password.min'   => Lang::get('keywords.password_with_special_char')
        ]);
        $user = Helper::authMerchantUser();
        if (!Hash::check(request('old_password'), $user->password)) {
            return response()->json(['old_password' => Lang::get('keywords.password_does_not_match')], 422);
        } else {
            $user->update(['password' => bcrypt(request('new_password'))]);
        }
        $data = [];
        return $data;
    }
}
