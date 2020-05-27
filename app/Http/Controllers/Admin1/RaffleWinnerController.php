<?php

namespace App\Http\Controllers\Admin1;

use App\Http\Controllers\Controller;
use App\Library\Helper;
use App\Models\RaffleWinner;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class RaffleWinnerController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:super admin');
    }

    public function index()
    {
        return view('admin1.pages.raffle_winner.index');
    }

    public function indexDatatable()
    {
        $winners = RaffleWinner::select('raffle_winners.*', DB::raw('concat(users.firstname," ",users.lastname) as name'), 'countries.name as country_name')
            ->leftJoin('users', 'users.id', '=', 'raffle_winners.user_id')
            ->leftJoin('countries', 'countries.id', '=', 'raffle_winners.country_id');
        $country = '';
        if (auth()->user()->hasRole('super admin')) {
            if (session()->has('country')) {
                $country = session('country');
            }
        } else {
            $country = auth()->user()->country;
        }
        if ($country != '') {
            $winners->where('raffle_winners.country_id', '=', $country);
        }
        return DataTables::of($winners)
            ->addColumn('date', function ($row) {
                return Helper::date_to_current_timezone($row->date, null, 'M Y');
            })
            ->addColumn('name', function ($row) {
                return '<a href="' . route('admin1.users.show', $row->user_id) . '">' . $row->name . '</a>';
            })
            ->rawColumns(['name'])
            ->make(true);
    }
}
