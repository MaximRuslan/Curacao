<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Library\Helper;
use App\Mail\ReferralMail;
use App\Models\Country;
use App\Models\ExistingLoanType;
use App\Models\LoanApplication;
use App\Models\LoanReason;
use App\Models\LoanType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\Facades\DataTables;

class DashboardController extends Controller
{
    public function terms()
    {
        $data = [];
        $country = Country::find(auth()->user()->country);
        if (auth()->user()->lang == 'esp') {
            $data['cms'] = $country->terms_esp;
        } else if (auth()->user()->lang == 'pap') {
            $data['cms'] = $country->terms_pap;
        } else {
            $data['cms'] = $country->terms_eng;
        }
        return view('client.terms', $data);
    }

    public function acceptTerms()
    {
        $user = auth()->user();
        $user->update([
            'terms_accepted' => '1',
//            'signature'   => Helper::base64ToJpeg(request('signature'), 'signature_user_' . auth()->user()->id)
        ]);
        $country = Country::find($user->country);
        if (config('site.raffle') && $country->referral == 1) {
            Mail::to($user->email)->send(new ReferralMail($user));
        }
        $data = [
            'url' => route('client1.home')
        ];
        return $data;
    }

    public function index()
    {
        $data = [];
        $data['loans'] = LoanApplication::where('client_id', '=', auth()->user()->id)
            ->count();
        return view('client.dashboard', $data);
    }

    public function loans()
    {
        $data = [];
        $data['userData'] = Auth::user();
        $data['country'] = Country::where('id', '=', auth()->user()->country)
            ->first();
        $data['reasons'] = LoanReason::getAllReasons()->pluck('title', 'id');
        $data['otherLoanTypes'] = ExistingLoanType::getType();
        $data['types'] = LoanType::activeLoanTypesViaUserId(auth()->user())->pluck('title', 'id');
        return view('client.loans', $data);
    }

    public function create()
    {
        $data = [];
        $data['userData'] = Auth::user();
        $data['country'] = Country::where('id', '=', auth()->user()->country)->first();
        $data['reasons'] = LoanReason::getAllReasons()->pluck('title', 'id');
        $data['otherLoanTypes'] = ExistingLoanType::getType();
        $data['types'] = LoanType::activeLoanTypesViaUserId(auth()->user())->pluck('title', 'id');
        return view('common.loanapplication.loan_create', $data);
    }

    public function getList()
    {
        $user = Auth::user();
        $lang = config('site.lang');
        $applications = LoanApplication::select('loan_applications.*',
            'users.id as user_id_number', 'users.firstname as user_first_name',
            'loan_reasons.title as reason_title', 'loan_types.title as loan_type_title',
            'loan_status.title as loan_status_title',
            'loan_decline_reasons.title as loan_decline_reasons_title')
            ->where(['client_id' => $user->id])
            ->leftJoin('users', function ($left) {
                $left->on('users.id', '=', 'loan_applications.client_id');
            })
            ->leftJoin('loan_reasons', function ($left) {
                $left->on('loan_reasons.id', '=', 'loan_applications.loan_reason');
            })
            ->leftJoin('loan_types', function ($left) {
                $left->on('loan_types.id', '=', 'loan_applications.loan_type');
            })
            ->leftJoin('loan_status', function ($left) {
                $left->on('loan_status.id', '=', 'loan_applications.loan_status');
            })
            ->leftJoin('loan_decline_reasons', function ($left) {
                $left->on('loan_decline_reasons.id', '=', 'loan_applications.loan_decline_reason');
            });
        return DataTables::of($applications)
            ->editColumn('decline_reason.title', function ($data) use ($lang) {
                if ($data->loan_status == 3 && $data->declineReason) {
                    if (\App::getLocale() == $lang["esp"] && auth()->user()->hasRole('client')) {
                        return $data->declineReason->title_es;
                    } else if (\App::getLocale() == $lang["pap"] && auth()->user()->hasRole('client')) {
                        return $data->declineReason->title_nl;
                    } else {
                        return $data->declineReason->title;
                    }

                } else if ($data->loan_status == 2 && $data->onHoldReason) {
                    if (\App::getLocale() == $lang["esp"] && auth()->user()->hasRole('client')) {
                        return $data->onHoldReason->title_es;
                    } else if (\App::getLocale() == $lang["pap"] && auth()->user()->hasRole('client')) {
                        return $data->onHoldReason->title_nl;
                    } else {
                        return $data->onHoldReason->title;
                    }
                } else {
                    return '';
                }
            })
            ->editColumn('type.title', function ($data) use ($lang) {
                if ($data->type) {
                    if (\App::getLocale() == $lang["esp"] && auth()->user()->hasRole('client')) {
                        return $data->type->title_es;
                    } else if (\App::getLocale() == $lang["pap"] && auth()->user()->hasRole('client')) {
                        return $data->type->title_nl;
                    } else {
                        return $data->type->title;
                    }
                }
                return '';
            })
            ->editColumn('status.title', function ($data) use ($lang) {
                if ($data->status) {
                    if (\App::getLocale() == $lang["esp"] && auth()->user()->hasRole('client')) {
                        return $data->status->title_es;
                    } else if (\App::getLocale() == $lang["pap"] && auth()->user()->hasRole('client')) {
                        return $data->status->title_nl;
                    } else {
                        return $data->status->title;
                    }
                }
                return '';
            })
            ->editColumn('reason.title', function ($data) use ($lang) {
                if ($data->reason) {
                    if (\App::getLocale() == $lang["esp"] && auth()->user()->hasRole('client')) {
                        return $data->reason->title_es;
                    } else if (\App::getLocale() == $lang["pap"] && auth()->user()->hasRole('client')) {
                        return $data->reason->title_nl;
                    } else {
                        return $data->reason->title;
                    }
                }
                return '';
            })
            ->addColumn('amount', function ($data) {
                return number_format($data->amount, 2);
            })
            ->editColumn('created_at', function ($data) {
                return Helper::date_to_current_timezone($data->created_at);
            })
            ->editColumn('start_date', function ($data) {
                if ($data->start_date) {
                    return Helper::datebaseToFrontDate($data->start_date);
                } else {
                    return "";
                }
            })
            ->editColumn('end_date', function ($data) {
                if ($data->end_date) {
                    return Helper::datebaseToFrontDate($data->end_date);
                } else {
                    return "";
                }

            })
            ->addColumn('action', function ($data) {
                $html = "";
                $url = route('loan-applications.edit', $data->id);
                $html .= '<div class="btn-group loan-actions">';
                if ($data->loan_status == 1 || $data->loan_status == 2) {
                    $html .= '<a href="javascript:;"  data-toggle="tooltip" title="' . __('keywords.delete') . '" data-modal-id="deleteLoanApplication" data-id="' . $data->id . '" onclick="DeleteConfirm(this)" class="btn btn-sm waves-effect btn-info"><i class="fa fa-trash"></i></a>';

                    $html .= '<a href="javascript:;"  data-toggle="tooltip" title="' . __('keywords.edit') . '" data-url="' . $url . '" onclick="editApplication(this)" class="btn btn-sm waves-effect btn-info"><i class="fa fa-pencil"></i></a>';
                }
                $html .= '<a  data-toggle="tooltip" title="' . __('keywords.view') . '" target="_blank" href="' . url()->route('loan-applications.show', [
                        'id'   => $data->id,
                        'lang' => \App::getLocale()
                    ]) . '"  class="btn btn-sm waves-effect btn-info"><i class="fa fa-eye"></i></a>';
                $html .= '</div>';
                return $html;
            })
            ->make(true);
    }
}
