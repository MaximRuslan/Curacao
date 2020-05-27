@extends('admin.layouts.app')
@section('page_name')
    Dashboard
@stop
@section('extra-styles')
    <link href="{{url(config('theme.admin.plugins').'/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet"
          type="text/css"/>
    <link href="{{url(config('theme.admin.plugins').'/datatables/buttons.bootstrap4.min.css')}}" rel="stylesheet"
          type="text/css"/>
    <link href="{{url(config('theme.admin.plugins').'/datatables/responsive.bootstrap4.min.css')}}" rel="stylesheet"
          type="text/css"/>
    <link href="{{url(config('theme.admin.plugins').'/jquery.steps/css/jquery.steps.css')}}" rel="stylesheet"
          type="text/css"/>
    <link href="{{url(config('theme.front.plugins'))}}/bootstrap-datepicker/css/bootstrap-datepicker.min.css"
          rel="stylesheet">
    <link href="{{url(config('theme.front.plugins'))}}/bootstrap-daterangepicker/daterangepicker.css"
          rel="stylesheet">
@endsection
@section('content')
    @if(auth()->user()->hasRole('super admin|admin|auditor'))
        @foreach($territory as $item)
            <div class="row">
                <div class="col-sm-12">
                    <h4 class="page-title m-b-20">{{$item->title}}</h4>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-lg-6 col-xl-2">
                    <div class="card-box widget-inline-box text-center">
                        <h3><i class="text-warning fa fa-user"></i> <b data-plugin="counterup">{{$item->userCount}}</b>
                        </h3>
                        <h4 class="text-muted font-16">Clients</h4>
                    </div>
                </div>

                <div class="col-md-6 col-lg-6 col-xl-2">
                    <div class="card-box widget-inline-box text-center">
                        <h3><i class="text-warning fa fa-vcard"></i> <b
                                    data-plugin="counterup">{{$item->applicationsCount}}</b></h3>
                        <h4 class="text-muted font-16">Applications</h4>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6 col-xl-2">
                    <div class="card-box widget-inline-box text-center">
                        <h3><i class="text-warning fa fa-android"></i> <b data-plugin="counterup">100</b></h3>
                        <h4 class="text-muted font-16">Android App</h4>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6 col-xl-2">
                    <div class="card-box widget-inline-box text-center">
                        <h3><i class="text-warning fa fa-apple"></i> <b data-plugin="counterup">150</b></h3>
                        <h4 class="text-muted font-16">iOS App</h4>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6 col-xl-3">
                    <div class="card-box widget-inline-box text-center">
                        <h3><i class="text-warning fa fa-bars"></i> <b
                                    data-plugin="counterup">{{$item->pendingCount}}</b>
                        </h3>
                        <h4 class="text-muted font-16">Pending Applications</h4>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6 col-xl-3">
                    <div class="card-box widget-box-1 bg-white">
                        <h4 class="text-dark">Open application</h4>
                        <h2 class="text-primary text-center"><span data-plugin="counterup">{{$item->openCount}}</span>
                        </h2>
                        <p class="text-muted">Total balance: ${{$item->openBalance}}</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6 col-xl-3">
                    <div class="card-box widget-box-1 bg-white">
                        <h4 class="text-dark">Exceeding application</h4>
                        <h2 class="text-primary text-center"><span
                                    data-plugin="counterup">{{$item->exceedingCount}}</span>
                        </h2>
                        <p class="text-muted">Total balance: ${{$item->exceedingBalance}}</p>
                    </div>
                </div>
                <div class="col-md-12 col-lg-12 col-lg-12">
                    <h3>Transactions</h3>
                    <div class="card-box widget-box-1 bg-white">
                        <div class="row">
                            <div class="col-md-3"></div>
                            <div class="col-md-3"></div>
                            <div class="col-md-3"></div>
                            <div class="col-md-3">
                                {!!Form::text('date', date('d-m-Y'), ['class'=>'form-control datepicker','data-id'=>$item->id,'placeholder'=>'Date'])!!}
                            </div>
                        </div>
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th></th>
                                @foreach(config('site.payment_types') as $key=>$value)
                                    <th colspan="2">{!! $value !!}</th>
                                @endforeach
                            </tr>
                            <tr>
                                <th></th>
                                @foreach(config('site.payment_types') as $key=>$value)
                                    <th>In</th>
                                    <th>Out</th>
                                @endforeach
                            </tr>
                            </thead>
                            <tbody id="transactions_{!! $item->id !!}">
                            <tr>
                                <th>System</th>
                                @foreach(config('site.payment_types') as $key=>$value)
                                    <td>{!! number_format($item->loan_amounts[$key]['in'],2) !!}</td>
                                    <td>{!! number_format($item->loan_amounts[$key]['out'],2) !!}</td>
                            @endforeach
                            {{--</tr>--}}
                            {{--<tr>--}}
                            {{--<th>Daily Turnovers</th>--}}
                            {{--@foreach(config('site.payment_types') as $key=>$value)--}}
                            {{--<td>{!! number_format($item->turnovers[$key]['in'] ,2)!!}</td>--}}
                            {{--<td>{!! number_format($item->turnovers[$key]['out'] ,2)!!}</td>--}}
                            {{--@endforeach--}}
                            {{--</tr>--}}
                            {{--<tr>--}}
                            {{--<th>Daily Turnovers Correction</th>--}}
                            {{--@foreach(config('site.payment_types') as $key=>$value)--}}
                            {{--<td>{!! number_format($item->correction[$key]['in'] ,2)!!}</td>--}}
                            {{--<td>{!! number_format($item->correction[$key]['out'] ,2)!!}</td>--}}
                            {{--@endforeach--}}
                            {{--</tr>--}}
                            {{--<tr>--}}
                            {{--<th>Difference</th>--}}
                            {{--@foreach(config('site.payment_types') as $key=>$value)--}}
                            {{--<td>{!! number_format($item->loan_amounts[$key]['in']-( $item->turnovers[$key]['in'] +  $item->correction[$key]['in'] ) ,2)!!}</td>--}}
                            {{--<td>{!! number_format($item->loan_amounts[$key]['out']-( $item->turnovers[$key]['out'] + $item->correction[$key]['out'] ),2)!!}</td>--}}
                            {{--@endforeach--}}
                            {{--</tr>--}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
@endsection
@section('extra-js')
    <script src="{{url(config('theme.front.plugins'))}}/moment/moment.js"></script>
    <script src="{{url(config('theme.front.plugins'))}}/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
@endsection
@section('custom-js')
    @if(auth()->user()->hasRole('super admin|admin|auditor'))
        <script>
            $('.datepicker').datepicker({
                format: 'dd-mm-yyyy'
            });
            $('.datepicker').change(function (e) {
                e.preventDefault();
                var transaction_id = $(this).data('id');
                $.ajax({
                    dataType: 'json',
                    method: 'get',
                    url: adminSiteURL + 'dashboard',
                    data: {
                        date: $(this).val()
                    },
                    success: function (data) {
                        for (var index in data['territory']) {
                            if (transaction_id == data['territory'][index]['id']) {
                                str = '' +
                                    '<tr>' +
                                    '   <th>System</th>';
                                for (var i in data['payment_types']) {
                                    str += '<td>' + data['territory'][index]['loan_amounts'][i]['in'].toFixed(2) + '</td>';
                                    str += '<td>' + data['territory'][index]['loan_amounts'][i]['out'].toFixed(2) + '</td>';
                                }
                                str += '</tr>';
                                str += '' +
                                    '<tr>' +
                                    '   <th>Daily Turnovers</th>';
                                for (var i in data['payment_types']) {
                                    console.log(data['territory'][index]['turnovers'][i]);
                                    str += '<td>' + data['territory'][index]['turnovers'][i]['in'].toFixed(2) + '</td>';
                                    str += '<td>' + data['territory'][index]['turnovers'][i]['out'].toFixed(2) + '</td>';
                                }
                                str += '</tr>';
                                str += '' +
                                    '<tr>' +
                                    '   <th>Daily Turnovers  Correction</th>';
                                for (var i in data['payment_types']) {
                                    console.log(data['territory'][index]['correction'][i]);
                                    str += '<td>' + data['territory'][index]['correction'][i]['in'].toFixed(2) + '</td>';
                                    str += '<td>' + data['territory'][index]['correction'][i]['out'].toFixed(2) + '</td>';
                                }
                                str += '</tr>';
                                str += '' +
                                    '<tr>' +
                                    '   <th>Difference</th>';
                                for (var i in data['payment_types']) {
                                    str += '<td>' + (parseFloat(data['territory'][index]['loan_amounts'][i]['in']) - parseFloat(data['territory'][index]['turnovers'][i]['in'])).toFixed(2) + '</td>';
                                    str += '<td>' + (parseFloat(data['territory'][index]['loan_amounts'][i]['out']) - parseFloat(data['territory'][index]['turnovers'][i]['out'])).toFixed(2) + '</td>';
                                }
                                str += '</tr>';
                                $('#transactions_' + data['territory'][index]['id']).html(str);
                            }
                        }
                    }
                });
            });
        </script>
    @endif
@endsection
