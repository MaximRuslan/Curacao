<?php

namespace App\Http\Controllers\Admin1;

use App\Http\Controllers\Controller;
use App\Models\LoanOnHoldReason;
use Yajra\DataTables\Facades\DataTables;

class LoanOnholdReasonController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:super admin');
    }

    public function index()
    {
        return view('admin1.pages.loanOnholdReason.index');
    }

    public function edit(LoanOnHoldReason $reason)
    {
        $data = [];

        $data['inputs'] = [
            'id'       => ["type" => "hidden", 'value' => $reason->id],
            'title'    => ["type" => "text", 'value' => $reason->title],
            'title_es' => ["type" => "text", 'value' => $reason->title_es],
            'title_nl' => ["type" => "text", 'value' => $reason->title_nl],
        ];
        return $data;
    }

    public function store()
    {
        $this->validate(request(), LoanOnHoldReason::validationRules());
        $id = request('id');
        $reason = LoanOnHoldReason::find($id);
        if ($reason) {
            $reason->update(request()->all());
        } else {
            $reason = LoanOnHoldReason::create(request()->all());
        }
        $data = [];
        $data['status'] = true;
        return $data;
    }

    public function destroy(LoanOnHoldReason $reason)
    {
        $data = [];
        $data['status'] = $reason->delete();
        return $data;
    }

    public function indexDatatable()
    {
        $loanStatus = LoanOnHoldReason::select('*');
        return DataTables::of($loanStatus)
            ->addColumn('action', function ($row) {
                $iconClass = "btn btn-icon btn-sm btn-info waves-effect";
                $html = '<div class="button-list">';
                $html .= "<a href='javascript:;' title='Edit' data-toggle='tooltip' data-id='$row->id' class='$iconClass editReason'>
                            <i class='fa fa-pencil'></i>
                        </a>";
                $html .= "<a href='javascript:;' title='Delete' data-toggle='tooltip' data-id='$row->id' class='$iconClass deleteReason'><i class='fa fa-trash'></i></a>";
                $html .= "<a href='javascript:;' title='View' data-toggle='tooltip' class='$iconClass editReason' data-id='$row->id' data-type='view'>
                            <i class='fa fa-eye'></i>
                        </a>";
                $html .= "</div>";
                return $html;
            })
            ->make();
    }
}
