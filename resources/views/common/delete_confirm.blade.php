<div id="{{$modalId}}" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"
     style="display: none;">
    <div class="modal-dialog">
        <form action="{{$action}}">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title mt-0">Delete Confirmation</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <p>Are you sure, you want to delete this?</p>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="id">
                    <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">No</button>
                    <button type="button" class="btn btn-danger waves-effect waves-light"
                            onclick="DeleteItem(this,'{{$callback}}')">Yes, Delete
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
