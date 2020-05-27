<?php

namespace App\Models;

use App\Library\FirebaseHelper;
use App\Mail\CreditStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class Credit extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'payment_type',
        'amount',
        'bank_id',
        'transaction_charge',
        'notes',
        'status',
        'file_name',
        'file_path',
        'branch_id',
        'reconciled_at',
    ];

    public static function validationRules($inputs)
    {
        $user_id = auth()->id();
        if (isset($inputs['user_id'])) {
            $user_id = $inputs['user_id'];
        }
        $wallet = Wallet::getUserWalletAmount($user_id);

        $user = User::find($user_id);
        $get_hold_balance = 0;
        if (isset($inputs['id'])) {
            $get_hold_balance = $user->getHoldBalance($inputs['id']);
        } else {
            $get_hold_balance = $user->getHoldBalance();
        }
        $available_balance = floatval($wallet) - $get_hold_balance;
        $wallet = $available_balance ? $available_balance : 0;
        $rules = [
            'notes'        => 'max:1000',
            'payment_type' => 'required|numeric',
        ];
        if (isset($inputs['payment_type']) && $inputs['payment_type'] == 2) {
            $rules += [
                'bank_id' => 'required|numeric',
            ];
        }
        if (isset($inputs['payment_type']) && $inputs['payment_type'] == 1 && auth()->user()->hasRole('client')) {
            $rules += [
                'branch_id' => 'required|numeric',
            ];
        }

        if (auth()->user()->hasAnyRole('admin|super admin|processor|credit and processing')) {
            $rules += [
                'user_id'      => 'required|numeric',
                'payment_type' => 'required|numeric',
            ];
        }

        $rules += [
            'amount' => 'required|numeric|max:' . $wallet
        ];


        if (request('status') == 2) {
            $rules += [
                'proof_image' => 'required',
            ];
        }

        return $rules;
    }

    public static function apiValidationRules($inputs)
    {
        $user_id = auth()->id();
        if (isset($inputs['user_id'])) {
            $user_id = $inputs['user_id'];
        }
        $wallet = Wallet::getUserWalletAmount($user_id);

        $user = User::find($user_id);
        $get_hold_balance = 0;
        if (isset($inputs['id'])) {
            $get_hold_balance = $user->getHoldBalance($inputs['id']);
        } else {
            $get_hold_balance = $user->getHoldBalance();
        }
        $available_balance = floatval($wallet) - $get_hold_balance;
        $wallet = $available_balance ? $available_balance : 0;
        $rules = [
            'notes'        => 'max:1000',
            'payment_type' => 'required|numeric',
        ];
        if (isset($inputs['payment_type']) && $inputs['payment_type'] == 2) {
            $rules += [
                'bank_id' => 'required|numeric',
            ];
        }
        if (isset($inputs['payment_type']) && $inputs['payment_type'] == 1) {
            $rules += [
                'branch_id' => 'required|numeric',
            ];
        }

        $rules += [
            'user_id'      => 'required|numeric',
            'payment_type' => 'required|numeric',
            'amount'       => 'required|numeric|max:' . $wallet
        ];

        return $rules;
    }

    public function statusChange($status, $note = null)
    {
        CreditStatusHistory::create([
            'status_id' => $status,
            'user_id'   => auth()->user()->id,
            'credit_id' => $this->id,
            'notes'     => $note
        ]);
    }

    public function sendStatusChangeMail()
    {
        $email_status = true;
        if ($this->status == 2 && $this->payment_type == 1) {
            $email_status = false;
        }
        $user = User::find($this->user_id);
        if ($user->email != null && $user->email != '') {
            if ($email_status) {
                try {
                    Mail::to($user->email)->send(new CreditStatus($user, $this));
                } catch (\Exception $e) {
                    Log::error($e);
                }
            }
        }

        $this->notificationSend($user);
    }

    public function notificationSend($user)
    {

        $status = '';
        if ($this->status == 1) {
            $status = 'Requested';
        } else if ($this->status == 2) {
            if ($this->payment_type == 2) {
                $status = 'In process';
            } elseif ($this->payment_type == 1) {
                $status = 'Approved';
            }
        } else if ($this->status == 3) {
            $status = 'Completed';
        } else if ($this->status == 4) {
            $status = 'Rejected';
        }
        $status = Lang::get('keywords.' . $status, [], $user->lang);

        $data = [
            'app_name'    => config('app.name'),
            'client_name' => ucwords(strtolower($user->firstname . ' ' . $user->lastname)),
            'credit_id'   => $this->id,
            'status'      => $status
        ];

        $key = '';
        if ($this->payment_type == 1) {
            $key = 'credit_cash_payout';
        } elseif ($this->payment_type == 2) {
            $key = 'credit_bank_transfer';
        }

        $template = Template::findFromKey($key, 2, $user->lang, $data);

        $data = [
            'credit_id' => $this->id,
        ];

        FirebaseHelper::firebaseNotification($this->user_id, $template->subject, $template->content, 'credits', $data);
    }

}
