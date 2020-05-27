<?php

namespace App\Http\Controllers\Admin1;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\UserTerritory;
use Yajra\DataTables\Facades\DataTables;

class DistrictController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:super admin');
    }

    public function index()
    {
        $data = [];
        $data['countries'] = Country::pluck('name', 'id');
        return view('admin1.pages.district.index', $data);
    }

    public function store()
    {
        $this->validate(request(), UserTerritory::validationRules());
        $id = request('id');
        $district = UserTerritory::find($id);
        $inputs = request()->all();
        $inputs['title_es'] = request('title');
        $inputs['title_nl'] = request('title');
        if ($district) {
            $district->update($inputs);
        } else {
            $district = UserTerritory::create($inputs);
        }
        $data = [];
        $data['status'] = true;
        return $data;
    }

    public function edit(UserTerritory $district)
    {
        $data = [];
        $data['inputs'] = [
            'id'         => ["type" => "hidden", 'value' => $district->id],
            'title'      => ["type" => "text", 'value' => $district->title],
            'country_id' => ["type" => "select2", 'value' => $district->country_id],
        ];
        return $data;
    }

    public function destroy(UserTerritory $district)
    {
        $data = [];
        $data['status'] = $district->delete();
        return $data;
    }

    public function indexDatatable()
    {
        $loanType = UserTerritory::select('user_territories.*', 'countries.name as country')
            ->leftJoin('countries', 'countries.id', '=', 'user_territories.country_id');
        return DataTables::of($loanType)
            ->addColumn('action', function ($row) {
                $iconClass = "btn btn-icon btn-sm btn-info waves-effect";
                $html = '<div class="button-list">';
                $html .= "<a href='javascript:;' title='Edit' data-toggle='tooltip' data-id='$row->id' class='$iconClass editDistrict'>
                            <i class='fa fa-pencil'></i>
                        </a>";
                $html .= "<a href='javascript:;' title='Delete' data-toggle='tooltip' data-id='$row->id' class='$iconClass deleteDistrict'><i class='fa fa-trash'></i></a>";
                $html .= "<a href='javascript:;' title='View' data-toggle='tooltip' class='$iconClass editDistrict' data-id='$row->id' data-type='view'>
                            <i class='fa fa-eye'></i>
                        </a>";
                return $html;
            })
            ->make();
    }
}
