<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoanStatus;
use DataTables;
use Illuminate\Http\Request;

class LoanStatusController extends Controller
{
    public function index()
    {
        return view('admin.loanstatus.index');
    }

    public function getList()
    {
        $loanStatus = LoanStatus::select([
            'id',
            'title',
            'title_es',
            'title_nl',
        ]);
        return DataTables::of($loanStatus)
            ->addColumn('action', function ($data) {
                $iconClass = "btn btn-icon btn-sm btn-info waves-effect";
                $html = '<div class="button-list">';
                $html .= "<a href='javascript:;' title='Edit' onclick='setEdit($data->id)' class='$iconClass'><i class='fa fa-pencil'></i></a>";
                $html .= "<a href='javascript:;' title='Delete' data-modal-id='deleteLoanStatus' data-id='$data->id' onclick='DeleteConfirm(this)' class='$iconClass'><i class='fa fa-trash'></i></a>";
                $html .= "<a href='javascript:;' title='View' onclick='setEdit($data->id," . '"view"' . ")' class='$iconClass'><i class='fa fa-eye'></i></a>";
                $html .= "</div>";
                return $html;
            })
            ->removeColumn('id')
            ->make();
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            "title" => 'required',
        ]);
        $id = $request->id;
        $loanStatus = LoanStatus::find($id);
        if ($loanStatus) {
            $loanStatus->update($request->all());
        } else {
            LoanStatus::create($request->all());
        }
        return response()->json([
            "status"  => "success",
            "message" => "Saved successfully.",
        ]);
    }

    public function show($id)
    {
        $loanStatus = LoanStatus::find($id);
        $filteredArr = [
            'id'       => ["type" => "hidden", 'value' => $loanStatus->id],
            'title'    => ["type" => "text", 'value' => $loanStatus->title],
            'title_nl' => ["type" => "text", 'value' => $loanStatus->title_nl],
            'title_es' => ["type" => "text", 'value' => $loanStatus->title_es],
        ];
        return response()->json([
            "status" => "success",
            "inputs" => $filteredArr,
        ]);
    }

    public function destroy($id)
    {
        $loanStatus = LoanStatus::find($id);
        $loanStatus->delete();
        return response()->json([
            "status"  => "success",
            "message" => "Deleted successfully.",
        ]);
    }
}
