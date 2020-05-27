<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Nlb;
use App\Models\NlbPaymentType;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class NlbController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:super admin|processor');
    }

    public function index()
    {
        $data = [];
        if (auth()->user()->hasRole('super admin|credit and processing')) {
            $data['branches'] = Branch::select('*');
            if (auth()->user()->hasRole('super admin')) {
                $country = session()->has('country') ? session()->get('country') : '';
            } else {
                $country = auth()->user()->country;
            }
            if ($country != '') {
                $data['branches']->where('country_id', '=', $country);
            }
            $data['branches'] = $data['branches']->pluck('title', 'id');
        }
        return view('admin.nlb-transactions.index', $data);
    }

    public function store()
    {
        $this->validate(request(), Nlb::validationRules());
        $id = request('id');
        $transaction = Nlb::find($id);
        $inputs = request()->only([
            'type',
            'reason',
            'desc',
        ]);
        if ($transaction) {
            $transaction->update($inputs);
        } else {
            $inputs['user_id'] = auth()->user()->id;
            $inputs['branch_id'] = session('branch_id');
            $transaction = Nlb::create($inputs);
        }
        foreach (config('site.payment_types') as $key => $value) {
            NlbPaymentType::updateOrCreate([
                'nlb_id'       => $transaction->id,
                'payment_type' => $key
            ], [
                'amount' => request('amount.' . $key)
            ]);
        }
        return response()->json([
            "status"  => "success",
            "message" => "Saved successfully.",
        ]);
    }

    public function show(Nlb $nlb_transaction)
    {
        $filteredArr = [
            'id'   => ["type" => "hidden", 'value' => $nlb_transaction->id],
            'type' => ["type" => "select", 'value' => $nlb_transaction->type],
            'desc' => ["type" => "textarea", 'value' => $nlb_transaction->desc],
        ];
        $reason = $nlb_transaction->reason;
        $amounts = NlbPaymentType::where('nlb_id', '=', $nlb_transaction->id)->pluck('amount', 'payment_type');
        return response()->json([
            "status"  => "success",
            "inputs"  => $filteredArr,
            'reason'  => $reason,
            'amounts' => $amounts
        ]);
    }

    public function indexDatatable()
    {
        $country = session()->has('country') ? session()->get('country') : '';

        $transactions = Nlb::select('nlbs.*', 'n_l_b_reasons.title as reason_name', 'branches.title as branch',
            DB::raw('concat(users.firstname," ",users.lastname) as user_name'), DB::raw('sum(nlb_payment_types.amount) as amount'))
            ->leftJoin('users', 'users.id', '=', 'nlbs.user_id')
            ->leftJoin('branches', 'branches.id', '=', 'nlbs.branch_id')
            ->leftJoin('n_l_b_reasons', 'n_l_b_reasons.id', '=', 'nlbs.reason')
            ->leftJoin('nlb_payment_types', 'nlb_payment_types.nlb_id', '=', 'nlbs.id')
            ->groupBy('nlbs.id');
        if (auth()->user()->hasRole('super admin')) {
            if ($country != '') {
                $transactions->where('branches.country_id', '=', $country)->whereNotNull('branches.country_id');
            }
        } else {
            $transactions->where('branches.country_id', '=', auth()->user()->country)->whereNotNull('branches.country_id');
        }
        if (session()->has('branch_id')) {
            $transactions->where('nlbs.branch_id', '=', session('branch_id'));
        }
        if (request('branch_id')) {
            $transactions->where('nlbs.branch_id', '=', request('branch_id'));
        }
        return DataTables::of($transactions)
            ->addColumn('type', function ($row) {
                if ($row->type == 1) {
                    return "IN";
                } elseif ($row->type == 2) {
                    return "OUT";
                }
            })
            ->addColumn('action', function ($data) {
                $iconClass = "btn btn-icon btn-sm btn-info waves-effect";
                $html = '<div class="button-list">';
                $html .= "<a href='javascript:;' title='Edit' onclick='setEdit($data->id)' class='$iconClass'>
                                <i class='fa fa-pencil'></i>
                          </a>";
                $html .= "<a href='javascript:;' title='Delete' data-modal-id='deleteNlb' data-id='$data->id' 
                            onclick='DeleteConfirm(this)' class='$iconClass'>
                                <i class='fa fa-trash'></i>
                          </a>";
                $html .= "<a href='javascript:;' title='View' onclick='setEdit($data->id," . '"view"' . ")' 
                            class='$iconClass'>
                                <i class='fa fa-eye'></i>
                          </a>";
                $html .= "</div>";
                return $html;
            })
            ->make();
    }

    public function destroy(Nlb $nlb_transaction)
    {
        $nlb_transaction->delete();
        NlbPaymentType::where('nlb_id', '=', $nlb_transaction->id)->delete();
        return response()->json([
            "status"  => "success",
            "message" => "Deleted successfully.",
        ]);
    }
}
