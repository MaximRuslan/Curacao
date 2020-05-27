@extends('layouts.app')

@section('extra-styles')
    <link href="{{url(config('theme.admin.plugins'))}}/datatables/dataTables.bootstrap4.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="{{url(config('theme.admin.plugins'))}}/datatables/buttons.bootstrap4.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="{{url(config('theme.admin.plugins'))}}/datatables/responsive.bootstrap4.min.css" rel="stylesheet"
          type="text/css"/>
    <style>
        .card-box {
            display: flex;
            align-items: center;
            height: 100%;
        }

        .card-box i {
            margin-right: 5px;
        }
    </style>
@endsection

@section('content')
    <section class="section" id="login">
        <div class="container">
            <div class="row">
                @if(auth()->user()->status !=  config('site.blacklistedUser'))
                    <a href="{!! route('client.loans.create') !!}" class="col-md-6 col-lg-6 col-xl-3" style=" height: 100px !important;">
                        <div class="widget-bg-color-icon card-box">
                            <div class="bg-icon pull-left" style="font-size: 70px;">
                                <i class="md md-note-add"></i>
                            </div>
                            <div class="text-right">
                                <p class="text-muted mb-0">@lang('keywords.Apply for loan')</p>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </a>
                @endif
                <a href="{!! route('client.loans') !!}" class="col-md-6 col-lg-6 col-xl-3"
                   style=" height: 100px !important;">
                    <div class="widget-bg-color-icon card-box">
                        <div class="bg-icon pull-left" style="font-size: 70px;">
                            <i class="md md-attach-money"></i>
                        </div>
                        <div class="text-right">
                            <p class="text-muted mb-0">@lang('keywords.My Loans')</p>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </a>
                @if(auth()->user()->status !=  config('site.blacklistedUser'))
                    <a href="{!! route('client.credits.index') !!}" class="col-md-6 col-lg-6 col-xl-3" style=" height: 100px !important;">
                        <div class="widget-bg-color-icon card-box">
                            <div class="bg-icon pull-left" style="font-size: 70px;">
                                <i class="md md-credit-card"></i>
                            </div>
                            <div class="text-right">
                                <p class="text-muted mb-0">@lang('keywords.My wallet')</p>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </a>
                @endif
                {{--<a href="#nogo" class="col-md-6 col-lg-6 col-xl-3" style=" height: 100px !important;">
                    <div class="widget-bg-color-icon card-box">
                        <div class="bg-icon pull-left" style="font-size: 70px;">
                            <i class="md md-list"></i>
                        </div>
                        <div class="text-right">
                            <p class="text-muted mb-0">@lang('keywords.My statements')</p>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </a>--}}
                <a href="#profileModal" data-toggle="modal" class="col-md-6 col-lg-6 col-xl-3"
                   style=" height: 100px !important;">
                    <div class="widget-bg-color-icon card-box">
                        <div class="bg-icon pull-left" style="font-size: 70px;">
                            <i class="md md-group"></i>
                        </div>
                        <div class="text-right">
                            <p class="text-muted mb-0">@lang('keywords.My profile')</p>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </a>
            </div>
        </div>
    </section>
@endsection

@section('extra-js')
@endsection

@section('custom-js')
@endsection
