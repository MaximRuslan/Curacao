@extends('client1.layouts.master')

@section('pageTitle')
    @lang('keywords.my_referrals')
@stop

@section('contentHeader')
@stop
@section('body_class')
    fixed-content
@stop

@section('content')
    <div class="froggy-listing" style="height: 100%">
        <div class="froggy-listing__header" style="background-image: url({!! asset('resources/img/client/froggy-cover.jpg') !!});">
            <div class="container">
                <h3>@lang('keywords.my_referrals')</h3>
            </div>
        </div>
        <div class="froggy-listing__content" style="background:white">
            <div class="container">
                <div class="mt-5">
                    <div class="froggy-table table-box mt-3">
                    <ul class="ul-reset functionality">
                            @foreach($referral_infos as $key=>$value)
                                <li style="text-align: center; height:75% !important; flex:0 0 30%; max-width:30%;">
                                    <a href="#nogo" class="functionality-box js--has-active-loan" style="justify-content: flex-start;">
                                        @if(isset($value['title']))
                                            <label style="word-break: break-word; font-size: 18px; font-weight: 600; height: 30px;">{!! $value['title'] !!}</label><br>
                                            <label style="font-size: 18px; height:25px;">{!! $value['value'] !!}</label>
                                        @endif
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="mt-5">
                    <div class="froggy-table table-box mt-3">
                        <table id="datatable">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>@lang('keywords.Date')</th>
                                <th>@lang('keywords.bonus_payout')</th>
                                <th>@lang('keywords.Status')</th>
                                <th>@lang('keywords.referred_client')</th>
                                <th>@lang('keywords.referred_client')</th>
                                <th>@lang('keywords.referred_client')</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('contentFooter')
    <script>
        $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                "url": clientAjaxURL + 'datatable-referral-histories'
            },
            columns: [
                {data: 'id', name: 'referral_histories.id', visible: false},
                {data: 'date', name: 'referral_histories.date'},
                {data: 'bonus_payout', name: 'referral_histories.bonus_payout'},
                {data: 'status', name: 'referral_histories.status'},
                {data: 'ref_name', name: 'ref.firstname'},
                {data: 'ref_name', name: 'ref.lastname', visible: false},
                {data: 'ref_name', name: 'ref.id_number', visible: false},
            ],
            order: [[0, 'desc']],
            pageLength: '50',
            "language": {
                "lengthMenu": keywords.Show + " _MENU_ " + keywords.Entries,
                "zeroRecords": keywords.NoMatchingRecordsFound,
                "info": keywords.Showing + " _START_ " + keywords.To + " _END_ " + keywords.Of + " _MAX_ " + keywords.Entries,
                "infoEmpty": keywords.Showing + " _START_ " + keywords.To + " _END_ " + keywords.Of + " _MAX_ " + keywords.Entries,
                "infoFiltered": "(" + keywords.FilteredFrom + " _MAX_ " + keywords.TotalRecords + ")",
                "search": keywords.Search,
                "paginate": {
                    "previous": keywords.Previous,
                    "next": keywords.Next,
                },
            },
            "drawCallback": function (settings) {
                initTooltip();
            },
        });
    </script>
@stop
