@extends('admin.layouts.app')
@section('page_name')
    Banks
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
        <div class="col-sm-12">
            <div class="btn-group pull-right m-b-20">
                <a data-toggle="modal" href="#bankModal" class="btn btn-default waves-effect waves-light">Add</a>
            </div>
            <h4 class="page-title">Banks</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive">
                <table id="bank-table" class="table  table-striped table-bordered" style="width:100%">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Contact Person</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Country</th>
                        <th>Transaction Fee</th>
                        <th>Tax On Transaction Fee</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @include('common.delete_confirm',[
        'modalId'=>'deleteBank',
        'action'=>route('banks.destroy','deleteId'),
        'item'=>'it',
        'callback'=>'showMsg'
        ])

    @include('admin.bank.create')
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
        $('.select2multiple').select2({
            'placeholder': 'Select Territories'
        });
        $(document).ready(function () {
            oTable = $('#bank-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{route('banks-data')}}",
                    "type": "POST"
                },
                order: [],
                "drawCallback": function (settings) {
                    InitTooltip();
                },
                columns: [
                    {data: 'id', name: 'banks.id', visible: false},
                    {data: 'name', name: 'banks.name'},
                    {data: 'contact_person', name: 'banks.contact_person'},
                    {data: 'email', name: 'banks.email'},
                    {data: 'phone', name: 'banks.phone'},
                    {data: 'country', name: 'countries.name'},
                    {data: 'transaction_fee', name: 'banks.transaction_fee'},
                    {data: 'tax_transaction', name: 'banks.tax_transaction'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ],
                order: [[0, 'desc']]
            });
            $('#bankModal').on('hidden.bs.modal', function () {
                $('#bankModal').find('form')[0].reset();
                $('#bankModal').find('form').find('input[name="id"]').val('');
                $('#bankModal').find('input').removeAttr('disabled');
                $('#bankModal').find('button[type="submit"]').show();
            })
        });

        function setEdit(id, type) {
            if (type == undefined) {
                type = '';
            }
            var action = '{{route('banks.show','')}}/' + id
            $.ajax({
                type: 'GET',
                url: action,
                data: {},
                dataType: 'json',
                success: function (data) {
                    setFormValues('bank_form', data.inputs);
                    $('#bankModal').modal('show');
                    countryTerritory($('[name="country_id"]').val());
                    if (type == 'view') {
                        setTimeout(function () {
                            $('#bankModal').find('input').attr('disabled', 'disabled');
                            $('#bankModal').find('button[type="submit"]').hide();
                        }, 100);
                    }
                },
                error: function (jqXHR, exception) {
                }
            });
        }

        function SaveBank(form) {
            var action = $(form).attr('action')
            $.ajax({
                type: 'POST',
                url: action,
                data: $(form).serialize(),
                dataType: 'json',
                success: function (data) {
                    successMsg('Message');
                    $('#bankModal').modal('hide');
                    oTable.draw(true);
                },
                error: function (jqXHR, exception) {
                    var Response = jqXHR.responseText;
                    ErrorBlock = $(form);
                    Response = $.parseJSON(Response);
                    DisplayErrorMessages(Response, ErrorBlock, 'input');
                }
            });
            return false;
        }

        function showMsg() {
            console.log('hello');
        }

        function countryTerritory(country) {
            if (country != '') {
                $.ajax({
                    dataType: 'json',
                    method: "get",
                    url: adminSiteURL + 'countries/' + country + '/info',
                    success: function (data) {
                        $('[name="country_tax_percentage"]').val(data['country']['tax_percentage']);
                    }
                });
            }
        }

        $(document).on('change blur keyup', '[name="transaction_fee"]', function (e) {
            e.preventDefault();
            var value = $(this).val();
            var percentage = $('[name="country_tax_percentage"]').val();
            var tax_percentage = value * percentage / 100;
            $('[name="tax_transaction"]').val(tax_percentage);
        });

        $(document).on('change', '#country_id', function (e) {
            e.preventDefault();
            countryTerritory($(this).val());
        });
    </script>
@endsection
