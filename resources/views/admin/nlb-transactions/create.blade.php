<div id="NLBTransactionsModel" class="modal fade" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <form action="{{route('nlb-transactions.store')}}" id="transaction_form" onsubmit="return SaveNLB(this)">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title mt-0">NLB Transactions</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!!Form::label('type','Type')!!}
                                {!!Form::select('type',['1'=>"IN",'2'=>"OUT"],old('type'),['class'=>'form-control select2','placeholder'=>'Select Type','id'=>'type','required'])!!}
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                {!!Form::label('reason','Reason')!!}
                                {!!Form::select('reason',[],old('reason'),['class'=>'form-control select2','placeholder'=>'Select Reason','required','id'=>'reason_selection'])!!}
                            </div>
                        </div>
                        @foreach(config('site.payment_types') as $key=>$value)
                            <div class="col-md-6">
                                {!!Form::label('amount.'.$key,$value)!!}
                                {!!Form::number('amount['.$key.']', old('amount.'.$key), ['class'=>'form-control amount_change','placeholder'=>$value,'step'=>'0.01'])!!}
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