<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Relationship;
use Yajra\DataTables\Facades\DataTables;

class RelationshipController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:super admin');
    }

    public function index()
    {
        $data = [];
        return view('admin.relationship.index', $data);
    }

    public function store()
    {
        $this->validate(request(), Relationship::validationRules());
        $id = request('id');
        $relationship = Relationship::find($id);
        if ($relationship) {
            $relationship->update(request()->all());
        } else {
            $relationship = Relationship::create(request()->all());
        }
        return response()->json([
            "status"  => "success",
            "message" => "Saved successfully.",
        ]);
    }

    public function show(Relationship $relationship)
    {
        $filteredArr = [
            'id'    => ["type" => "hidden", 'value' => $relationship->id],
            'title' => ["type" => "text", 'value' => $relationship->title],
        ];
        return response()->json([
            "status" => "success",
            "inputs" => $filteredArr,
        ]);
    }

    public function indexDatatable()
    {
        $loanStatus = Relationship::select('*');
        return DataTables::of($loanStatus)
            ->addColumn('action', function ($data) {
                $iconClass = "btn btn-icon btn-sm btn-info waves-effect";
                $html = '<div class="button-list">';
                $html .= "<a href='javascript:;' title='Edit' onclick='setEdit($data->id)' class='$iconClass'><i class='fa fa-pencil'></i></a>";
                $html .= "<a href='javascript:;' title='Delete' data-modal-id='deleteRelationship' data-id='$data->id' onclick='DeleteConfirm(this)' class='$iconClass'><i class='fa fa-trash'></i></a>";
                $html .= "<a href='javascript:;' title='View' onclick='setEdit($data->id," . '"view"' . ")' class='$iconClass'><i class='fa fa-eye'></i></a>";
                $html .= "</div>";
                return $html;
            })
            ->make();
    }

    public function destroy(Relationship $relationship)
    {
        $relationship->delete();
        return response()->json([
            "status"  => "success",
            "message" => "Deleted successfully.",
        ]);
    }
}
