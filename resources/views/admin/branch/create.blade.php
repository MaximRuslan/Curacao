<div id="branchModal" class="modal fade" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <form action="{{route('branch.store')}}" id="branch_form"
              onsubmit="return SaveBranch(this)">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title mt-0">Branch</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">Country</label>
                                {!! Form::select('country_id',$countries,old('country_id'),['class'=>'form-control','placeholder'=>"Select Country",'required']) !!}
                            </div>
                            <div class="error">
                                <span class="help-block" for="country_id"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">Name</label>
                                <input type="text" name="title" class="form-control" placeholder="Name">
                            </div>
                            <div class="error">
                                <span class="help-block" for="title"></span>
                            </div>
                        </div>
                    </div>
                    {{--<div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">Title ES</label>
                                <input type="text" name="title_es" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">Title NL</label>
                                <input type="text" name="title_nl" class="form-control">
                            </div>
                        </div>
                    </div>--}}
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
