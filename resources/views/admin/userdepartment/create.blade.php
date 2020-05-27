<div id="userDepartmentModal" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <form action="{{route('user-department.store')}}" id="userDepartment_form" onsubmit="return SaveUserdepartment(this)">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title mt-0">User department</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">Title</label>
                                <input type="text" name="title" class="form-control" placeholder="e.g Sales">
                            </div>
                        </div>
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