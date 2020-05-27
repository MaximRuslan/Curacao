<?php

namespace App\Http\Controllers\Admin1;

use App\Http\Controllers\Controller;
use App\Library\DefaultHelper;
use App\Models\Country;
use App\Models\LoanType;
use App\Models\UserStatus;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class LoanTypeController extends Controller
{

    public function __construct()
    {
        $this->middleware('role:super admin');
    }

    public function index()
    {
        $data = [];
        $data['countries'] = Country::pluck('name', 'id');
        $data['default_terms'] = DefaultHelper::defaultTerms();
        $data['user_statuses'] = UserStatus::pluckListing([1, 2])->pluck('title', 'id');
        return view('admin1.pages.loanType.index', $data);
    }

    public function store()
    {
        $this->validate(request(), LoanType::validationRules(request('number_of_days')));
        $id = request('id');
        $type = LoanType::find($id);
        $inputs = request()->all();
        if ($type) {
            $type->update($inputs);
        } else {
            $type = LoanType::create($inputs);
        }
        $user_statuses = request('user_status');
        if (in_array('0', $user_statuses)) {
            $user_statuses = ['0'];
        }

        $type->user_statuses()->sync($user_statuses);

        $data = [];
        $data['status'] = true;
        return $data;
    }

    public function edit(LoanType $type)
    {
        $data = [];
        $data['inputs'] = [
            'id'                         => ["type" => "hidden", 'value' => $type->id],
            'title'                      => ["type" => "text", 'value' => $type->title],
            'title_nl'                   => ["type" => "text", 'value' => $type->title_nl],
            'title_es'                   => ["type" => "text", 'value' => $type->title_es],
            'number_of_days'             => ["type" => 'number', 'value' => $type->number_of_days],
            'interest'                   => ["type" => 'number', 'value' => $type->interest],
            'cap_period'                 => ["type" => 'number', 'value' => $type->cap_period],
            'minimum_loan'               => ["type" => 'number', 'value' => intval($type->minimum_loan)],
            'maximum_loan'               => ["type" => 'number', 'value' => intval($type->maximum_loan)],
            'unit'                       => ["type" => 'number', 'value' => intval($type->unit)],
            'loan_component'             => ["type" => 'number', 'value' => $type->loan_component],
            'apr'                        => ["type" => 'number', 'value' => $type->apr],
            'origination_type'           => ["type" => 'select2', 'value' => $type->origination_type],
            'origination_amount'         => ["type" => 'number', 'value' => $type->origination_amount],
            'renewal_type'               => ["type" => 'select2', 'value' => $type->renewal_type],
            'renewal_amount'             => ["type" => 'number', 'value' => $type->renewal_amount],
            'debt_type'                  => ["type" => 'select2', 'value' => $type->debt_type],
            'debt_amount'                => ["type" => 'number', 'value' => $type->debt_amount],
            'debt_collection_type'       => ["type" => 'select2', 'value' => $type->debt_collection_type],
            'debt_collection_percentage' => ["type" => 'number', 'value' => $type->debt_collection_percentage],
            'debt_collection_tax_type'   => ['type' => 'select2', 'value' => $type->debt_collection_tax_type],
            'debt_collection_tax_value'  => ['type' => 'number', 'value' => $type->debt_collection_tax_value],
            'debt_tax_type'              => ["type" => 'select2', 'value' => $type->debt_tax_type],
            'debt_tax_amount'            => ["type" => 'number', 'value' => $type->debt_tax_amount],
            'loan_agreement_eng'         => ["type" => "tinymce", 'value' => $type->loan_agreement_eng],
            'loan_agreement_esp'         => ["type" => "tinymce", 'value' => $type->loan_agreement_esp],
            'loan_agreement_pap'         => ["type" => "tinymce", 'value' => $type->loan_agreement_pap],
            'pagare'                     => ["type" => "tinymce", 'value' => $type->pagare],
            'country_id'                 => ['type' => 'select2', 'value' => $type->country_id],
            'status'                     => ["type" => 'radio', 'value' => $type->status],
        ];
        $data['statuses'] = DB::table('loan_type_user_statuses')->where('loan_type_id', '=', $type->id)->pluck('user_status_id');
        return $data;
    }

    public function destroy(LoanType $type)
    {
        $data = [];
        $type->update([
            'deleted_by' => auth()->user()->id,
        ]);
        $data['status'] = $type->delete();
        return $data;
    }

    public function indexDatatable()
    {
        $selections = [
            'loan_types.id',
            'loan_types.title',
            'loan_types.title_es',
            'loan_types.title_nl',
            'countries.name as country',
        ];
        $loanType = LoanType::select($selections)
            ->leftJoin('countries', 'countries.id', '=', 'loan_types.country_id');
        if (session()->has('country')) {
            $loanType->where('loan_types.country_id', '=', session('country'));
        }
        return DataTables::of($loanType)
            ->addColumn('action', function ($row) {
                $iconClass = "btn btn-icon btn-sm btn-info waves-effect";
                $html = '<div class="button-list">';
                $html .= "<a href='javascript:;' title='Edit' data-toggle='tooltip' data-id='$row->id' class='$iconClass editType'>
                            <i class='fa fa-pencil'></i>
                        </a>";
                $html .= "<a href='javascript:;' title='Delete' data-toggle='tooltip' data-id='$row->id' class='$iconClass deleteType'><i class='fa fa-trash'></i></a>";
                $html .= "<a href='javascript:;' title='View' data-toggle='tooltip' class='$iconClass editType' data-id='$row->id' data-type='view'>
                            <i class='fa fa-eye'></i>
                        </a>";
                return $html;
            })
            ->make();
    }

}
