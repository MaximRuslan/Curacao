<div id="addNewFollowupModal" class="modal fade" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="add_new_note_form">
                <div class="modal-header">
                    <h4 class="modal-title mt-0">Add New Note</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label><input type="checkbox" name="priority" value="1">Priority</label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                {!!Form::label('date','Date *')!!}
                                {!!Form::text('date', old('date'), ['class'=>'form-control old-date-picker','placeholder'=>'Date'])!!}
                                <span class="error" for="date"></span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                {!!Form::label('follow_up','Follow up *')!!}
                                {!!Form::text('follow_up', old('follow_up'), ['class'=>'form-control date-picker','placeholder'=>'Follow up'])!!}
                                <span class="error" for="follow_up"></span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                {!!Form::label('details','Details')!!}
                                {!!Form::textarea('details', old('details'), ['class'=>'form-control','placeholder'=>'Details'])!!}
                                <span class="error" for="details"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="id">
                    <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                    <button class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
