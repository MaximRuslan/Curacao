@extends('admin.layouts.app')
@section('page_name')
    Audit Bank Reconciliation
@stop
@section('extra-styles')

    <link href="{{url(config('theme.admin.plugins'))}}/datatables/dataTables.bootstrap4.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="{{url(config('theme.admin.plugins'))}}/datatables/buttons.bootstrap4.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="{{url(config('theme.admin.plugins'))}}/datatables/responsive.bootstrap4.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="{{url(config('theme.admin.plugins'))}}/select2/css/select2.min.css" rel="stylesheet"
          type="text/css"/>
@endsection


@section('content')
    <div class="row">
        <div class="col-sm-12 m-b-20">
            <h4 class="page-title">Audit Bank Reconciliation</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive">
                <div class="row">
                    <div class="col-md-8"></div>
                    <div class="col-md-4">
                        {!!Form::select('year',$years,date('Y'),['class'=>'form-control','id'=>'year'])!!}
                    </div>
                </div>
                <table class="table table-bordered table-striped mt-3">
                    <thead>
                    <tr>
                        <th>Month</th>
                        <th colspan="2">CREDIT</th>
                        <th colspan="2">DEBIT</th>
                        <th colspan="2">Difference</th>
                    </tr>
                    <tr>
                        <th></th>
                        <th>Bank transfer</th>
                        <th>Reconciled</th>
                        <th>Bank transfer</th>
                        <th>Reconciled</th>
                        <th>CREDIT</th>
                        <th>DEBIT</th>
                    </tr>
                    </thead>
                    <tbody id="audit_tbody"></tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('extra-js')
    <script src="{{url(config('theme.admin.plugins'))}}/datatables/jquery.dataTables.min.js"></script>
    <script src="{{url(config('theme.admin.plugins'))}}/datatables/dataTables.bootstrap4.min.js"></script>

    <script src="{{url(config('theme.admin.plugins'))}}/datatables/dataTables.responsive.min.js"></script>
    <script src="{{url(config('theme.admin.plugins'))}}/datatables/responsive.bootstrap4.min.js"></script>
    <script src="{{url(config('theme.admin.plugins'))}}/select2/js/select2.full.js"></script>
@endsection

@section('custom-js')
    <script type="text/javascript">
        var oTable = "";
        $('#year').select2();
        $(document).on('change', '#year', function (e) {
            e.preventDefault();
            auditBank($(this).val());
        });

        function auditBank(year) {
            $.ajax({
                dataType: 'json',
                method: 'post',
                url: adminSiteURL + 'audit-bank-reconciliation',
                data: {
                    year: year
                },
                success: function (data) {
                    str = '';
                    for (var index in data) {
                        var month = data[index];
                        str += '<tr>' +
                            '<td>' + month['month'] + '</td>' +
                            '<td>' + month['credit'] + '</td>' +
                            '<td>' + month['re_credit'] + '</td>' +
                            '<td>' + month['debit'] + '</td>' +
                            '<td>' + month['re_debit'] + '</td>' +
                            '<td>' + month['difference'] + '</td>' +
                            '<td>' + month['re_difference'] + '</td>' +
                            '</tr>';
                    }
                    $('#audit_tbody').html(str);
                }
            });
        }

        $(document).ready(function () {
            auditBank($('#year').val());
        });
    </script>
@endsection
