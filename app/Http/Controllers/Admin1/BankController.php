<?php

namespace App\Http\Controllers\Admin1;

use App\Http\Controllers\Controller;
use App\Library\Helper;
use App\Models\Bank;
use App\Models\Country;
use Yajra\DataTables\Facades\DataTables;

class BankController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:super admin');
    }

    public function index()
    {
        $data = [];
        $data['countries'] = Country::pluck('name', 'id');
        return view('admin1.pages.bank.index', $data);
    }

    public function store()
    {
        $this->validate(request(), Bank::validationRules());
        $id = request('id');
        $bank = Bank::find($id);
        $inputs = request()->all();
        $inputs['transaction_fee_type'] = 2;
        $country = Country::find(request('country_id'));
        $inputs['tax_transaction'] = round(request('transaction_fee') * $country->tax_percentage / 100, 2);
        if ($bank) {
            $bank->update($inputs);
        } else {
            $bank = Bank::create($inputs);
        }
        $data = [];
        $data['status'] = true;
        return $data;
    }

    public function edit(Bank $bank)
    {
        $data = [];
        $data['inputs'] = [
            'id'              => ["type" => "hidden", 'value' => $bank->id],
            'name'            => ["type" => "text", 'value' => $bank->name],
            'contact_person'  => ["type" => "text", 'value' => $bank->contact_person],
            'email'           => ["type" => "text", 'value' => $bank->email],
            'phone'           => ["type" => "number", 'value' => $bank->phone],
            'transaction_fee' => ["type" => "number", 'value' => $bank->transaction_fee],
            'tax_transaction' => ["type" => "number", 'value' => $bank->tax_transaction],
            'country_id'      => ['type' => 'select2', 'value' => $bank->country_id,]
        ];
        return $data;
    }

    public function destroy(Bank $bank)
    {
        $data = [];
        $data['status'] = $bank->delete();
        return $data;
    }

    public function indexDatatable()
    {
        $loanType = Bank::select('banks.*', 'countries.name as country_name')
            ->leftJoin('countries', 'countries.id', '=', 'banks.country_id');
        return DataTables::of($loanType)
            ->addColumn('transaction_fee', function ($row) {
                return Helper::decimalShowing($row->transaction_fee, $row->country_id);
            })
            ->addColumn('tax_transaction', function ($row) {
                return Helper::decimalShowing($row->tax_transaction, $row->country_id);
            })
            ->addColumn('action', function ($row) {
                $iconClass = "btn btn-icon btn-sm btn-info waves-effect";
                $html = '<div class="button-list">';
                $html .= "<a href='javascript:;' title='Edit' data-toggle='tooltip' data-id='$row->id' class='$iconClass editBank'>
                            <i class='fa fa-pencil'></i>
                        </a>";
                $html .= "<a href='javascript:;' title='Delete' data-toggle='tooltip' data-id='$row->id' class='$iconClass deleteBank'><i class='fa fa-trash'></i></a>";
                $html .= "<a href='javascript:;' title='View' data-toggle='tooltip' class='$iconClass editBank' data-id='$row->id' data-type='view'>
                            <i class='fa fa-eye'></i>
                        </a>";
                return $html;
            })
            ->make();
    }
}
