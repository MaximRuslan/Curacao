@extends('admin1.layouts.master')
@section('page_name')
    Permission Denied
@stop
@section('content')
    <div class="wrapper-page">
        <div class="ex-page-content text-center">
            <div class="text-error">
                <span class="text-primary">4</span><i class="ti-face-sad text-pink"></i><span
                        class="text-info">3</span>
            </div>
            <h2>Who0ps!<br>
                You don't have access for this page</h2>
            <br>
            <p class="text-muted">
                Sorry, an error has occured, Requested page not found!
            </p>
            <br>
            <a href="{{url('admin')}}" class="btn btn-default waves-effect waves-light">
                <span class="fa fa-home"></span>
                Take Me Home
            </a>
        </div>
    </div>
@stop