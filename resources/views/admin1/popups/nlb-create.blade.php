<div id="nlbsModal" class="modal fade" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <form id="transaction_form">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title mt-0">NLB Transactions</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">Date *</label>
                                <input value="{!! \App\Library\Helper::date_to_sheet_timezone(date('Y-m-d H:i:s')) !!}" type="text" class="old-date-picker form-control" name="date">
                                <span class="error" for="date"></span>
                            </div>
                        </div>
                        @if(auth()->user()->hasRole('super admin') && !session()->has('country'))
                            <div class="col-md-12">
                                <div class="form-group">
                                    {!!Form::label('country_id','Country *')!!}
                                    {!!Form::select('country_id',$countries,old('country_id'),['class'=>'form-control','id'=>'country_selection','placeholder'=>'Select Country'])!!}
                                    <span class="error" for="country_id"></span>
                                </div>
                            </div>
                        @endif
                        @if(auth()->user()->hasRole('super admin|admin|credit and processing'))
                            <div class="col-md-12">
                                <div class="form-group">
                                    @if(!session()->has('country') && auth()->user()->hasRole('super admin'))
                                        {!!Form::label('branch_id','Branch *')!!}
                                        {!!Form::select('branch_id',[],old('branch_id'),['class'=>'form-control','id'=>'branch_selection','placeholder'=>'Select Branch'])!!}
                                    @else
                                        {!!Form::label('branch_id','Branch *')!!}
                                        {!!Form::select('branch_id',$branches,old('branch_id'),['class'=>'form-control','id'=>'branch_selection','placeholder'=>'Select Branch'])!!}
                                    @endif
                                    <span class="error" for="branch_id"></span>
                                </div>
                            </div>
                        @endif
                        <div class="col-md-12">
                            <div class="form-group">
                                {!!Form::label('type','Type *')!!}
                                {!!Form::select('type',['1'=>"IN",'2'=>"OUT"],old('type'),['class'=>'form-control select2single','placeholder'=>'Select Type','id'=>'type','required'])!!}
                                <span class="error" for="type"></span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                {!!Form::label('reason','Reason *')!!}
                                {!!Form::select('reason',[],old('reason'),['class'=>'form-control select2single','placeholder'=>'Select Reason','required','id'=>'reason_selection'])!!}
                                <span class="error" for="type"></span>
                            </div>
                        </div>
                        @foreach(config('site.payment_types') as $key=>$value)
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!!Form::label('amount.'.$key,$value)!!}
                                    {!!Form::number('amount['.$key.']', old('amount.'.$key), ['class'=>'form-control amount_change','placeholder'=>$value,'step'=>'0.01'])!!}
                                </div>
                            </div>
                        @endforeach
                        <div class="col-md-12">
                            <div class="form-group">
                                {!!Form::label('total_amount','Total Amount')!!}
                                {!!Form::number('total_amount', old('total_amount'), ['class'=>'form-control','id'=>'total_amount','placeholder'=>'Total Amount'])!!}
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                {!!Form::label('desc','Description')!!}
                                {!!Form::textarea('desc', old('desc'), ['class'=>'form-control','placeholder'=>'Description'])!!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="id">
                    <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-info waves-effect waves-light">Save changes</button>
                </div>
            </div>
        </form>
    </div>
</div>