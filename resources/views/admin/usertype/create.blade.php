<div id="userTypeModal" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <form action="{{route('user-types.store')}}" id="userType_form" onsubmit="return SaveUsertype(this)">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title mt-0">User type</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">Name</label>
                                <input type="text" name="name" class="form-control" placeholder="e.g Admin">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">Name ES</label>
                                <input type="text" name="name_es" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">Name NL</label>
                                <input type="text" name="name_nl" class="form-control">
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