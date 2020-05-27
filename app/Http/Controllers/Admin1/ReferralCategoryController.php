<?php

namespace App\Http\Controllers\Admin1;

use App\Http\Controllers\Controller;
use App\Library\Helper;
use App\Models\Country;
use App\Models\ReferralCategory;
use Yajra\DataTables\Facades\DataTables;

class ReferralCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:super admin|admin');
    }

    public function index()
    {
        $data = [];
        $country = session()->has('country') ? session()->get('country') : '';
        if (auth()->user()->hasRole('super admin') && $country == '') {
            $data['countries'] = Country::pluck('name', 'id');
        }
        return view('admin1.pages.referral_categories.index', $data);
    }

    public function store()
    {
        $max = request('max_referrals');
        $this->validate(request(), ReferralCategory::validationRules($max));
        $inputs = request()->all();
        $country = auth()->user()->country;
        if (auth()->user()->hasRole('super admin')) {
            if (session()->has('country')) {
                $country = session('country');
            } else {
                $country = $inputs['country_id'];
            }
        }
        $inputs['country_id'] = $country;
        $id = request('id');
        $category = ReferralCategory::find($id);
        if ($category) {
            $category->update($inputs);
        } else {
            $category = ReferralCategory::create($inputs);
        }
        $data = [];
        $data['status'] = true;
        return $data;
    }

    public function edit(ReferralCategory $category)
    {
        $data = [];
        $data['inputs'] = [
            'id'            => ["type" => "hidden", 'value' => $category->id],
            'country_id'    => ["type" => "select2", 'value' => $category->country_id],
            'title'         => ["type" => "text", 'value' => $category->title],
            'min_referrals' => ["type" => "number", 'value' => $category->min_referrals],
            'max_referrals' => ["type" => "number", 'value' => $category->max_referrals],
            'loan_start'    => ["type" => "number", 'value' => $category->loan_start],
            'loan_pif'      => ["type" => "number", 'value' => $category->loan_pif],
            'status'        => ["type" => "radio", 'value' => $category->status],
        ];
        return $data;
    }

    public function destroy(ReferralCategory $category)
    {
        $data = [];
        $category->update([
            'deleted_by' => auth()->user()->id
        ]);
        $data['status'] = $category->delete();
        return $data;
    }

    public function indexDatatable()
    {
        $loanType = ReferralCategory::select('referral_categories.*', 'countries.name as country_name')
            ->leftJoin('countries', 'countries.id', '=', 'referral_categories.country_id');

        $country = '';
        if (auth()->user()->hasRole('super admin')) {
            if (session()->has('country')) {
                $country = session('country');
            }
        } else {
            $country = auth()->user()->country;
        }
        if ($country != '') {
            $loanType->where('country_id', '=', $country);
        }

        return DataTables::of($loanType)
            ->addColumn('loan_start', function ($row) {
                return Helper::decimalShowing($row->loan_start, $row->country_id);
            })
            ->addColumn('loan_pif', function ($row) {
                return Helper::decimalShowing($row->loan_pif, $row->country_id);
            })
            ->addColumn('status', function ($row) {
                if ($row->status == 1) {
                    return 'Active';
                } else {
                    return 'Inactive';
                }
            })
            ->addColumn('action', function ($row) {
                $iconClass = "btn btn-icon btn-sm btn-info waves-effect";
                $html = '<div class="button-list">';
                $html .= "<a href='javascript:;' title='Edit' data-toggle='tooltip' data-id='$row->id' class='$iconClass editCategory'>
                            <i class='fa fa-pencil'></i>
                        </a>";
                $html .= "<a href='javascript:;' title='Delete' data-toggle='tooltip' data-id='$row->id' class='$iconClass deleteCategory'><i class='fa fa-trash'></i></a>";
                $html .= "<a href='javascript:;' title='View' data-toggle='tooltip' class='$iconClass editCategory' data-id='$row->id' data-type='view'>
                            <i class='fa fa-eye'></i>
                        </a>";
                return $html;
            })
            ->make();
    }
}
