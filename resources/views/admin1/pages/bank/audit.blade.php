@extends('admin1.layouts.master')
@section('page_name')
    Audit Bank Reconciliation
@stop
@section('contentHeader')
@stop
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
@stop
@section('contentFooter')
    <script>
        $('#year').select2();
        $(document).on('change', '#year', function (e) {
            e.preventDefault();
            auditBank($(this).val());
        });

        function auditBank(year) {
            $.ajax({
                dataType: 'json',
                method: 'post',
                url: adminAjaxURL + 'audit-bank-reconciliation',
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
@stop