@extends('admin1.layouts.master')
@section('page_name')
    Raffle Winners
@stop
@section('contentHeader')
@stop
@section('content')
    <div class="row">
        <div class="col-sm-12 m-b-20">
            <h4 class="page-title">Raffle Winners</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive">
                <table id="datatable" class="table  table-striped table-bordered" style="width:100%">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Month-Year</th>
                        <th>Winner</th>
                        <th>Winner</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

@stop
@section('contentFooter')
    <script src="{!! asset(mix('resources/js/admin/raffleWinner.js')) !!}"></script>
    <script>
        raffleWinner.init();
    </script>
@stop