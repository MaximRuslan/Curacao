<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
        $data['countries'] = Country::orderBy('name', 'asc')
            ->pluck('name', 'id');
        return view('admin.bank.index', $data);
    }

    public function store()
    {
        $this->validate(request(), Bank::validationRules());
        $id = request('id');
        $bank = Bank::find($id);
        if ($bank) {
            $bank->update(request()->all());
        } else {
            $bank = Bank::create(request()->all());
        }
        /*if (request('territory_id')) {
            $bank->territories()->sync(request('territory_id'));
        }*/
        return response()->json([
            "status"  => "success",
            "message" => "Saved successfully.",
        ]);
    }

    public function show(Bank $bank)
    {
        $filteredArr = [
            'id'                   => ["type" => "hidden", 'value' => $bank->id],
            'name'                 => ["type" => "text", 'value' => $bank->name],
            'contact_person'       => ["type" => "text", 'value' => $bank->contact_person],
            'email'                => ["type" => "text", 'value' => $bank->email],
            'phone'                => ["type" => "text", 'value' => $bank->phone],
            'territory_id'         => ["type" => "select", 'value' => $bank->territories->pluck('id')],
            'transaction_fee_type' => ["type" => "select", 'value' => $bank->transaction_fee_type],
            'transaction_fee'      => ["type" => "text", 'value' => $bank->transaction_fee],
            'tax_transaction'      => ["type" => "text", 'value' => $bank->tax_transaction],
            'country_id'           => [
//                'type'  => 'select-territory',
                'type'  => 'select',
                'value' => $bank->country_id,
//                'territory' => $bank->territories->pluck('id')
            ]
        ];
        return response()->json([
            "status" => "success",
            "inputs" => $filteredArr,
        ]);
    }

    public function indexDatatable()
    {
        $loanStatus = Bank::select('banks.*', 'countries.name as country')
            ->leftJoin('countries', 'countries.id', '=', 'banks.country_id');
        return DataTables::of($loanStatus)
            ->addColumn('transaction_fee', function ($data) {
                return number_format($data->transaction_fee, 2);
            })
            ->addColumn('action', function ($data) {
                $iconClass = "btn btn-icon btn-sm btn-info waves-effect";
                $html = '<div class="button-list">';
                $html .= "<a href='javascript:;' title='Edit' onclick='setEdit($data->id)' class='$iconClass'><i class='fa fa-pencil'></i></a>";
                $html .= "<a href='javascript:;' title='Delete' data-modal-id='deleteBank' data-id='$data->id' onclick='DeleteConfirm(this)' class='$iconClass'><i class='fa fa-trash'></i></a>";
                $html .= "<a href='javascript:;' title='View' onclick='setEdit($data->id," . '"view"' . ")' class='$iconClass'><i class='fa fa-eye'></i></a>";
                $html .= "</div>";
                return $html;
            })
            ->make();
    }

    public function destroy(Bank $bank)
    {
        $bank->delete();
        return response()->json([
            "status"  => "success",
            "message" => "Deleted successfully.",
        ]);
    }
}
