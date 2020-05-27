<?php

namespace App\Http\Controllers\Admin1;

use App\Http\Controllers\Controller;
use App\Library\Helper;
use App\Models\Branch;
use App\Models\Country;
use App\Models\Credit;
use App\Models\Dayopen;
use App\Models\LoanTransaction;
use App\Models\Nlb;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class DailyTurnoverController extends Controller
{
    public function __construct()
    {
        /**
         * @desc day open middleware
         * @date 19 Jun 2018 12:30
         */
        $this->middleware('role:super admin|admin|processor|credit and processing')->only([
            'dayopenIndex',
            'dayOpenDatatable',
            'dayOpenStore',
            'dayOpenEdit',
            'dayOpenShow',
            'branches',
        ]);

        /**
         * @desc audit approve and see middelware
         * @date 19 Jun 2018 12:31
         */
        $this->middleware('role:super admin|auditor')->only([
            'auditIndex',
            'auditShow',
            'auditApprove',
            'auditDatatable',
        ]);
    }

    /**
     * @desc day open code
     * @date 19 Jun 2018 12:29
     */
    public function dayopenIndex()
    {
        $data = [];
        if (request('type') && request('type') != '2') {
            if (request('type') == 1) {
                $data['payment_types'] = collect(config('site.payment_types'))->only([6, 4, 2, 3]);
            } else if (request('type') == 2) {
                $data['payment_types'] = collect(config('site.payment_types'))->only([5]);
            } else if (request('type') == 3) {
                $data['payment_types'] = collect(config('site.payment_types'))->only([1]);
            }
            if (auth()->user()->hasRole('super admin')) {
                $data['branches'] = Branch::select('*');
                $country = session()->has('country') ? session()->get('country') : '';
                if ($country != '') {
                    $data['branches']->where('country_id', '=', $country);
                }
                $data['branches'] = $data['branches']->pluck('title', 'id');
                $data['countries'] = Country::pluck('name', 'id');
            } else if (auth()->user()->hasRole('admin') && (request('type') == 1 || request('type') == 3)) {
                $data['branches'] = Branch::select('*');
                $data['branches']->where('country_id', '=', auth()->user()->country);
                $data['branches'] = $data['branches']->pluck('title', 'id');
            } else if (auth()->user()->hasRole('processor') && request('type') == 1) {

            } else if (auth()->user()->hasRole('credit and processing') && request('type') == 3) {
                $data['branches'] = Branch::select('*');

                $data['branches']->where('country_id', '=', auth()->user()->country);

                $data['branches'] = $data['branches']->pluck('title', 'id');
            } else {
                return redirect()->route('admin1.permission-denied');
            }
            return view('admin1.pages.dailyturnovers.dayopen', $data);
        } else {
            return redirect()->route('admin1.permission-denied');
        }
    }

    public function dayopenCreate()
    {
        $data = [];
        $last_verified_dayopen = Dayopen::whereNotNull('completion_date')
            ->where('branch_id', '=', session('branch_id'))
            ->orderBy('date', 'desc')
            ->first();
        $start_date = null;
        if ($last_verified_dayopen != null) {
            $start_date = Dayopen::where('date', '>', $last_verified_dayopen->date)
                ->whereNull('completion_date')
                ->where('branch_id', '=', session('branch_id'))
                ->orderBy('date', 'asc')
                ->first();
        }
        if ($start_date != null) {
            $data['startDate'] = $start_date->date;
        }
        return $data;
    }

    public function dayOpenDatatable()
    {
        if (request('type') == 1) {
            $payment_types = [6, 4, 2, 3];
        } else if (request('type') == 2) {
            $payment_types = [5];
        } else if (request('type') == 3) {
            $payment_types = [1];
        }
        $dayopen = Dayopen::select('dayopens.date', 'dayopens.custom_created_at', DB::raw('sum(amount) as total_amount'),
            DB::raw('concat(users.firstname," ",users.lastname) as username'), 'branches.title as branch_name', 'branches.country_id', 'dayopens.user_id', 'dayopens.branch_id')
            ->whereIn('dayopens.payment_type', $payment_types);
        $dayopen->leftJoin('users', function ($join) {
            $join->on('users.id', '=', 'dayopens.user_id')
                ->whereNull('users.deleted_at');
        });
        $dayopen->leftJoin('branches', 'branches.id', '=', 'dayopens.branch_id');
        $dayopen->groupBy('dayopens.date', DB::raw('dayopens.custom_created_at'), 'dayopens.user_id', 'dayopens.branch_id');
        if (auth()->user()->hasRole('super admin')) {
            if (session()->has('country')) {
                $dayopen->where('branches.country_id', '=', session('country'));
            }
            if (request('branch_id')) {
                $dayopen->where('dayopens.branch_id', '=', request('branch_id'));
            }
        } else {
            if (session()->has('branch_id')) {
                $dayopen->where('dayopens.branch_id', '=', session('branch_id'));
            }
        }

        return DataTables::of($dayopen)
            ->addColumn('total_amount', function ($row) {
                return Helper::decimalShowing($row->total_amount, $row->country_id);
            })
            ->addColumn('date', function ($row) {
                return Helper::datebaseToFrontDate($row->date);
            })
            ->addColumn('custom_created_at', function ($row) {
                if ($row->custom_created_at != null) {
                    return Helper::date_time_to_current_timezone($row->custom_created_at);
                }
            })
            ->addColumn('action', function ($row) {
                $iconClass = "btn btn-icon btn-sm btn-info waves-effect";
                $html = '<div class="button-list">';
                if (auth()->user()->hasRole('super admin')) {
                    $html .= "<a href='#' title='Edit' class='$iconClass editDayOpenButton'
                                    data-date='$row->date' data-user='$row->user_id' data-branch='$row->branch_id'>
                                <i class='fa fa-pencil'></i>
                            </a>";
                }
//                $html .= "<a href='javascript:;' title='Delete' data-modal-id='deleteDailyTurnover' data-id='$data->id' onclick='DeleteConfirm(this)' class='$iconClass'><i class='fa fa-trash'></i></a>";
                $html .= "<a href='javascript:;' title='View' data-date='" . $row->date . "' data-user='$row->user_id' data-branch='$row->branch_id' class='$iconClass viewDayopen'>
                            <i class='fa fa-eye'></i>
                          </a>";
                $html .= "</div>";
                return $html;
            })
            ->make(true);
    }

    public function dayopenStore()
    {
        $data = [];
        $format = config('site.date_format.php');
        $date = \DateTime::createFromFormat($format, request('date'));
        $date = $date->format('Y-m-d');
        if (request('old_date') != '') {
            $old_date = \DateTime::createFromFormat($format, request('old_date'));
            $old_date = $old_date->format('Y-m-d');
        }
        if (request('type') == 1) {
            $payment_types = [6, 4, 2, 3];
        } else if (request('type') == 2) {
            $payment_types = [5];
        } else if (request('type') == 3) {
            $payment_types = [1];
        }
        $created_at = date('Y-m-d H:i:s');
        if (request('branch') != '' && request('old_date') != '' && auth()->user()->hasRole('super admin')) {
            $dayopen = Dayopen::where('date', '=', $old_date)
                ->where('branch_id', '=', request('branch'))
                ->whereIn('payment_type', $payment_types)
                ->get()
                ->keyBy('payment_type');
            if ($date != $old_date) {
                $current_dayopen = Dayopen::where('date', '=', $date)
                    ->where('branch_id', '=', request('branch_id'))
                    ->whereIn('payment_type', $payment_types)
                    ->get();
            }
            if (!isset($current_dayopen) || $current_dayopen->count() <= 0) {
                foreach (request('amount') as $key => $value) {
                    if (isset($dayopen[$key])) {
                        $dayopen[$key]->update([
                            'amount'            => $value,
                            'date'              => $date,
                            'branch_id'         => request('branch_id'),
                            'custom_created_at' => $created_at
                        ]);
                    } else {
                        if ($value != '' && $value > 0) {
                            Dayopen::create([
                                'user_id'           => request('user'),
                                'branch_id'         => request('branch_id'),
                                'payment_type'      => $key,
                                'amount'            => $value,
                                'date'              => $date,
                                'custom_created_at' => $created_at
                            ]);
                        }
                    }
                }
                $previous_day_open = Dayopen::where('date', '<', $date)
                    ->where('branch_id', '=', request('branch'))
                    ->whereIn('payment_type', $payment_types)
                    ->orderBy('date', 'desc')
                    ->first();
                if ($previous_day_open != null) {
                    $date = $previous_day_open->date;
                }
                Dayopen::where('date', '>=', $date)
                    ->where('branch_id', '=', request('branch'))
                    ->whereIn('payment_type', $payment_types)
                    ->update([
                        'completion_date' => null,
                        'verified_by'     => null
                    ]);
                $data['status'] = true;
            } else {
                $data['status'] = false;
                $data['message'] = 'This date is already entered for this branch.';
            }
        } else if (request('branch') == '' && request('old_date') == '') {
            $branch_id = '';
            if (auth()->user()->hasRole('super admin|admin|credit and processing')) {
                $branch_id = request('branch_id');
            } else if (auth()->user()->hasRole('processor')) {
                $branch_id = session('branch_id');
            }
            $dayopen = Dayopen::where('date', '=', $date)
                ->where('branch_id', '=', $branch_id)
                ->whereIn('payment_type', $payment_types)
                ->get();
            $last_verified_dayopen = Dayopen::whereNotNull('completion_date')
                ->where('branch_id', '=', $branch_id)
                ->whereIn('payment_type', $payment_types)
                ->orderBy('date', 'desc')
                ->first();

            if ($last_verified_dayopen != null) {
                $last_date = Dayopen::where('date', '>', $last_verified_dayopen->date)
                    ->whereNull('completion_date')
                    ->where('branch_id', '=', $branch_id)
                    ->whereIn('payment_type', $payment_types)
                    ->orderBy('date', 'asc')
                    ->first();
            }
            if ($dayopen->count() <= 0 && (!isset($last_date) || $last_date->date < $date)) {
                $this->validate(request(), Dayopen::validationRules());

                foreach (request('amount') as $key => $value) {
                    if ($value != '' && $value > 0) {
                        Dayopen::create([
                            'user_id'           => auth()->user()->id,
                            'branch_id'         => $branch_id,
                            'payment_type'      => $key,
                            'amount'            => $value,
                            'date'              => $date,
                            'custom_created_at' => $created_at
                        ]);
                    }
                }
                $data['status'] = true;
            } else {
                $data['status'] = false;
                $data['message'] = 'This date is already entered for this branch.';
            }
        }
        return $data;
    }

    public function dayOpenEdit($date, $user, $branch)
    {
        $data = [];
        $data['dayopens'] = Dayopen::where('date', '=', $date)
            ->where('branch_id', '=', $branch)
            ->get();
        $branch = Branch::find($branch);
        $data['branch'] = $branch->title;
        $data['country'] = $branch->country_id;
        return $data;
    }

    public function branches(Country $country)
    {
        $data = [];

        $data['branches'] = Branch::where('country_id', '=', $country->id)
            ->orderBy('title', 'asc')
            ->pluck('title', 'id');

        return $data;
    }

    public function auditIndex()
    {
        if (request('type')) {
            $data = [];
            if (request('type') == 1) {
                $data['payment_types'] = collect(config('site.payment_types'))->only([6, 4, 2, 3]);
            } else if (request('type') == 2) {
                $data['payment_types'] = collect(config('site.payment_types'))->only([5]);
            } else if (request('type') == 3) {
                $data['payment_types'] = collect(config('site.payment_types'))->only([1]);
            }
            if (auth()->user()->hasRole('super admin')) {
                $data['branches'] = Branch::select('*');
                $country = session()->has('country') ? session()->get('country') : '';
                if ($country != '') {
                    $data['branches']->where('country_id', '=', $country);
                }
                $data['branches'] = $data['branches']->pluck('title', 'id');
            } else {
                $dayopen = Dayopen::where('date', '=', date('Y-m-d'))->get();
                if ($dayopen->count() > 0) {
                    $data['today_dayopen'] = false;
                } else {
                    $data['today_dayopen'] = true;
                }
            }
            return view('admin1.pages.dailyturnovers.audit', $data);
        } else {
            return redirect()->route('admin1.permission-denied');
        }
    }

    public function auditShow($date, $user, $branch)
    {
        $data = [];
        $data['date'] = Helper::datebaseToFrontDate($date);
        $data['user'] = $user;
        $data['branch'] = Branch::find($branch);
        $dayopen = Dayopen::where('user_id', '=', $user)
            ->whereDate('date', '=', $date)
            ->where('branch_id', '=', $branch)
            ->first();
        $data['approved'] = false;
        if ($dayopen != null && $dayopen->completion_date != null) {
            $data['approved'] = true;
        }
        if (request('type') == 1) {
            $payment_types = [6, 4, 2, 3];
        } else if (request('type') == 2) {
            $payment_types = [5];
        } else if (request('type') == 3) {
            $payment_types = [1];
        }

        $data['dayopens'] = Dayopen::whereDate('date', '=', $date)
            ->where('branch_id', '=', $branch)
            ->whereIn('payment_type', $payment_types)
            ->pluck('amount', 'payment_type');

        $end_date = Dayopen::where('date', '>', $date)
            ->where('branch_id', '=', $branch)
            ->whereIn('payment_type', $payment_types)
            ->orderBy('date', 'asc')
            ->first();

        $data['loan_transactions'] = LoanTransaction::select('payment_type', DB::raw('sum(amount - cash_back_amount) as amount'))
            ->where('payment_date', '=', $date)
            ->where('branch_id', '=', $branch)
            ->whereIn('payment_type', $payment_types)
            ->groupBy('payment_type')
            ->pluck('amount', 'payment_type');

        $data['credits'] = Credit::select('payment_type', DB::raw('sum(amount) as amount'))
            ->whereDate('created_at', '=', $date)
            ->where('branch_id', '=', $branch)
            ->whereIn('payment_type', $payment_types)
            ->groupBy('payment_type')
            ->pluck('amount', 'payment_type');

        $data['nlb'] = Nlb::select('nlbs.type', 'nlbs.id', 'nlb_payment_types.payment_type', DB::raw('sum(amount) as amount'))
            ->leftJoin('nlb_payment_types', 'nlb_payment_types.nlb_id', '=', 'nlbs.id')
            ->where('nlbs.branch_id', '=', $branch)
            ->whereDate('nlbs.date', '=', $date)
            ->whereIn('payment_type', $payment_types)
            ->groupBy('nlbs.id', 'nlb_payment_types.payment_type')
            ->get();

        $data['nlb_in'] = $data['nlb']->where('type', '=', 1)->pluck('amount', 'payment_type');
        $data['nlb_out'] = $data['nlb']->where('type', '=', 2)->pluck('amount', 'payment_type');

        $date = null;
        $data['is_eligible'] = false;
        $data['end_date'] = '';
        if ($end_date != null) {
            $data['is_eligible'] = true;
            $date = $end_date->date;
            $data['end_date'] = Helper::datebaseToFrontDate($date);
        }

        $data['next_date_dayopens'] = Dayopen::whereDate('date', '=', $date)
            ->where('branch_id', '=', $branch)
            ->whereIn('payment_type', $payment_types)
            ->pluck('amount', 'payment_type');

        $data['difference'] = [];
        $data['in_amount'] = [];
        $data['out_amount'] = [];
        foreach ($payment_types as $key => $value) {

            if (isset($data['loan_transactions'][$value]) && isset($data['nlb_in'][$value])) {
                $data['in_amount'][$value] = round($data['loan_transactions'][$value], 2) + round($data['nlb_in'][$value], 2);
            } else if (isset($data['loan_transactions'][$value])) {
                $data['in_amount'][$value] = $data['loan_transactions'][$value];
            } else if (isset($data['nlb_in'][$value])) {
                $data['in_amount'][$value] = $data['nlb_in'][$value];
            } else {
                $data['in_amount'][$value] = '0.00';
            }

            if (isset($data['credits'][$value]) && isset($data['nlb_out'][$value])) {
                $data['out_amount'][$value] = round($data['credits'][$value], 2) + round($data['nlb_out'][$value], 2);
            } else if (isset($data['credits'][$value])) {
                $data['out_amount'][$value] = $data['credits'][$value];
            } else if (isset($data['nlb_out'][$value])) {
                $data['out_amount'][$value] = $data['nlb_out'][$value];
            } else {
                $data['out_amount'][$value] = '0.00';
            }

            if (!isset($data['dayopens'][$value])) {
                $data['dayopens'][$value] = 0;
            }
            if (!isset($data['in_amount'][$value])) {
                $data['in_amount'][$value] = 0;
            }
            if (!isset($data['out_amount'][$value])) {
                $data['out_amount'][$value] = 0;
            }

            $data['difference'][$value] = round($data['dayopens'][$value], 2) - round($data['out_amount'][$value], 2) + round($data['in_amount'][$value], 2);
            // if (isset($data['next_date_dayopens'][$value]) && isset($data['out_amount'][$value])) {
            // } elseif (isset($data['next_date_dayopens'][$value])) {
            //     $data['difference'][$value] = round($data['next_date_dayopens'][$value], 2);
            // } elseif (isset($data['dayopens'][$value])) {
            //     $data['difference'][$value] = 0 - round($data['dayopens'][$value], 2);
            // } else {
            //     $data['difference'][$value] = '0.00';
            // }
            $data['in_amount'][$value] = Helper::decimalShowing($data['in_amount'][$value], $data['branch']->country_id);
            $data['out_amount'][$value] = Helper::decimalShowing($data['out_amount'][$value], $data['branch']->country_id);
            $data['difference'][$value] = Helper::decimalShowing($data['difference'][$value], $data['branch']->country_id);
        }

        $data['dayopen_sum'] = round($data['dayopens']->sum(), 2);
        $data['total_in'] = Helper::decimalShowing($data['loan_transactions']->sum() + $data['nlb_in']->sum(), $data['branch']->country_id);
        $data['total_out'] = Helper::decimalShowing($data['credits']->sum() + $data['nlb_out']->sum(), $data['branch']->country_id);
        $data['next_dayopen_sum'] = $data['next_date_dayopens']->sum();
        $data['total_difference'] = Helper::decimalShowing(round($data['dayopen_sum'], 2) - round($data['credits']->sum() + $data['nlb_out']->sum(), 2) + round($data['loan_transactions']->sum() + $data['nlb_in']->sum(), 2), $data['branch']->country_id);
        $data['next_dayopen_sum'] = Helper::decimalShowing($data['next_dayopen_sum'], $data['branch']->country_id);
        $data['dayopen_sum'] = Helper::decimalShowing($data['dayopens']->sum(), $data['branch']->country_id);
        $data['dayopens'] = $data['dayopens']->map(function ($item, $key) use ($data) {
            return Helper::decimalShowing($item, $data['branch']->country_id);
        });
        $data['next_date_dayopens'] = $data['next_date_dayopens']->map(function ($item, $key) use ($data) {
            return Helper::decimalShowing($item, $data['branch']->country_id);
        });
        return $data;
    }

    public function auditApprove($date, $user, $branch)
    {
        $data = [];
        if (request('type') == 1) {
            $payment_types = [6, 4, 2, 3];
        } else if (request('type') == 2) {
            $payment_types = [5];
        } else if (request('type') == 3) {
            $payment_types = [1];
        }
        $end_date = Dayopen::where('date', '>', $date)
            ->where('branch_id', '=', $branch)
            ->whereIn('payment_type', $payment_types)
            ->orderBy('date', 'asc')
            ->first();
        if ($end_date != null) {
            Dayopen::whereDate('date', '=', $date)
                ->where('branch_id', '=', $branch)
                ->whereIn('payment_type', $payment_types)
                ->update([
                    'verified_by'     => auth()->user()->id,
                    'completion_date' => date('Y-m-d H:i:s')
                ]);
        }
        $data['status'] = true;
        return $data;
    }

    public function auditDatatable()
    {
        if (request('type') == 1) {
            $payment_types = [6, 4, 2, 3];
        } else if (request('type') == 2) {
            $payment_types = [5];
        } else if (request('type') == 3) {
            $payment_types = [1];
        }

        $dayopen = Dayopen::select('dayopens.date', 'dayopens.custom_created_at', 'dayopens.user_id', 'dayopens.branch_id',
            'dayopens.completion_date', 'dayopens.verified_by', DB::raw('sum(amount) as total_amount'),
            DB::raw('concat(users.firstname," ",users.lastname) as username'),
            DB::raw('concat(verified_by_user.firstname," ",verified_by_user.lastname) as verified_by_username'),
            'branches.title as branch_name')
            ->whereIn('dayopens.payment_type', $payment_types);
        $dayopen->leftJoin('users', function ($join) {
            $join->on('users.id', '=', 'dayopens.user_id')
                ->whereNull('users.deleted_at');
        });
        $dayopen->leftJoin('users as verified_by_user', function ($join) {
            $join->on('verified_by_user.id', '=', 'dayopens.verified_by')
                ->whereNull('verified_by_user.deleted_at');
        });
        $dayopen->leftJoin('branches', 'branches.id', '=', 'dayopens.branch_id');
        if (auth()->user()->hasRole('super admin')) {
            if (session()->has('country')) {
                $dayopen->where('branches.country_id', '=', session('country'));
            }
            if (request('branch_id')) {
                $dayopen->where('dayopens.branch_id', '=', request('branch_id'));
            }
        } else {
            if (session()->has('branch_id')) {
                $dayopen->where('dayopens.branch_id', '=', session('branch_id'));
            }
        }
        $dayopen->groupBy('dayopens.date', 'users.firstname', 'users.lastname', 'branches.title',
            'dayopens.completion_date', 'dayopens.user_id', 'dayopens.branch_id', 'dayopens.verified_by', 'dayopens.custom_created_at');

        return DataTables::of($dayopen)
            ->addColumn('date', function ($row) {
                return Helper::datebaseToFrontDate($row->date);
            })
            ->addColumn('custom_created_at', function ($row) {
                if ($row->custom_created_at != null) {
                    return Helper::date_time_to_current_timezone($row->custom_created_at);
                }
            })
            ->addColumn('completion_date', function ($row) {
                if ($row->completion_date != null) {
                    return Helper::date_time_to_current_timezone($row->completion_date);
                }
            })
            ->addColumn('status', function ($row) {
                if ($row->completion_date != null && $row->verified_by != null) {
                    return 'Completed';
                } else {
                    return 'Pending';
                }
            })
            ->addColumn('action', function ($row) {
                $iconClass = "btn btn-icon btn-sm btn-info waves-effect";
                $html = '<div class="button-list">';
                if ($row->date < date('Y-m-d')) {
                    $html .= "<a href='#nogo' title='View' data-date='" . date('Y-m-d', strtotime($row->date)) . "' data-user='" . $row->user_id . "' data-branch='" . $row->branch_id . "' 
                                class='$iconClass viewAuditReport'>
                                    <i class='fa fa-eye'></i>
                            </a>";
                }
                $html .= "</div>";
                return $html;
            })
            ->make(true);
    }
}
