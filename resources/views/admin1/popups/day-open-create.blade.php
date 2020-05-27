<div id="dayOpenModal" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"
     style="display: none;">
    <div class="modal-dialog">
        <form id="dayopenForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title mt-0 jq--title">Day Open - </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            {!!Form::label('date','Date *')!!}
                            {!!Form::text('date',\App\Library\Helper::date_to_sheet_timezone(date('Y-m-d H:i:s')), ['class'=>'form-control old-date-picker','placeholder'=>'Date','required'])!!}
                            <span class="error date_error" for="date"></span>
                        </div>
                        @if(auth()->user()->hasRole('super admin') && !session()->has('country'))
                            <div class="col-md-12">
                                {!!Form::label('country_id','Country *')!!}
                                {!!Form::select('country_id',$countries,old('country_id'),['class'=>'form-control','id'=>'country_selection','placeholder'=>'Select Country'])!!}
                                <span class="error" for="country_id"></span>
                            </div>
                        @endif
                        @if(auth()->user()->hasRole('super admin|admin|credit and processing'))
                            <div class="col-md-12">
                                @if(!session()->has('country') && auth()->user()->hasRole('super admin'))
                                    {!!Form::label('branch_id','Branch *')!!}
                                    {!!Form::select('branch_id',[],old('branch_id'),['class'=>'form-control','id'=>'branch_selection','placeholder'=>'Select Branch'])!!}
                                @else
                                    {!!Form::label('branch_id','Branch *')!!}
                                    {!!Form::select('branch_id',$branches,old('branch_id'),['class'=>'form-control','id'=>'branch_selection','placeholder'=>'Select Branch'])!!}
                                @endif
                                <span class="error" for="branch_id"></span>
                            </div>
                        @endif
                        <div class="col-md-12">
                            <h5>Amount</h5>
                        </div>
                        <div class="col-md-12">
                            <div class="row">
                                @foreach($payment_types as $key=>$type)
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label payment_type_name" data-id="{!! $key !!}">
                                                {!! $type !!}
                                            </label>
                                            <input type="text" name="amount[{!! $key !!}]"
                                                   class="form-control numeric-input payment_type_value_{!! $key !!}">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Total</label>
                                <input type="text" name="total" class="form-control numeric-input">
                                <p class="total_payment_amount_error error"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="branch">
                    <input type="hidden" name="old_date">
                    <input type="hidden" name="user">
                    <input type="hidden" name="type" value="{!! request('type') !!}">
                    <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-info waves-effect waves-light">Save changes</button>
                </div>
            </div>
        </form>
    </div>
</div>
