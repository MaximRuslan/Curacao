<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Library\Helper;
use App\Models\MerchantReconciliation;
use App\Models\MerchantReconciliationHistory;
use Yajra\DataTables\Facades\DataTables;

class ReconciliationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth_merchant');
        $this->middleware('auth_merchant_reconciliation');
    }

    public function index()
    {
        $data = [];
        return view('merchant.pages.reconciliations.index', $data);
    }

    public function approve()
    {
        $reconciliation = MerchantReconciliation::find(request('id'));
        $this->validate(request(), [
            'id'  => 'required',
            'otp' => 'required'
        ]);
        $data = [];
        if ($reconciliation != null) {
            if ($reconciliation->otp == request('otp')) {
                $reconciliation->update([
                    'status' => '2'
                ]);
                MerchantReconciliationHistory::addStatusHistory($reconciliation->id, 2, 'merchant', Helper::authMerchantUser()->id);
            } else {
                return response()->json([
                    'errors' => [
                        'otp' => [__('keywords.entered_otp_wrong')]
                    ]
                ], 422);
            }
        } else {
            return response()->json([
                'errors' => [
                    'otp' => [__('keywords.something_went_wrong')]
                ]
            ], 422);
        }
        return $data;
    }

    public function indexDatatable()
    {
        $data = MerchantReconciliation::select('merchant_reconciliations.*', 'merchant_branches.name as branch')
            ->leftJoin('merchant_branches', 'merchant_branches.id', '=', 'merchant_reconciliations.branch_id');

        $data->where('merchant_reconciliations.merchant_id', '=', Helper::getMerchantId());
        if (Helper::authMerchantUser()->type == 2) {
            $data->where('merchant_reconciliations.branch_id', '=', Helper::authMerchantUser()->branch_id);
        }
        if (session()->has('branch_id')) {
            $data->where('merchant_reconciliations.branch_id', '=', session('branch_id'));
        }

        return DataTables::of($data)
            ->addColumn('date', function ($row) {
                return Helper::date_time_to_current_timezone($row->created_at);
            })
            ->addColumn('status', function ($row) {
                if ($row->status != null) {
                    return config('site.reconciliation_status')[$row->status];
                }
            })
            ->addColumn('action', function ($row) {
                $iconClass = "btn btn-icon btn-sm btn-info waves-effect";
                $html = '<div class="button-list">';
                if ($row->status == 1) {
                    $html .= "<a href='javascript:;' title='" . __('keywords.approve') . "' data-toggle='tooltip' data-id='$row->id' class='$iconClass js--reconciliation-approve-button'>
                            <i class='fa fa-check'></i>
                        </a>";
                }
                $html .= "<a href='javascript:;' title='" . __('keywords.history') . "' data-toggle='tooltip' data-id='$row->id' class='$iconClass js--reconciliation-history-button'>
                            <i class='fa fa-history'></i>
                        </a>";
                $html .= '</div>';
                return $html;
            })
            ->addIndexColumn()
            ->make(true);
    }

    public function history(MerchantReconciliation $reconciliation)
    {
        $data = [];

        $history = MerchantReconciliationHistory::select('merchant_reconciliation_histories.*', 'users.firstname', 'users.lastname', 'merchants.name',
            'merchants.first_name', 'merchants.last_name', 'main.name as merchant_name', 'merchants.type as merchant_type')
            ->leftJoin('users', 'users.id', '=', 'merchant_reconciliation_histories.user_id')
            ->leftJoin('merchants', 'merchants.id', '=', 'merchant_reconciliation_histories.user_id')
            ->leftJoin('merchants as main', 'main.id', '=', 'merchants.merchant_id')
            ->where('merchant_reconciliation_histories.merchant_reconciliation_id', '=', $reconciliation->id)
            ->orderBy('merchant_reconciliation_histories.id', 'asc')
            ->get();
        $history = $history->map(function ($item, $key) {
            if ($item->type == 'user') {
                $item->username = $item->firstname . ' ' . $item->lastname;
            } else {
                if ($item->merchant_type == 1) {
                    $item->username = $item->first_name . ' ' . $item->last_name . ' (' . $item->name . ')';
                } else {
                    $item->username = $item->first_name . ' ' . $item->last_name . ' (' . $item->merchant_name . ')';
                }
            }
            $item->date_time = Helper::date_time_to_current_timezone($item->created_at);
            $item->status = config('site.reconciliation_status')[$item->status];
            return $item;
        });

        $data['history'] = $history;

        return $data;
    }
}
