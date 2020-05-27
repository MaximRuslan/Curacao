<div id="profileModal" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title mt-0">@lang('keywords.profile')</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <form enctype='multipart/form-data' id="profileForm">
                <div class="modal-body">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="alertDiv"></div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">@lang('keywords.Firstname') *</label>
                                        <input class="form-control" name="firstname" disabled type="text" value="">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">@lang('keywords.Lastname') *</label>
                                        <input class="form-control" name="lastname" disabled type="text" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">@lang('keywords.EmailAddress')</label>
                                        <input name="email" type="email" class="form-control" disabled value="">
                                    </div>
                                </div>
                                {{--<div class="col-md-4" style="margin-top: 1.9rem !important;">
                                    <button class="btn btn-primary changeEmailAddress">Change Email Address</button>
                                </div>--}}
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!!Form::label('lang',__('keywords.Language').' *')!!}
                                        {!!Form::select('lang', config('site.language'),'', ['class'=>'form-control','required','placeholder'=>__('keywords.SelectLanguage')])!!}
                                    </div>
                                    <div class="error">
                                        <span class="help-block" for="lang"></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">@lang('keywords.Gender')</label><br>
                                        <label class="radio-inline">
                                            <input type="radio" name="sex" value="1">
                                            @lang('keywords.Male')
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="sex" value="2">
                                            @lang('keywords.Female')
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label"  data-toggle="tooltip" title="png,gif,jpg,jpeg">@lang('keywords.ProfilePic')</label><br>
                                        <input type="file" accept="image/*" id="profile_pic" name="profile_pic" class="form-control">
                                    </div>
                                    <div class="profile_pic_image col-md-12" style="display: none;">
                                        <div class="form-group loan-image thumb-small">
                                            <span class="helper"></span>
                                            <img src="" class="img-responsive" width="200">
                                            <button type="button" class="btn btn-danger delete-btn deleteProfilePic">
                                                @lang('keywords.DeleteImage')
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="col-md-12">
                                <input type="hidden" name="type" value="normal">

                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary pull-right">
                        @lang('keywords.Save')
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>