@extends('layouts.app')

@section('extra-styles')
    <link href="{{url(config('theme.admin.plugins'))}}/datatables/buttons.bootstrap4.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="{{url(config('theme.admin.plugins'))}}/datatables/responsive.bootstrap4.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="{{url(config('theme.front.plugins'))}}/bootstrap-datepicker/css/bootstrap-datepicker.min.css"
          rel="stylesheet">
    <link href="{{url(config('theme.front.plugins'))}}/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">
@endsection

@section('content')

    <section class="section" id="login">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <h4 class="page-title">@lang('keywords.Apply Loan Application')</h4>
                </div>
            </div>

            @include('loan_form', ['page' => '1'])
        </div>
    </section>
@endsection


@section('extra-js')
    <script src="{{url(config('theme.admin.plugins'))}}/datatables/dataTables.bootstrap4.min.js"></script>
    <script src="{{url(config('theme.admin.plugins'))}}/datatables/dataTables.responsive.min.js"></script>
    <script src="{{url(config('theme.admin.plugins'))}}/datatables/responsive.bootstrap4.min.js"></script>
    <script src="{{url(config('theme.front.plugins'))}}/moment/moment.js"></script>
    <script src="{{url(config('theme.front.plugins'))}}/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
    <script src="{{url(config('theme.front.plugins'))}}/bootstrap-daterangepicker/daterangepicker.js"></script>
    <script src="{{url(config('theme.common.js'))}}/loan.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script>
@endsection

@section('custom-js')
    <script>
        $('[data-toggle="tooltip"]').tooltip();
    </script>
@endsection
