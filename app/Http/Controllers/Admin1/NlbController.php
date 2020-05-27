<?php

namespace App\Http\Controllers\Admin1;

use App\Http\Controllers\Controller;
use App\Library\Helper;
use App\Models\Branch;
use App\Models\Country;
use App\Models\Nlb;
use App\Models\NlbPaymentType;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class NlbController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:super admin|admin|processor');
    }

    public function index()
    {
        $data = [];

        if (auth()->user()->hasRole('super admin|admin|credit and processing')) {
            if (auth()->user()->hasRole('super admin')) {
                $data['branches'] = Branch::select('*');
                $country = session()->has('country') ? session()->get('country') : '';
                if ($country != '') {
                    $data['branches']->where('country_id', '=', $country);
                }
                $data['branches'] = $data['branches']->pluck('title', 'id');
                $data['countries'] = Country::pluck('name', 'id');
            } else if (auth()->user()->hasRole('admin')) {
                $data['branches'] = Branch::select('*');
                $data['branches']->where('country_id', '=', auth()->user()->country);
                $data['branches'] = $data['branches']->pluck('title', 'id');
            }
        }
        return view('admin1.pages.nlb.index', $data);
    }

    public function edit(Nlb $nlb)
    {
        $data = [];
        $format = config('site.date_format.php');
        $date = '';
        if ($nlb->date != null) {
            $date = date($format, strtotime($nlb->date));
        }
        $branch = Branch::find($nlb->branch_id);
        $data['inputs'] = [
            'id'        => ["type" => "hidden", 'value' => $nlb->id],
            'type'      => ["type" => "select2", 'value' => $nlb->type],
            'desc'      => ["type" => "textarea", 'value' => $nlb->desc],
            'date'      => ["type" => "text", 'value' => $date],
            'branch_id' => ["type" => "select2", "value" => $nlb->branch_id]
        ];
        if ($branch != null) {
            $data['inputs'] += [
                'country_id' => ["type" => "select2", "value" => $branch->country_id]
            ];
        }
        $data['reason'] = $nlb->reason;
        $data['amounts'] = NlbPaymentType::where('nlb_id', '=', $nlb->id)->pluck('amount', 'payment_type');

        return $data;
    }

    public function store()
    {
        $this->validate(request(), Nlb::validationRules());
        $id = request('id');
        $nlb = Nlb::find($id);
        $inputs = request()->all();
        $format = config('site.date_format.php');
        $date = '';
        if (request('date')) {
            $date = \DateTime::createFromFormat($format, request('date'));
            $date = $date->format('Y-m-d');
        } else if ($nlb != null) {
            $date = $nlb->date;
        }
        if ($nlb && auth()->user()->hasRole('super admin|admin')) {
            if (auth()->user()->hasRole('super admin|admin|auditor')) {
                $inputs['date'] = $date;
            }
            $nlb->update($inputs);
        } else {
            $branch_id = '';
            if (auth()->user()->hasRole('super admin|admin')) {
                $branch_id = request('branch_id');
            } else if (auth()->user()->hasRole('processor')) {
                $branch_id = session('branch_id');
            }
            $inputs['date'] = $date;
            $inputs['user_id'] = auth()->user()->id;
            $inputs['branch_id'] = $branch_id;
            $nlb = Nlb::create($inputs);
        }
        foreach (config('site.payment_types') as $key => $value) {
            NlbPaymentType::updateOrCreate([
                'nlb_id'       => $nlb->id,
                'payment_type' => $key
            ], [
                'amount' => request('amount.' . $key)
            ]);
        }
        $data = [];
        $data['status'] = true;
        return $data;
    }

    public function destroy(Nlb $nlb)
    {
        $data = [];
        $nlb->update([
            'deleted_by' => auth()->user()->id
        ]);
        $data['status'] = $nlb->delete();
        return $data;
    }

    public function indexDatatable()
    {
        $country = session()->has('country') ? session()->get('country') : '';

        $transactions = Nlb::select('nlbs.*', 'n_l_b_reasons.title as reason_name', 'branches.title as branch', 'branches.country_id',
            DB::raw('concat(users.firstname," ",users.lastname) as user_name'), DB::raw('sum(nlb_payment_types.amount) as amount'))
            ->leftJoin('users', 'users.id', '=', 'nlbs.user_id')
            ->leftJoin('branches', 'branches.id', '=', 'nlbs.branch_id')
            ->leftJoin('n_l_b_reasons', 'n_l_b_reasons.id', '=', 'nlbs.reason')
            ->leftJoin('nlb_payment_types', 'nlb_payment_types.nlb_id', '=', 'nlbs.id')
            ->groupBy('nlbs.id');

        if (auth()->user()->hasRole('super admin')) {
            if ($country != '') {
                $transactions->where('branches.country_id', '=', $country)
                    ->whereNotNull('branches.country_id');
            }
        } else {
            $transactions->where('branches.country_id', '=', auth()->user()->country)
                ->whereNotNull('branches.country_id');
        }
        if (session()->has('branch_id')) {
            $transactions->where('nlbs.branch_id', '=', session('branch_id'));
        }
        if (request('branch_id')) {
            $transactions->where('nlbs.branch_id', '=', request('branch_id'));
        }
        return DataTables::of($transactions)
            ->addColumn('amount', function ($row) {
                return Helper::decimalShowing($row->amount, $row->country_id);
            })
            ->addColumn('type', function ($row) {
                if ($row->type == 1) {
                    return "IN";
                } else if ($row->type == 2) {
                    return "OUT";
                }
            })
            ->addColumn('date', function ($row) {
                if ($row->date != null) {
                    return Helper::datebaseToFrontDate($row->date);
                }
            })
            ->addColumn('action', function ($row) {
                $iconClass = "btn btn-icon btn-sm btn-info waves-effect";
                $html = '<div class="button-list">';
                if (auth()->user()->hasRole('super admin|admin')) {
                    $html .= "<a href='javascript:;' title='Edit' data-toggle='tooltip' data-id='$row->id' class='$iconClass editNlb'>
                            <i class='fa fa-pencil'></i>
                        </a>";
                    $html .= "<a href='javascript:;' title='Delete' data-toggle='tooltip' data-id='$row->id' class='$iconClass deleteNlb'><i class='fa fa-trash'></i></a>";
                }
                $html .= "<a href='javascript:;' title='View' data-toggle='tooltip' class='$iconClass editNlb' data-id='$row->id' data-type='view'>
                            <i class='fa fa-eye'></i>
                        </a>";
                $html .= "</div>";
                return $html;
            })
            ->make();
    }
}
