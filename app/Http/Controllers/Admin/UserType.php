<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use DataTables;
use Illuminate\Http\Request;

class UserType extends Controller
{
    //
    public function index()
    {
        return view('admin.usertype.index');
    }

    public function getList()
    {
        $roles = Role::select([
            'id',
            'name',
            'name_es',
            'name_nl',
        ])
            ->where('name', '!=', 'super admin');
        return DataTables::of($roles)
            ->addColumn('action', function ($data) {
                $iconClass = "btn btn-icon btn-sm btn-info waves-effect";
                $html = '<div class="button-list">';
                $html .= "<a href='javascript:;' title='Edit' onclick='setEdit($data->id)' class='$iconClass'><i class='fa fa-pencil'></i></a>";
                $html .= "<a href='javascript:;' title='Delete' data-modal-id='deleteRole' data-id='$data->id' onclick='DeleteConfirm(this)' class='$iconClass'><i class='fa fa-trash'></i></a>";
                $html .= "<a href='javascript:;' title='View' onclick='setEdit($data->id," . '"view"' . ")' class='$iconClass'><i class='fa fa-eye'></i></a>";
                $html .= "</div>";
                return $html;
            })
            ->removeColumn('id')
            ->make();
    }

    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        //
        $this->validate($request, [
            "name" => 'required',
        ]);
        $id = $request->id;
        $role = Role::find($id);
        if ($role) {
            $role->update($request->all());
        } else {
            $inputs = $request->all();
            $inputs['guard_name'] = 'web';
            Role::create($inputs);
        }
        return response()->json([
            "status"  => "success",
            "message" => "Saved successfully.",
        ]);
    }


    public function show($id)
    {
        //
        $role = Role::find($id);
        $filteredArr = [
            'id'      => ["type" => "hidden", 'value' => $role->id],
            'name'    => ["type" => "text", 'value' => $role->name],
            'name_nl' => ["type" => "text", 'value' => $role->name_nl],
            'name_es' => ["type" => "text", 'value' => $role->name_es],
        ];
        return response()->json([
            "status" => "success",
            "inputs" => $filteredArr,
        ]);
    }


    public function edit($id)
    {
        //
    }


    public function update(Request $request, $id)
    {
        //
    }


    public function destroy($id)
    {
        //
        $role = Role::find($id);
        $role->delete();
        return response()->json([
            "status"  => "success",
            "message" => "Saved successfully.",
        ]);
    }
}
