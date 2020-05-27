<?php

namespace App\Models;

use App\Library\FirebaseHelper;
use App\Library\Helper;
use App\Library\TemplateHelper;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Lang;

class LoanApplication extends BaseModel
{

    use SoftDeletes;

    protected $fillable = [
        'client_id',
        'loan_reason',
        'amount',
        'loan_type',
        'loan_status',
        'loan_decline_reason',
        'decline_description',
        'salary',
        'start_date',
        'end_date',
        'deadline_date',
        'employee_id',
        'other_loan_deduction',
        'signature',
        'tax_percentage',
        'tax_name',
        'tax',
        'loan_component',
        'origination_type',
        'origination_amount',
        'origination_fee',
        'renewal_type',
        'renewal_amount',
        'debt_type',
        'debt_amount',
        'debt_collection_type',
        'debt_collection_percentage',
        'debt_tax_type',
        'debt_tax_amount',
        'period',
        'interest',
        'interest_amount',
        'cap_period',
        'max_amount',
        'signature_pdf',
        'debt_collection_tax_type',
        'debt_collection_tax_value',
        'created_by',
        'updated_by',
        'deleted_by',
    ];


    public function user()
    {
        return $this->hasOne(User::class, 'id', 'client_id');
    }

    public function reason()
    {
        return $this->hasOne(LoanReason::class, 'id', 'loan_reason');
    }

    public function type()
    {
        return $this->hasOne(LoanType::class, 'id', 'loan_type');
    }

    public function status()
    {
        return $this->hasOne(LoanStatus::class, 'id', 'loan_status');
    }

    public function declineReason()
    {
        return $this->hasOne(LoanDeclineReason::class, 'id', 'loan_decline_reason')->orderBy('title', 'asc');
    }

    public function onHoldReason()
    {
        return $this->hasOne(LoanOnHoldReason::class, 'id', 'loan_decline_reason')->orderBy('title', 'asc');
    }

    public function amounts()
    {
        return $this->hasMany(LoanAmounts::class, 'loan_id', 'id');
    }

    public static function validationRules($max, $salaryMax, $id = 0)
    {
        $rules = [
            'salary'               => 'required',
            'loan_reason'          => 'required|numeric',
            'amount'               => 'required|numeric|min:1',
            // 'amount'               => 'required|numeric|min:1|max:' . $max,
            'other_loan_deduction' => 'nullable|numeric|max:' . $salaryMax,
            'date_of_payment.0'    => 'required',
            'income_amount.*'      => 'required',
            'other_amount.*'       => 'required',
        ];
        if ($id == 0) {
            $rules += [
                'income_proof_image.*'  => 'required|max:' . (config('app.max_file_size') * 1024),
                'expense_proof_image.*' => 'required|max:' . (config('app.max_file_size') * 1024),
            ];
        }
        return $rules;
    }

    public static function adminValidationRules()
    {
        return [
            'client_id'            => 'required|numeric|exists:users,id,role_id,3',
            'loan_reason'          => 'required|exists:loan_reasons,id',
            'loan_type'            => 'required|exists:loan_types,id',
            'amount'               => 'required|numeric|regex:/^\d*(\.\d{1,2})?$/',
            'signature'            => 'required',
            'income_type.*'        => 'required|in:1,2',
            'income_amount.*'      => 'required|numeric|regex:/^\d*(\.\d{1,2})?$/',
            'date_of_payment.0'    => 'required|date_format:d/m/Y',
            'income_proof_image.*' => 'required|mimes:png,gif,jpg,jpeg,bmp,doc,docx,pdf',
            'expense_type.*'       => 'required|exists:existing_loan_types,id',
            'other_amount.*'       => 'required|numeric|regex:/^\d*(\.\d{1,2})?$/',
        ];
    }

    public static function validationMessage()
    {
        return [
            'date_of_payment.0.required'     => 'Salary date is required',
            'amount.max'                     => 'Unfortunately the available loan is less than you requested, please lower your requested amount.',
            'income_proof_image.*.required'  => 'Income Proof is required',
            //:value
            'income_proof_image.*.mimes'     => 'Only  jpeg,png,gif,jpg,doc,docx,pdf and bmp images are allowed for image',
            //:value
            'expense_proof_image.*.required' => 'Expanse Proof Image is required',
            //:value
            'expense_proof_image.*.mimes'    => 'Only jpeg,png and bmp images are allowed for image',
            //:value
            'income_amount.*.required'       => 'Income Amount is required',
            'other_amount.*.required'        => 'Expanse Amount  is required',
        ];
    }

    public static function addLoanStatusHistory($loan_id, $status, $note = null, $user = null, $date = null)
    {
        if ($user == null) {
            if (Helper::authMerchant()) {
                $user = Helper::authMerchantUser();
            } else {
                $user = auth()->user();
            }
        }

        $inputs = [
            'user_id'   => $user->id,
            'status_id' => $status,
            'loan_id'   => $loan_id,
            'note'      => $note,
        ];

        if ($date != null) {
            LoanStatusHistory::where('loan_id', '=', $loan_id)->where('created_at', '>=', date('Y-m-d H:i:s', strtotime($date)))->delete();
            $inputs['created_at'] = $date;
        }
        LoanStatusHistory::create($inputs);
    }

    public static function clientValidationRules($inputs)
    {
        $salaryMax = collect($inputs['income_amount'])->sum();

        $rules = [
            'salary'               => 'required|numeric|regex:/^\d*(\.\d{1,2})?$/',
            'loan_reason'          => 'required|numeric',
            'amount'               => 'required|numeric|min:1|regex:/^\d*(\.\d{1,2})?$/',
            'other_loan_deduction' => 'nullable|numeric|regex:/^\d*(\.\d{1,2})?$/|max:' . $salaryMax,
            'date_of_payment.0'    => 'required',
            'income_amount.*'      => 'required|numeric|regex:/^\d*(\.\d{1,2})?$/',
            'other_amount.*'       => 'required|numeric|regex:/^\d*(\.\d{1,2})?$/',
        ];

        if (!isset($inputs['id']) || $inputs['id'] == 0) {
            $rules += [
                'income_proof_image.*'  => 'required|max:' . (config('app.max_file_size') * 1024),
                'expense_proof_image.*' => 'required|max:' . (config('app.max_file_size') * 1024),
            ];
        } else {
            $rules += [
                'income_proof_image.*'  => 'nullable|max:' . (config('app.max_file_size') * 1024),
                'expense_proof_image.*' => 'nullable|max:' . (config('app.max_file_size') * 1024),
            ];
        }

        return $rules;
    }

    public function signaturePdfHtml($data)
    {
        $str = '<html>
                    <body>
                        <h4>Loan Agreement Text</h4>
                        ' . $data['agreement_text'] . '
                        <br><br>
                        <table>
                            <tr  style="border: 1px #000 solid;">
                                <th>Signature</th>
                                <td><img width="100px" src="' . $data['signature'] . '"></td>
                            </tr>
                            <tr  style="border: 1px #000 solid;">
                                <th>User Name</th>
                                <td>' . $data['client_name'] . '</td>
                            </tr>
                            <tr  style="border: 1px #000 solid;">
                                <th>Date Time</th>
                                <td>' . $data['requested_date'] . '</td>
                            </tr>
                        </table>
                    </body>
                </html>';

        return $str;
    }

    public function generateSignaturePdf($timezone = null)
    {
        $loantype = LoanType::find($this->loan_type);
        $user = User::find($this->client_id);
        $requested_date = '';
        if ($timezone == null) {
            $requested_date = Helper::date_time_to_current_timezone($this->created_at);
        } else {
            $requested_date = Helper::date_time_to_current_timezone($this->created_at, $timezone);
        }

        $inputs = [
            'first_name'     => '',
            'last_name'      => '',
            'address'        => '',
            'loan_amount'    => '',
            'number_of_days' => '',
            'civil_status'   => '',
            'id_number'      => '',
            'today_date'     => '',
        ];

        if ($user != null) {
            $inputs['first_name'] = $user->firstname;
            $inputs['last_name'] = $user->lastname;
            $inputs['address'] = $user->address;
            $inputs['civil_status'] = Lang::get('keywords.' . config('site.civil_statues.' . $user->civil_status), [], $user->lang);
            $inputs['id_number'] = $user->id_number;
        }

        $inputs['today_date'] = Helper::datebaseToFrontDate(date('Y-m-d'));
        $inputs['number_of_days'] = $loantype->number_of_days * 7;
        $inputs['loan_amount'] = $this->amount;

        $cms = '';
        if ($user->lang == 'esp') {
            $cms = $loantype->loan_agreement_esp;
        } else {
            if ($user->lang == 'pap') {
                $cms = $loantype->loan_agreement_pap;
            } else {
                $cms = $loantype->loan_agreement_eng;
            }
        }

        $data = [
            'agreement_text' => TemplateHelper::replaceNotificationTemplateTag($cms, $inputs),
            'signature'      => asset('uploads/' . $this->signature),
            'client_name'    => ucwords(strtolower($user->lastname . ' ' . $user->firstname)),
            'requested_date' => $requested_date,
        ];
        $pdf = \PDF::loadHTML($this->signaturePdfHtml($data));
        $filename = time() . '_signature_' . $this->id . '.pdf';
        $pdf->setPaper('a4', 'landscape')->save(public_path('pdf/' . $filename));
        return $filename;
    }

    public function notificationSend()
    {
        $status = '';
        $reason = '';

        $loan_status = LoanStatus::find($this->loan_status);
        $loan_reason = LoanReason::find($this->loan_reason);
        $user = User::find($this->client_id);
        if ($loan_status != null) {
            if ($user->lang == 'esp') {
                $status = $loan_status->title_es;
            } else {
                if ($user->lang == 'pap') {
                    $status = $loan_status->title_nl;
                } else {
                    $status = $loan_status->title;
                }
            }
        }
        if ($loan_reason != null) {
            if ($user->lang == 'esp') {
                $reason = $loan_reason->title_es;
            } else {
                if ($user->lang == 'pap') {
                    $reason = $loan_reason->title_nl;
                } else {
                    $reason = $loan_reason->title;
                }
            }
        }

        $data = [
            'app_name'    => config('app.name'),
            'client_name' => ucwords(strtolower($user->firstname . ' ' . $user->lastname)),
            'loan_id'     => $this->id,
            'reason'      => $reason,
            'status'      => $status,
        ];

        $key = 'loan_status';
        if ($this->loan_status == 1) {
            $key = 'loan_current_status';
        }

        $template = Template::findFromKey($key, 2, $user->lang, $data);

        $data = [
            'loan_id' => $this->id,
        ];

        FirebaseHelper::firebaseNotification($this->client_id, $template->subject, $template->content, 'loans', $data);
    }

    public static function inActiveStatuses()
    {
        return [1, 2, 3, 4, 5, 6, 12];
    }

    public static function hasActiveLoan($user)
    {
        $statuses = self::inActiveStatuses();
        $loans = LoanApplication::where('client_id', '=', $user->id)->whereIn('loan_status', $statuses)->count();
        if ($loans > 0) {
            return true;
        }
        return false;
    }

    public static function getActiveLoan($user)
    {
        $statuses = self::inActiveStatuses();
        return LoanApplication::where('client_id', '=', $user->id)->whereIn('loan_status', $statuses)->first();
    }

    public static function inActiveClients()
    {
        $statuses = self::inActiveStatuses();

        $clients = LoanApplication::whereIn('loan_status', $statuses)->pluck('client_id')->unique();

        return $clients;
    }

    public function isFirstLoan()
    {
        $loan = LoanApplication::withTrashed()->where('client_id', '=', $this->client_id)->orderBy('id', 'asc')->first();

        if ($this->id == $loan->id) {
            return true;
        } else {
            return false;
        }
    }

    public function createCountryPdf()
    {
        $user = User::find($this->client_id);

        $loan_type = LoanType::find($this->loan_type);

        $country = Country::find($user->country);

        $html = $loan_type->pagare;

        if ($html == null) {
            $html = '';
        }

        $district = UserTerritory::find($user->territory);
        if ($district != null) {
            $district = $district->title;
        } else {
            $district = '';
        }

        $position = '';

        $user_work = UserWork::where('user_id', '=', $user->id)
            ->where('employed_since', '<=', date('Y-m-d'))
            ->where('contract_expires', '>=', date('Y-m-d'))
            ->first();

        if ($user_work != null) {
            $position = $user_work->position;
        }

        $origination_fee_type = '';
        if (isset(config('site.debt_collection_fee_type')[$loan_type->origination_type])) {
            $origination_fee_type = config('site.debt_collection_fee_type')[$loan_type->origination_type];
        }
        $origination_amount = '';
        if ($loan_type->origination_amount != '' && $loan_type->origination_amount != null) {
            $origination_amount = $loan_type->origination_amount;
        }

        $renewal_type = '';
        if (isset(config('site.debt_collection_fee_type')[$loan_type->renewal_type])) {
            $renewal_type = config('site.debt_collection_fee_type')[$loan_type->renewal_type];
        }
        $renewal_amount = '';
        if ($loan_type->renewal_amount != '' && $loan_type->renewal_amount != null) {
            $renewal_amount = $loan_type->renewal_amount;
        }

        $f = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);

        $inputs = [
            'logo'               => '<img src="' . asset('uploads/' . $country->logo) . '" style="width=100px;height:100px;">',
            'amount'             => $this->amount,
            'amount_in_words'    => $f->format($this->amount),
            'date'               => date('d/m/Y'),
            'client_name'        => $user->firstname . " " . $user->lastname,
            'position_of_work'   => $position,
            'address'            => $user->address,
            'district'           => $district,
            'lang'               => $user->lang,
            'civil_status'       => Lang::get('keywords.' . config('site.civil_statues.' . $user->civil_status), [], $user->lang),
            'id_number'          => $user->id_number,
            'today_date'         => Helper::datebaseToSheetDate(date('Y-m-d H:i:s'), $country->timezone),
            'origination_type'   => $origination_fee_type,
            'origination_amount' => $origination_amount,
            'renewal_type'       => $renewal_type,
            'renewal_amount'     => $renewal_amount,
        ];

        $html = TemplateHelper::replaceNotificationTemplateTag($html, $inputs);

        $pdf = \PDF::loadHTML($html);

        $filename = time() . $user->id . 'agreement.pdf';

        $pdf->setPaper('a4', 'portrait')->save(public_path('pdf/' . $filename));

        return asset('pdf/' . $filename);
    }

}
