<div id="changePasswordModal" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title mt-0">@lang('keywords.change password')</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <form id="changePasswordForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="control-label">@lang('keywords.OldPassword') *</label>
                                                <input type="password" name="old_password" class="form-control">
                                                <span for="old_password" class="help-block text-danger"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="control-label">@lang('keywords.NewPassword') *</label>
                                                <input type="password" name="new_password" class="form-control">
                                                <span for="new_password" class="help-block text-danger"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="control-label">@lang('keywords.ConfirmPassword') *</label>
                                                <input type="password" name="confirm_password" class="form-control">
                                                <span for="confirm_password" class="help-block text-danger"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="type" value="password">
                    <button type="submit" class="btn btn-primary pull-right profileSubmit">
                        @lang('keywords.Save')
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
s