<?php

namespace App\Http\Controllers\Admin1;

use App\Http\Controllers\Controller;
use App\Models\ExistingLoanType;
use Yajra\DataTables\Facades\DataTables;

class ExistingLoanTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:super admin');
    }

    public function index()
    {
        $data = [];
        return view('admin1.pages.existingLoanType.index', $data);
    }

    public function store()
    {
        $this->validate(request(), ExistingLoanType::validationRules());
        $id = request('id');
        $type = ExistingLoanType::find($id);
        $inputs = request()->all();
        if ($type) {
            $type->update($inputs);
        } else {
            $type = ExistingLoanType::create($inputs);
        }
        $data = [];
        $data['status'] = true;
        return $data;
    }

    public function edit(ExistingLoanType $type)
    {
        $data = [];
        $data['inputs'] = [
            'id'       => ["type" => "hidden", 'value' => $type->id],
            'title'    => ["type" => "text", 'value' => $type->title],
            'title_nl' => ["type" => "text", 'value' => $type->title_nl],
            'title_es' => ["type" => "text", 'value' => $type->title_es],
        ];
        return $data;
    }

    public function destroy(ExistingLoanType $type)
    {
        $data = [];
        $data['status'] = $type->delete();
        return $data;
    }

    public function indexDatatable()
    {
        $loanType = ExistingLoanType::select('id', 'title', 'title_es', 'title_nl');
        return DataTables::of($loanType)
            ->addColumn('action', function ($row) {
                $iconClass = "btn btn-icon btn-sm btn-info waves-effect";
                $html = '<div class="button-list">';
                $html .= "<a href='javascript:;' title='Edit' data-toggle='tooltip' data-id='$row->id' class='$iconClass editType'>
                            <i class='fa fa-pencil'></i>
                        </a>";
                $html .= "<a href='javascript:;' title='Delete' data-toggle='tooltip' data-id='$row->id' class='$iconClass deleteType'><i class='fa fa-trash'></i></a>";
                $html .= "<a href='javascript:;' title='View' data-toggle='tooltip' class='$iconClass editType' data-id='$row->id' data-type='view'>
                            <i class='fa fa-eye'></i>
                        </a>";
                return $html;
            })
            ->make();
    }
}
