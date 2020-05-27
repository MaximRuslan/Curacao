<?php

namespace App\Http\Controllers\Admin1;

use App\Http\Controllers\Controller;
use App\Library\Helper;
use App\Models\ReferralHistory;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class ReferralController extends Controller
{
    public function index()
    {
        $data = [];

        return view('admin1.pages.referral_histories.index', $data);
    }

    public function query()
    {
        $history = ReferralHistory::select('referral_histories.*', DB::raw('concat(users.firstname," ",users.lastname,"-",users.id_number) as name'), 'users.country',
            DB::raw('concat(ref.firstname," ",ref.lastname,"-",ref.id_number) as ref_name'))
            ->leftJoin('users', 'users.id', '=', 'referral_histories.client_id')
            ->leftJoin('users as ref', 'ref.id', '=', 'referral_histories.referred_client');

        if (auth()->user()->hasRole('super admin')) {
            $country = session()->has('country') ? session()->get('country') : '';
            if ($country != '') {
                $history->where('users.country', '=', $country);
            }
        } else {
            $history->where('users.country', '=', auth()->user()->country);
        }
        $format = config('site.date_format.php');
        if (request('start_date')) {
            $date = \DateTime::createFromFormat($format, request('start_date'));
            $history->whereDate('referral_histories.date', '>=', $date->format('Y-m-d'));
        }

        if (request('end_date')) {
            $date = \DateTime::createFromFormat($format, request('end_date'));
            $history->whereDate('referral_histories.date', '<=', $date->format('Y-m-d'));
        }

        if (request('loan_status') && request('loan_status') != 0) {
            $history->where('referral_histories.status', '=', request('loan_status'));
        }

        return $history;
    }

    public function indexDatatable()
    {

        return DataTables::of(self::query())
            ->addColumn('bonus_payout', function ($row) {
                return Helper::decimalShowing($row->bonus_payout, $row->country);
            })
            ->addColumn('status', function ($row) {
                if ($row->status == 1) {
                    return 'Start';
                } else if ($row->status == 2) {
                    return 'PIF';
                }
            })
            ->addColumn('date', function ($row) {
                return Helper::date_to_current_timezone($row->date, null);
            })
            ->addColumn('name', function ($row) {
                return '<a href="' . route('admin1.users.show', $row->client_id) . '">' . $row->name . '</a>';
            })
            ->addColumn('ref_name', function ($row) {
                return '<a href="' . route('admin1.users.show', $row->referred_client) . '">' . $row->ref_name . '</a>';
            })
            ->rawColumns(['name', 'ref_name'])
            ->make(true);
    }

    public function excelDownload()
    {
        $elements = self::query()->get();

        $data = [];

        foreach ($elements as $key => $value) {
            $data[$key]['Date'] = Helper::date_to_current_timezone($value->date);
            $data[$key]['Bonus Payout'] = Helper::decimalShowing($value->bonus_payout, $value->country);
            $data[$key]['Client-ID'] = $value->name;
            if ($value->status == 1) {
                $data[$key]['Status'] = 'Start';
            } else {
                $data[$key]['Status'] = 'PIF';
            }
            $data[$key]['Referred Client'] = $value->name;
        }

        $filename = 'Referral History -' . date('Ymd');
        Excel::create($filename, function ($excel) use ($data) {
            $excel->setTitle('Report OF ' . date('d-m-Y H:i:s'));
            //Chain the setters
            $excel->sheet('Report', function ($sheet) use ($data) {
                $sheet->fromArray($data);
            });
        })->store('xlsx', public_path() . '/uploads/excel');

        $data = [];

        $data['url'] = asset('uploads/excel/' . $filename . '.xlsx');

        return $data;
    }
}
