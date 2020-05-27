<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Tax;
use App\Models\UserTerritory;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class UserTerritoryController extends Controller
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
        return view('admin.userterritory.index', $data);
    }

    public function getList()
    {
        $territory = UserTerritory::select('user_territories.*', 'countries.name as country')
            ->leftJoin('countries', 'countries.id', '=', 'user_territories.country_id');
        return DataTables::of($territory)
            ->addColumn('action', function ($data) {
                $iconClass = "btn btn-icon btn-sm btn-info waves-effect";
                $html = '<div class="button-list">';
                $html .= "<a href='javascript:;' title='Edit' onclick='setEdit($data->id)' class='$iconClass'><i class='fa fa-pencil'></i></a>";
                $html .= "<a href='javascript:;' title='Delete' data-modal-id='deleteTerritory' data-id='$data->id' onclick='DeleteConfirm(this)' class='$iconClass'><i class='fa fa-trash'></i></a>";
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
        ]);
        $id = $request->id;
        $territory = UserTerritory::find($id);
        if ($territory) {
            $territory->update($request->all());
        } else {
            $inputs = $request->all();
            UserTerritory::create($inputs);
        }
        return response()->json([
            "status"  => "success",
            "message" => "Saved successfully.",
        ]);
    }

    public function show($id)
    {
        $territory = UserTerritory::find($id);
        $filteredArr = [
            'id'         => ["type" => "hidden", 'value' => $territory->id],
            'country_id' => ["type" => "select", 'value' => $territory->country_id],
            'title'      => ["type" => "text", 'value' => $territory->title],
            'title_nl'   => ["type" => "text", 'value' => $territory->title_nl],
            'title_es'   => ["type" => "text", 'value' => $territory->title_es],

        ];
        return response()->json([
            "status" => "success",
            "inputs" => $filteredArr,
        ]);
    }

    public function destroy($id)
    {
        $territory = UserTerritory::find($id);
        $territory->delete();
        return response()->json([
            "status"  => "success",
            "message" => "Deleted successfully.",
        ]);
    }
}
