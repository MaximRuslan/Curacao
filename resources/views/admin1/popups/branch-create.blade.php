<div id="branchModal" class="modal fade" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <form id="branch_form">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title mt-0">Branch</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!!Form::label('country_id','Country *')!!}
                                {!!Form::select('country_id',$countries,old('country_id'),['class'=>'form-control','id'=>'country_id','placeholder'=>'Select Country'])!!}
                                <span class="error" for="country_id"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!!Form::label('title','Title *')!!}
                                {!!Form::text('title', old('title'), ['class'=>'form-control','placeholder'=>'Title'])!!}
                                <span class="error" for="title"></span>
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