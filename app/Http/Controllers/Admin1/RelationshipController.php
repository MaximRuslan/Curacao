<?php

namespace App\Http\Controllers\Admin1;

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
        return view('admin1.pages.relationship.index');
    }

    public function edit(Relationship $relationship)
    {
        $data = [];

        $data['inputs'] = [
            'id'    => ["type" => "hidden", 'value' => $relationship->id],
            'title' => ["type" => "text", 'value' => $relationship->title],
        ];
        return $data;
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
        $data = [];
        $data['status'] = true;
        return $data;
    }

    public function destroy(Relationship $relationship)
    {
        $data = [];
        $data['status'] = $relationship->delete();
        return $data;
    }

    public function indexDatatable()
    {
        $loanStatus = Relationship::select('*');
        return DataTables::of($loanStatus)
            ->addColumn('action', function ($row) {
                $iconClass = "btn btn-icon btn-sm btn-info waves-effect";
                $html = '<div class="button-list">';
                $html .= "<a href='javascript:;' title='Edit' data-toggle='tooltip' data-id='$row->id' class='$iconClass editRelation'>
                            <i class='fa fa-pencil'></i>
                        </a>";
                $html .= "<a href='javascript:;' title='Delete' data-toggle='tooltip' data-id='$row->id' class='$iconClass deleteRelation'><i class='fa fa-trash'></i></a>";
                $html .= "<a href='javascript:;' title='View' data-toggle='tooltip' class='$iconClass editRelation' data-id='$row->id' data-type='view'>
                            <i class='fa fa-eye'></i>
                        </a>";
                $html .= "</div>";
                return $html;
            })
            ->make();
    }

}
