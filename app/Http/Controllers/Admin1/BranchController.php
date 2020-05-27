<?php

namespace App\Http\Controllers\Admin1;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Country;
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
        $data['countries'] = Country::pluck('name', 'id');
        return view('admin1.pages.branch.index', $data);
    }

    public function store()
    {
        $this->validate(request(), Branch::validationRules());
        $id = request('id');
        $branch = Branch::find($id);
        $inputs = request()->all();
        $inputs['title_es'] = request('title');
        $inputs['title_nl'] = request('title');
        if ($branch) {
            $branch->update($inputs);
        } else {
            $branch = Branch::create($inputs);
        }
        $data = [];
        $data['status'] = true;
        return $data;
    }

    public function edit(Branch $branch)
    {
        $data = [];
        $data['inputs'] = [
            'id'         => ["type" => "hidden", 'value' => $branch->id],
            'title'      => ["type" => "text", 'value' => $branch->title],
            'country_id' => ["type" => "select2", 'value' => $branch->country_id],
        ];
        return $data;
    }

    public function destroy(Branch $branch)
    {
        $data = [];
        $data['status'] = $branch->delete();
        return $data;
    }

    public function indexDatatable()
    {
        $loanType = Branch::select('branches.*', 'countries.name as country_name')
            ->leftJoin('countries', 'countries.id', '=', 'branches.country_id');
        return DataTables::of($loanType)
            ->addColumn('action', function ($row) {
                $iconClass = "btn btn-icon btn-sm btn-info waves-effect";
                $html = '<div class="button-list">';
                $html .= "<a href='javascript:;' title='Edit' data-toggle='tooltip' data-id='$row->id' class='$iconClass editBranch'>
                            <i class='fa fa-pencil'></i>
                        </a>";
                $html .= "<a href='javascript:;' title='Delete' data-toggle='tooltip' data-id='$row->id' class='$iconClass deleteBranch'><i class='fa fa-trash'></i></a>";
                $html .= "<a href='javascript:;' title='View' data-toggle='tooltip' class='$iconClass editBranch' data-id='$row->id' data-type='view'>
                            <i class='fa fa-eye'></i>
                        </a>";
                return $html;
            })
            ->make();
    }
}
