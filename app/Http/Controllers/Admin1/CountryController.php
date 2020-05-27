<?php

namespace App\Http\Controllers\Admin1;

use App\Http\Controllers\Controller;
use App\Library\DefaultHelper;
use App\Library\Helper;
use App\Models\Country;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class CountryController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:super admin');
    }

    public function index()
    {
        $data = [];
        $data['default_terms'] = DefaultHelper::defaultAgreement();
        return view('admin1.pages.country.index', $data);
    }

    public function store()
    {
        $this->validate(request(), Country::validationRules(request()->all()));
        $id = request('id');
        $country = Country::find($id);
        $inputs = request()->all();
        $inputs['referral'] = 0;
        if (request('referral') == 1) {
            $inputs['referral'] = 1;
        }
        $inputs['raffle'] = 0;
        if (request('raffle') == 1) {
            $inputs['raffle'] = 1;
        }
        $inputs['decimal'] = 0;
        if (request('decimal') == 1) {
            $inputs['decimal'] = 1;
        }
        $inputs['pagare'] = 0;
        if (request('pagare') == 1) {
            $inputs['pagare'] = 1;
        }
        if (request()->hasFile('logo')) {
            if ($country != null && $country->logo != '') {
                Storage::delete(public_path('uploads/' . $country->logo));
            }
            $logo = time() . '_' . request()->file('logo')->getClientOriginalName();
            $path = request()->logo->move(public_path('uploads'), $logo);
            $inputs['logo'] = $logo;
        }
        $inputs['time_offset'] = Helper::timezoneToOffset($inputs['timezone']);
        if ($country) {
            $country->update($inputs);
        } else {
            $country = Country::create($inputs);
        }
        $data = [];
        $data['status'] = true;
        return $data;
    }

    public function edit(Country $country)
    {
        $data = [];
        $data['inputs'] = [
            'id'             => ["type" => "hidden", 'value' => $country->id],
            'name'           => ["type" => "text", 'value' => $country->name],
            'country_code'   => ["type" => "number", 'value' => $country->country_code],
            'phone_length'   => ["type" => "number", 'value' => $country->phone_length],
            'valuta_name'    => ["type" => "text", 'value' => $country->valuta_name],
            'tax'            => ["type" => "text", 'value' => $country->tax],
            'logo'           => ["type" => "image", 'value' => $country->logo],
            'tax_percentage' => ["type" => "number", 'value' => $country->tax_percentage],
            'timezone'       => ["type" => "text", 'value' => $country->timezone],
            'map_link'       => ["type" => "text", 'value' => $country->map_link],
            'terms_eng'      => ["type" => "tinymce", 'value' => $country->terms_eng],
            'terms_esp'      => ["type" => "tinymce", 'value' => $country->terms_esp],
            'terms_pap'      => ["type" => "tinymce", 'value' => $country->terms_pap],
            'pagare'         => ["type" => "checkbox", 'value' => $country->pagare],
            'email'          => ['type' => 'email', 'value' => $country->email],
            'referral'       => ['type' => 'checkbox', 'value' => $country->referral],
            'raffle'         => ['type' => 'checkbox', 'value' => $country->raffle],
            'decimal'        => ['type' => 'checkbox', 'value' => $country->decimal],
            'telephone'      => ['type' => 'number', 'value' => $country->telephone],
            'web'            => ['type' => 'text', 'value' => $country->web],
            'company_name'   => ['type' => 'text', 'value' => $country->company_name],
            'sender_number'  => ['type' => 'text', 'value' => $country->sender_number],

        ];
        return $data;
    }

    public function destroy(Country $country)
    {
        $data = [];
        $country->update([
            'deleted_by' => auth()->user()->id
        ]);
        $data['status'] = $country->delete();
        return $data;
    }

    public function indexDatatable()
    {
        $loanType = Country::select('*');
        return DataTables::of($loanType)
            ->addColumn('action', function ($row) {
                $iconClass = "btn btn-icon btn-sm btn-info waves-effect";
                $html = '<div class="button-list">';
                $html .= "<a href='javascript:;' title='Edit' data-toggle='tooltip' data-id='$row->id' class='$iconClass editCountry'>
                            <i class='fa fa-pencil'></i>
                        </a>";
                $html .= "<a href='javascript:;' title='Delete' data-toggle='tooltip' data-id='$row->id' class='$iconClass deleteCountry'><i class='fa fa-trash'></i></a>";
                $html .= "<a href='javascript:;' title='View' data-toggle='tooltip' class='$iconClass editCountry' data-id='$row->id' data-type='view'>
                            <i class='fa fa-eye'></i>
                        </a>";
                return $html;
            })
            ->make();
    }
}
