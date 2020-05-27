<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NLBReason;
use Yajra\DataTables\Facades\DataTables;

class NLBReasonController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:super admin')->except([
            'typeReasonsListing'
        ]);
    }

    public function index()
    {
        return view('admin.nlb-reasons.index');
    }

    public function store()
    {
        $this->validate(request(), NLBReason::validationRules());
        $id = request('id');
        $reason = NLBReason::find($id);
        if ($reason) {
            $reason->update(request()->all());
        } else {
            $reason = NLBReason::create(request()->all());
        }
        return response()->json([
            "status"  => "success",
            "message" => "Saved successfully.",
        ]);
    }

    public function show(NLBReason $nlb_reason)
    {
        $filteredArr = [
            'id'    => ["type" => "hidden", 'value' => $nlb_reason->id],
            'title' => ["type" => "text", 'value' => $nlb_reason->title],
            'type'  => ["type" => "select", 'value' => $nlb_reason->type],
        ];
        return response()->json([
            "status" => "success",
            "inputs" => $filteredArr,
        ]);
    }

    public function indexDatatable()
    {
        $loanStatus = NLBReason::select('*');
        return DataTables::of($loanStatus)
            ->addColumn('type', function ($row) {
                if ($row->type == 1) {
                    return "IN";
                } elseif ($row->type == 2) {
                    return "OUT";
                }
            })
            ->addColumn('action', function ($data) {
                $iconClass = "btn btn-icon btn-sm btn-info waves-effect";
                $html = '<div class="button-list">';
                $html .= "<a href='javascript:;' title='Edit' onclick='setEdit($data->id)' class='$iconClass'><i class='fa fa-pencil'></i></a>";
                $html .= "<a href='javascript:;' title='Delete' data-modal-id='deleteNLBReason' data-id='$data->id' onclick='DeleteConfirm(this)' class='$iconClass'><i class='fa fa-trash'></i></a>";
                $html .= "<a href='javascript:;' title='View' onclick='setEdit($data->id," . '"view"' . ")' class='$iconClass'><i class='fa fa-eye'></i></a>";
                $html .= "</div>";
                return $html;
            })
            ->make();
    }

    public function destroy(NLBReason $nlb_reason)
    {
        $nlb_reason->delete();
        return response()->json([
            "status"  => "success",
            "message" => "Deleted successfully.",
        ]);
    }

    public function typeReasonsListing($type)
    {
        $data = [];
        $data['reasons'] = NLBReason::where('type', '=', $type)->pluck('title', 'id');
        return $data;
    }
}
