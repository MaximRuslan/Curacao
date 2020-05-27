<?php

namespace App\Http\Controllers\Admin1;

use App\Http\Controllers\Controller;
use App\Models\NLBReason;
use Yajra\DataTables\Facades\DataTables;

class NlbReasonController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:super admin')->except('typeReasonsListing');
    }

    public function index()
    {
        return view('admin1.pages.nlbReason.index');
    }

    public function edit(NLBReason $reason)
    {
        $data = [];

        $data['inputs'] = [
            'id'    => ["type" => "hidden", 'value' => $reason->id],
            'type'  => ["type" => "select2", 'value' => $reason->type],
            'title' => ["type" => "text", 'value' => $reason->title],
        ];
        return $data;
    }

    public function store()
    {
        $this->validate(request(), NLBReason::validationRules());
        $id = request('id');
        $reason = NLBReason::find($id);
        if ($reason) {
            $reason->update(request()->all());
        } else {
            $reason = NLBReason::create(request()->all());
        }
        $data = [];
        $data['status'] = true;
        return $data;
    }

    public function destroy(NLBReason $reason)
    {
        $data = [];
        $data['status'] = $reason->delete();
        return $data;
    }

    public function indexDatatable()
    {
        $loanStatus = NLBReason::select('*');
        return DataTables::of($loanStatus)
            ->addColumn('type', function ($row) {
                if ($row->type == 1) {
                    return "IN";
                } elseif ($row->type == 2) {
                    return "OUT";
                }
            })
            ->addColumn('action', function ($row) {
                $iconClass = "btn btn-icon btn-sm btn-info waves-effect";
                $html = '<div class="button-list">';
                $html .= "<a href='javascript:;' title='Edit' data-toggle='tooltip' data-id='$row->id' class='$iconClass editReason'>
                            <i class='fa fa-pencil'></i>
                        </a>";
                $html .= "<a href='javascript:;' title='Delete' data-toggle='tooltip' data-id='$row->id' class='$iconClass deleteReason'><i class='fa fa-trash'></i></a>";
                $html .= "<a href='javascript:;' title='View' data-toggle='tooltip' class='$iconClass editReason' data-id='$row->id' data-type='view'>
                            <i class='fa fa-eye'></i>
                        </a>";
                $html .= "</div>";
                return $html;
            })
            ->make();
    }


    public function typeReasonsListing($type)
    {
        $data = [];
        $data['reasons'] = NLBReason::where('type', '=', $type)->pluck('title', 'id');
        return $data;
    }
}
