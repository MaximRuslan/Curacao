<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExistingLoanType;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ExistingLoanTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:super admin');
    }

    public function index()
    {
        return view('admin.existingloantype.index');
    }

    public function getList()
    {
        $type = ExistingLoanType::select([
            'id',
            'title',
            'title_es',
            'title_nl',
        ]);
        return DataTables::of($type)
            ->addColumn('action', function ($data) {
                $iconClass = "btn btn-icon btn-sm btn-info waves-effect";
                $html = '<div class="button-list">';
                $html .= "<a href='javascript:;' title='Edit' onclick='setEdit($data->id)' class='$iconClass'><i class='fa fa-pencil'></i></a>";
                $html .= "<a href='javascript:;' title='Delete' data-modal-id='deleteExistingLoanYype' data-id='$data->id' onclick='DeleteConfirm(this)' class='$iconClass'><i class='fa fa-trash'></i></a>";
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
        $type = ExistingLoanType::find($id);
        if ($type) {
            $type->update($request->all());
        } else {
            $inputs = $request->all();
            ExistingLoanType::create($inputs);
        }
        return response()->json([
            "status"  => "success",
            "message" => "Saved successfully.",
        ]);
    }

    public function show($id)
    {
        $type = ExistingLoanType::find($id);
        $filteredArr = [
            'id'       => ["type" => "hidden", 'value' => $type->id],
            'title'    => ["type" => "text", 'value' => $type->title],
            'title_nl' => ["type" => "text", 'value' => $type->title_nl],
            'title_es' => ["type" => "text", 'value' => $type->title_es],
        ];
        return response()->json([
            "status" => "success",
            "inputs" => $filteredArr,
        ]);
    }

    public function destroy($id)
    {
        $type = ExistingLoanType::find($id);
        $type->delete();
        return response()->json([
            "status"  => "success",
            "message" => "Deleted successfully.",
        ]);
    }
}
