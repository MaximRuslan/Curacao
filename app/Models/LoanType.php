<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;

class LoanType extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'title_nl',
        'title_es',
        'minimum_loan',
        'maximum_loan',
        'unit',
        'loan_component',
        'apr',
        'number_of_days',
        'interest',
        'cap_period',
        'territory_id',
        'status',
        'origination_type',
        'origination_amount',
        'renewal_type',
        'renewal_amount',
        'debt_type',
        'debt_amount',
        'debt_collection_type',
        'debt_collection_percentage',
        'debt_tax_type',
        'debt_tax_amount',
        'country_id',
        'loan_agreement_eng',
        'loan_agreement_esp',
        'loan_agreement_pap',
        'pagare',
        'debt_collection_tax_type',
        'debt_collection_tax_value',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    public function user_statuses()
    {
        return $this->belongsToMany(UserStatus::class, 'loan_type_user_statuses')->withTimestamps();
    }

    public static function activeAllLoanTypes()
    {
        return LoanType::orderBy('id', 'ASC')
            ->where('status', '=', '1')
            ->pluck('title', 'id');
    }

    public static function activeLoanTypesViaUserId($user, $lang = null)
    {
        if ($lang == null) {
            $lang = App::getLocale();
        }
        $loantypes = [];
        if ($user->country != null) {
            $loantypes = LoanType::leftJoin('loan_type_user_statuses', 'loan_type_user_statuses.loan_type_id', '=', 'loan_types.id')
                ->where('loan_types.country_id', '=', $user->country)
                ->where('loan_types.status', '=', 1)
                ->where(function ($query) use ($user) {
                    $query->where('loan_type_user_statuses.user_status_id', '=', 0)
                        ->orWhere('loan_type_user_statuses.user_status_id', '=', $user->status);
                });

            if ($lang == 'esp') {
                $loantypes->orderBy('loan_types.title_es', 'asc')
                    ->select('loan_types.title_es as title', 'loan_types.id');
            } else if ($lang == 'pap') {
                $loantypes->orderBy('loan_types.title_nl', 'asc')
                    ->select('loan_types.title_nl as title', 'loan_types.id');
            } else {
                $loantypes->orderBy('loan_types.title', 'asc')
                    ->select('loan_types.title', 'loan_types.id');
            }
            $loantypes->groupBy('loan_types.id');
        }
        return $loantypes->get();
    }

    public static function validationRules($min)
    {
        return [
            'minimum_loan'               => 'required',
            'maximum_loan'               => 'required',
            'unit'                       => 'required',
            'loan_component'             => 'required',
            'apr'                        => 'required',
            'origination_type'           => 'required',
            'origination_amount'         => 'required',
            'number_of_days'             => 'required',
            'interest'                   => 'required',
            'cap_period'                 => 'required|numeric|min:' . $min,
            'renewal_type'               => 'required',
            'renewal_amount'             => 'required',
            'debt_type'                  => 'required',
            'debt_amount'                => 'required',
            'debt_collection_type'       => 'required',
            'debt_collection_percentage' => 'required',
            'debt_collection_tax_type'   => 'required',
            'debt_collection_tax_value'  => 'required',
            'debt_tax_type'              => 'required',
            'debt_tax_amount'            => 'required',
            'user_status'                => 'required|array'
        ];
    }
}
