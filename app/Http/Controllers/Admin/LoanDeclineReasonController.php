<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoanDeclineReason;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class LoanDeclineReasonController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:super admin');
    }

    public function index()
    {
        return view('admin.loandecline-reasons.index');
    }

    public function getList()
    {
        $loanDeclineReason = LoanDeclineReason::select([
            'id',
            'title',
            'title_es',
            'title_nl',
        ]);
        return DataTables::of($loanDeclineReason)
            ->addColumn('action', function ($data) {
                $iconClass = "btn btn-icon btn-sm btn-info waves-effect";
                $html = '<div class="button-list">';
                $html .= "<a href='javascript:;' title='Edit' onclick='setEdit($data->id)' class='$iconClass'><i class='fa fa-pencil'></i></a>";
                $html .= "<a href='javascript:;' title='Delete' data-modal-id='deleteLoanDeclineReason' data-id='$data->id' onclick='DeleteConfirm(this)' class='$iconClass'><i class='fa fa-trash'></i></a>";
                $html .= "<a href='javascript:;' title='View' onclick='setEdit($data->id," . '"view"' . ")' class='$iconClass'><i class='fa fa-eye'></i></a>";
                $html .= "</div>";
                return $html;
            })
            ->make();
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            "title" => 'required',
        ]);
        $id = $request->id;
        $loanDeclineReason = LoanDeclineReason::find($id);
        if ($loanDeclineReason) {
            $loanDeclineReason->update($request->all());
        } else {
            $inputs = $request->all();
            LoanDeclineReason::create($inputs);
        }
        return response()->json([
            "status"  => "success",
            "message" => "Saved successfully.",
        ]);
    }

    public function show($id)
    {
        $loanDeclineReason = LoanDeclineReason::find($id);
        $filteredArr = [
            'id'       => ["type" => "hidden", 'value' => $loanDeclineReason->id],
            'title'    => ["type" => "text", 'value' => $loanDeclineReason->title],
            'title_nl' => ["type" => "text", 'value' => $loanDeclineReason->title_nl],
            'title_es' => ["type" => "text", 'value' => $loanDeclineReason->title_es],
        ];
        return response()->json([
            "status" => "success",
            "inputs" => $filteredArr,
        ]);
    }

    public function destroy($id)
    {
        $loanDeclineReason = LoanDeclineReason::find($id);
        $loanDeclineReason->delete();
        return response()->json([
            "status"  => "success",
            "message" => "Deleted successfully.",
        ]);
    }
}
