<div id="delete{!! $modal_name !!}Modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Delete Confirmation</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this {!! kebab_case($modal_name) !!}?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
                <button class="btn btn-primary confirmDelete{!! $modal_name !!}Button" data-type="{!! $modal_name !!}"
                        data-id="">
                    Yes
                </button>
            </div>
        </div>

    </div>
</div>