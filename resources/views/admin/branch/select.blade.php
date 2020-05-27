@extends('admin.layouts.app')
@section('page_name')
    Branch Select
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
            <h4 class="page-title">Branch Select</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive">
                {!!Form::open(['url'=>'admin/branch/select'])!!}
                <div class="col-md-12">
                    <div class="col-md-4">
                        {!!Form::label('branch_id','Branch Select')!!}
                        {!!Form::select('branch_id',$branches,old('branch_id'),['class'=>'form-control','placeholder'=>'Branch Select'])!!}
                        @if($errors->has('branch_id'))
                            <p class="help-block">{!!$errors->first('branch_id')!!}</p>
                        @endif
                    </div>
                </div>
                <div class="col-md-12 mt-3">
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">Select</button>
                    </div>
                </div>
                {!!Form::close()!!}
            </div>
        </div>
    </div>
@endsection

@section('extra-js')
    <script src="{{url(config('theme.admin.plugins'))}}/datatables/jquery.dataTables.min.js"></script>
    <script src="{{url(config('theme.admin.plugins'))}}/datatables/dataTables.bootstrap4.min.js"></script>

    <script src="{{url(config('theme.admin.plugins'))}}/datatables/dataTables.responsive.min.js"></script>
    <script src="{{url(config('theme.admin.plugins'))}}/datatables/responsive.bootstrap4.min.js"></script>
@endsection

@section('custom-js')

@endsection
