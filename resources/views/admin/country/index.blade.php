@extends('admin.layouts.app')
@section('page_name')
    Countries
@stop
@section('extra-styles')

    <link href="{{url(config('theme.admin.plugins'))}}/datatables/dataTables.bootstrap4.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="{{url(config('theme.admin.plugins'))}}/datatables/buttons.bootstrap4.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="{{url(config('theme.admin.plugins'))}}/datatables/responsive.bootstrap4.min.css" rel="stylesheet"
          type="text/css"/>
@endsection


@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="btn-group pull-right m-b-20">
                <a data-toggle="modal" href="#countryModal" class="btn btn-default waves-effect waves-light">Add</a>
            </div>
            <h4 class="page-title">Countries</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive">
                <table id="country-table" class="table  table-striped table-bordered" style="width:100%">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Phone Code</th>
                        <th>Phone Length</th>
                        <th>Valuta</th>
                        <th>Tax</th>
                        <th>Tax %</th>
                        <th>Time Zone</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @include('common.delete_confirm',[
        'modalId'=>'deleteCountry',
        'action'=>route('countries.destroy','deleteId'),
        'item'=>'it',
        'callback'=>'showMsg'
        ])

    @include('admin.country.create')
@endsection
@section('extra-js')
    <script src="{{url(config('theme.admin.plugins'))}}/datatables/jquery.dataTables.min.js"></script>
    <script src="{{url(config('theme.admin.plugins'))}}/datatables/dataTables.bootstrap4.min.js"></script>

    <script src="{{url(config('theme.admin.plugins'))}}/datatables/dataTables.responsive.min.js"></script>
    <script src="{{url(config('theme.admin.plugins'))}}/datatables/responsive.bootstrap4.min.js"></script>
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
        $(document).ready(function () {
            oTable = $('#country-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{route('countries-data')}}",
                    "type": "POST"
                },
                order: [],
                "drawCallback": function (settings) {
                    InitTooltip();
                },
                columns: [
                    {data: 'id', name: 'id', visible: false},
                    {data: 'name', name: 'name'},
                    {data: 'country_code', name: 'country_code'},
                    {data: 'phone_length', name: 'phone_length'},
                    {data: 'valuta_name', name: 'valuta_name'},
                    {data: 'tax', name: 'tax'},
                    {data: 'tax_percentage', name: 'tax_percentage'},
                    {data: 'timezone', name: 'timezone'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ],
                order: [[0, 'desc']]
            });
            $('#countryModal').on('hidden.bs.modal', function () {
                $('#countryModal').find('form')[0].reset();
                $('#countryModal').find('form').find('input[name="id"]').val('');
                $('#countryModal').find('input').removeAttr('disabled');
                $('#countryModal').find('button[type="submit"]').show();
                $('#countryModal').find('[name="logo"]').prop('required', true);
                $('.logo-holder').hide();
                $('.logo-holder').find('img').attr('src', '');
            })
        });

        function setEdit(id, type) {
            if (type == undefined) {
                type = '';
            }
            var action = '{{route('countries.show','')}}/' + id
            $.ajax({
                type: 'GET',
                url: action,
                data: {},
                dataType: 'json',
                success: function (data) {
                    setFormValues('country_form', data.inputs);
                    if (data['inputs']['logo']['value'] != '') {
                        $('[name="logo"]').prop('required', false);
                    } else {
                        $('[name="logo"]').prop('required', true);
                    }
                    $('#countryModal').modal('show');
                    if (type == 'view') {
                        setTimeout(function () {
                            $('#countryModal').find('input').attr('disabled', 'disabled');
                            $('#countryModal').find('button[type="submit"]').hide();
                        }, 100);
                    }
                    if (data.inputs.logo.value != '') {
                        $('.logo-holder').show();
                        $('.logo-holder').find('img').attr('src', data.inputs.logo.value);
                    } else {
                        $('.logo-holder').hide();
                        $('.logo-holder').find('img').attr('src', '');
                    }
                },
                error: function (jqXHR, exception) {
                }
            });
        }

        $("#country_form").submit(function (e) {
            e.preventDefault();
            var action = $("#country_form").attr('action');
            var dataObj = new FormData(this);
            dataObj.append('terms_eng', tinyMCE.get('terms_eng').getContent());
            dataObj.append('terms_esp', tinyMCE.get('terms_esp').getContent());
            dataObj.append('terms_pap', tinyMCE.get('terms_pap').getContent());
            $.ajax({
                type: 'POST',
                url: action,
                data: dataObj,
                dataType: 'json',
                processData: false,
                contentType: false,
                success: function (rst) {
                    successMsg('Message');
                    $('#countryModal').modal('hide');
                    oTable.draw(true);
                },
                error: function (jqXHR, exception) {
                    var Response = jqXHR.responseText;
                    ErrorBlock = $("#country_form");
                    Response = $.parseJSON(Response);
                    DisplayErrorMessages(Response, ErrorBlock, 'input');
                }
            });
            return false;
        });

        function showMsg() {
            console.log('hello');
        }
    </script>
@endsection
