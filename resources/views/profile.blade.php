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
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">@lang('keywords.Firstname') *</label>
                                <input class="form-control"
                                       @if(auth()->user()->hasRole('client'))
                                       disabled
                                       @else
                                       name="firstname"
                                       type="text"
                                       @endif
                                       value="{{auth()->user()->firstname}}">
                            </div>
                            <div class="error">
                                <span class="help-block" for="firstname"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">@lang('keywords.Lastname') *</label>
                                <input class="form-control"
                                       @if(auth()->user()->hasRole('client'))
                                       disabled
                                       @else
                                       name="lastname"
                                       type="text"
                                       @endif
                                       value="{{auth()->user()->lastname}}">
                            </div>
                            <div class="error">
                                <span class="help-block" for="lastname"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="control-label">@lang('keywords.EmailAddress')</label>
                                    <input name="email" class="form-control" disabled
                                           value="{!! auth()->user()->email !!}">
                                </div>
                            </div>
                            <div class="col-md-4 mt-4">
                                <button class="btn btn-danger changeEmailAddress">Change Email Address</button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {!!Form::label('lang',__('keywords.Language').' *')!!}
                                {!!Form::select('lang', config('site.language'),auth()->user()->lang, ['class'=>'form-control','required','placeholder'=>__('keywords.SelectLanguage'),'id'=>'lang'])!!}
                            </div>
                            <div class="error">
                                <span class="help-block" for="lang"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">@lang('keywords.Gender')</label><br>
                                <label class="radio-inline">
                                    <input type="radio" name="sex"
                                           value="1" {{auth()->user()->sex==1 ? "checked" : ""}}>
                                    @lang('keywords.Male')
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="sex"
                                           value="2" {{auth()->user()->sex==2 ? "checked" : ""}}>
                                    @lang('keywords.Female')
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">@lang('keywords.ProfilePic')</label>
                                <input type="file" id="profile_pic" name="profile_pic" class="form-control">
                                <input type="hidden" name="removeImage">
                            </div>
                        </div>
                        @if(auth()->user()->profile_pic)
                            <div class="col-md-12">
                                <div class="form-group loan-image thumb-small">
                                    <span class="helper"></span>
                                    <img src="{{asset('uploads/'.auth()->user()->profile_pic)}}" class="img-responsive">
                                    <button type="button" onclick="removeProfilePic(this)"
                                            class="btn btn-danger delete-btn">@lang('keywords.DeleteImage')
                                    </button>
                                </div>
                            </div>
                        @endif
                        <div class="clearfix"></div>
                    </div>
                    <div class="col-md-12">
                        <input type="hidden" name="type" value="normal">
                        <button type="submit"
                                class="btn btn-primary pull-right profileSubmit">@lang('keywords.Save')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
