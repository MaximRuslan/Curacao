<div id="changePasswordModal" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title mt-0">@lang('keywords.change password')</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
              <div class="row">
              <div class="col-md-12">
                  <div class="panel panel-default">
                      <div class="panel-body">
                          @if (session('normal_status'))
                              <div class="alert alert-success">
                                  {{ session('normal_status') }}
                              </div>
                          @endif
                          <form method="POST" action="{{route('post-profile')}}" onsubmit="return saveProfile(this)"
                                enctype='multipart/form-data'>
                              {{csrf_field()}}
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
                              <div class="col-md-12">
                                  <input type="hidden" name="type" value="password">
                                  <button type="submit" class="btn btn-primary pull-right profileSubmit">@lang('keywords.Save')</button>
                              </div>
                          </form>
                      </div>
                  </div>
                  </div>
              </div>
            </div>
            <div class="modal-footer">

            </div>
        </div>
    </div>
</div>
