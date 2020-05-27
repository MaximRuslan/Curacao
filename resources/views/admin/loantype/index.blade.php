@extends('admin.layouts.app')
@section('page_name')
    Loan Types
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
                <a data-toggle="modal" href="#loanTypeModal" class="btn btn-default waves-effect waves-light">Add</a>
            </div>
            <h4 class="page-title">Loan types</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive">
                <table id="loan-type-table" class="table  table-striped table-bordered" style="width:100%">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name ENG</th>
                        <th>Name ESP</th>
                        <th>Name PAP</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @include('common.delete_confirm',[
        'modalId'=>'deleteLoanType',
        'action'=>route('loan-types.destroy','deleteId'),
        'item'=>'it',
        'callback'=>'showMsg'
        ])

    @include('admin.loantype.create')
@endsection
@section('extra-js')
    <script src="{{url(config('theme.admin.plugins'))}}/datatables/jquery.dataTables.min.js"></script>
    <script src="{{url(config('theme.admin.plugins'))}}/datatables/dataTables.bootstrap4.min.js"></script>
    <script src="{{url(config('theme.admin.plugins'))}}/datatables/dataTables.responsive.min.js"></script>
    <script src="{{url(config('theme.admin.plugins'))}}/datatables/responsive.bootstrap4.min.js"></script>
    <script src="{{url(config('theme.admin.plugins'))}}/select2/js/select2.full.js"></script>
    <script src="{!! asset('admin/js/plugins/tinymce.min.js') !!}"></script>
@endsection

@section('custom-js')
    <script type="text/javascript">
        var oTable = "";
        var tiny_mce = tinymce.init({
            selector: "textarea.cms_textarea",
            theme: "modern",
            height: 300,
            plugins: [
                "advlist autolink link lists charmap print preview hr anchor pagebreak spellchecker",
                "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime nonbreaking",
                "save table contextmenu directionality emoticons template paste textcolor"
            ],
            toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | l      ink | print preview fullpage | forecolor backcolor emoticons",
            style_formats: [
                {title: 'Bold text', inline: 'b'},
                {title: 'Red text', inline: 'span', styles: {color: '#ff0000'}},
                {title: 'Red header', block: 'h1', styles: {color: '#ff0000'}},
                {title: 'Example 1', inline: 'span', classes: 'example1'},
                {title: 'Example 2', inline: 'span', classes: 'example2'},
                {title: 'Table styles'},
                {title: 'Table row 1', selector: 'tr', classes: 'tablerow1'}
            ]
        });
        $('.select-multiple').select2();
        $(document).ready(function () {
            oTable = $('#loan-type-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{route('loan-type-data')}}",
                    "type": "POST"
                },
                order: [],
                "drawCallback": function (settings) {
                    InitTooltip();
                },
                columns: [
                    {data: 'id', name: 'id', visible: false},
                    {data: 'title', name: 'title'},
                    {data: 'title_es', name: 'title_es'},
                    {data: 'title_nl', name: 'title_nl'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ],
                order: [0, 'desc']
            });
            $('#loanTypeModal').on('hidden.bs.modal', function () {
                $('#loanTypeModal').find('form')[0].reset();
                $('#loanTypeModal').find('form').find('input[name="id"]').val('');
                $('#loanTypeModal').find('input').removeAttr('disabled');
                $('#loanTypeModal').find('button[type="submit"]').show();
            })
        });

        function setEdit(id, type) {
            if (type == undefined) {
                type = '';
            }
            var action = '{{route('loan-types.show','')}}/' + id
            $.ajax({
                type: 'GET',
                url: action,
                data: {},
                dataType: 'json',
                success: function (data) {
                    setFormValues('loanType_form', data.inputs);
                    $('#loanTypeModal').modal('show');
                    if (type == 'view') {
                        setTimeout(function () {
                            $('#loanTypeModal').find('input').attr('disabled', 'disabled');
                            $('#loanTypeModal').find('button[type="submit"]').hide();
                        }, 100);
                    }
                },
                error: function (jqXHR, exception) {
                }
            });
        }

        function SaveLoanType(form) {
            var action = $(form).attr('action');
            if (parseInt($('#minimum_loan').val()) <= parseInt($('#maximum_loan').val())) {
                var dataObj = new FormData(form);
                dataObj.append('loan_agreement_eng', tinyMCE.get('loan_agreement_eng').getContent());
                dataObj.append('loan_agreement_esp', tinyMCE.get('loan_agreement_esp').getContent());
                dataObj.append('loan_agreement_pap', tinyMCE.get('loan_agreement_pap').getContent());
                $.ajax({
                    type: 'POST',
                    url: action,
                    data: dataObj,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function (data) {
                        successMsg('Message');
                        $('#loanTypeModal').modal('hide');
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
            } else {
                $('.minimum_maximum_loan_error').html('Minimum amount should be lower than maximum amount.');
            }
        }

        function showMsg() {
            console.log('hello');
        }

        function countryTerritory(country, territory_id) {
            if (territory_id == undefined) {
                territory_id = [];
            }
            if (country != '' && country != null) {
                $.ajax({
                    dataType: 'json',
                    method: "get",
                    url: adminSiteURL + 'countries/' + country + '/territories',
                    success: function (data) {
                        str = '';
                        for (var index in data['territories']) {
                            var territory = data['territories'][index];
                            if (territory_id.indexOf(parseInt(index)) >= 0) {
                                str += '<option value="' + index + '" selected>' + territory + '</option>';
                            } else {
                                str += '<option value="' + index + '">' + territory + '</option>';
                            }
                        }
                        $('#territory_id').html(str);
                    }
                });
            }
        }

        $(document).on('change', '#country_id', function (e) {
            e.preventDefault();
            countryTerritory($(this).val());
        });

        $(document).on('submit', '#loanType_form', function (e) {
            e.preventDefault();
            SaveLoanType('#loanType_form');
        });
    </script>
@endsection
