<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Country;
use App\Models\Tax;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BranchController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:super admin');
    }

    public function index()
    {
        $data = [];
        $data['taxes'] = Tax::pluck('name', 'id');
        $data['countries'] = Country::pluck('name', 'id');
        return view('admin.branch.index', $data);
    }

    public function indexDatatable()
    {
        $territory = Branch::select('branches.*', 'countries.name as country')
            ->leftJoin('countries', 'countries.id', '=', 'branches.country_id');
        return DataTables::of($territory)
            ->addColumn('action', function ($data) {
                $iconClass = "btn btn-icon btn-sm btn-info waves-effect";
                $html = '<div class="button-list">';
                $html .= "<a href='javascript:;' title='Edit' onclick='setEdit($data->id)' class='$iconClass'><i class='fa fa-pencil'></i></a>";
                $html .= "<a href='javascript:;' title='Delete' data-modal-id='deleteBranch' data-id='$data->id' onclick='DeleteConfirm(this)' class='$iconClass'><i class='fa fa-trash'></i></a>";
                $html .= "<a href='javascript:;' title='View' onclick='setEdit($data->id," . '"view"' . ")' class='$iconClass'><i class='fa fa-eye'></i></a>";
                $html .= "</div>";
                return $html;
            })
            ->make();
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'country_id' => 'required|numeric',
            "title"      => 'required',
        ],['title.required'=>'The Name field is required.']);
        $id = $request->id;
        $branch = Branch::find($id);
        if ($branch) {
            $branch->update($request->all());
        } else {
            $inputs = $request->all();
            Branch::create($inputs);
        }
        return response()->json([
            "status"  => "success",
            "message" => "Saved successfully.",
        ]);
    }

    public function show($id)
    {
        $branch = Branch::find($id);
        $filteredArr = [
            'id'         => ["type" => "hidden", 'value' => $branch->id],
            'country_id' => ["type" => "select", 'value' => $branch->country_id],
            'title'      => ["type" => "text", 'value' => $branch->title],
            'title_nl'   => ["type" => "text", 'value' => $branch->title_nl],
            'title_es'   => ["type" => "text", 'value' => $branch->title_es],

        ];
        return response()->json([
            "status" => "success",
            "inputs" => $filteredArr,
        ]);
    }

    public function destroy($id)
    {
        $branch = Branch::find($id);
        $branch->delete();
        return response()->json([
            "status"  => "success",
            "message" => "Deleted successfully.",
        ]);
    }
}
