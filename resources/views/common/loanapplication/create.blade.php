<div id="loanApplicationModal" class="modal fade" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <form onsubmit="return SaveStatusApplication(this)" id="decline_form">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title mt-0 jq__title">Reject Loan</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="js__status">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">Reason *</label>
                                <select name="decline_reason" id="decline_reason" class="form-control">
                                    @foreach($declineReasons->sortBy('title') as $reason)
                                        <option value="{{$reason->id}}">{{$reason->title}}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="id" value="">
                            </div>
                            <div class="form-group">
                                <label class="control-label">Description</label>
                                <textarea name="decline_reason_description" id="decline_description" rows="10"
                                          class="form-control"></textarea>
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
