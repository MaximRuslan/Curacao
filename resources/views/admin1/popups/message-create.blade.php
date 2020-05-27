<div id="messageModel" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"
     style="display: none;">
    <div class="modal-dialog">
        <form id="messageModelForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title mt-0">SMS Create</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        @if(auth()->user()->hasRole('super admin'))
                            <div class="col-md-12">
                                <div class="form-group">
                                    {!!Form::label('country_id','Countries *')!!}
                                    {!!Form::select('country_id',$countries,old('country_id'),['class'=>'form-control select2single','placeholder'=>"All",'id'=>'country_select'])!!}
                                </div>
                            </div>
                        @endif
                        <div class="col-md-12">
                            <div class="form-group">
                                {!!Form::label('status','Status *')!!}
                                {!!Form::select('status',$statuses,old('status'),['class'=>'form-control select2single','placeholder'=>"All",'id'=>'status_select'])!!}
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                {!!Form::label('loan','Loan *')!!}
                                {!!Form::select('loan',$loans,old('loan'),['class'=>'form-control select2single','placeholder'=>"All",'id'=>'loans_select'])!!}
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group row">
                                <div class="col-md-8">
                                    {!!Form::label('user_id','Users *')!!}
                                    {!!Form::select('user_id[]',$users,old('user_id'),['class'=>'form-control','multiple','id'=>'user_select'])!!}
                                    <span class="error" for="user_id[]"></span>
                                </div>
                                <div class="col-md-4" style="margin-top: 35px;">
                                    <label>
                                        {!!Form::checkbox('select_all', '1',false,['id'=>'select_all_checkbox'])!!}
                                        Select All
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                {!!Form::label('message','Message *')!!}
                                {!!Form::textarea('message', old('message'), ['class'=>'form-control','placeholder'=>'Message','required','maxlength'=>160])!!}
                                <span class="error" for="message"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-info waves-effect waves-light">Send</button>
                </div>
            </div>
        </form>
    </div>
</div>