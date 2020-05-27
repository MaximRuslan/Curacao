<div id="templateModal" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <form id="template_form">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title mt-0">Template</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!!Form::label('name','Name *')!!} <span><i class="fa fa-info-circle" data-toggle="tooltip" title="" id="js--key-tooltip"></i></span>
                                {!!Form::text('name', old('name'), ['class'=>'form-control','placeholder'=>'Name'])!!}
                                <span class="error" for="name"></span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <h6>Support Elements</h6>
                            <h5 for="params"></h5>
                        </div>
                        <div class="col-md-12">
                            <h6>Receivers</h6>
                            <h5 for="receivers"></h5>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <ul class="nav nav-tabs tabs">
                                <li class="tab">
                                    <a href="#lang-eng" id="language-eng-click" data-toggle="tab" aria-expanded="false">
                                        ENG
                                    </a>
                                </li>
                                <li class="tab">
                                    <a href="#lang-esp" data-toggle="tab" aria-expanded="false">
                                        ESP
                                    </a>
                                </li>
                                <li class="tab">
                                    <a href="#lang-pap" data-toggle="tab" aria-expanded="true">
                                        PAP
                                    </a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane" id="lang-eng">
                                    <div class="row">
                                        <div class="col-md-12 js--subject-div">
                                            <div class="form-group">
                                                {!!Form::label('subject','Subject *')!!}
                                                {!!Form::text('subject', old('subject'), ['class'=>'form-control','placeholder'=>'Subject'])!!}
                                                <span class="error" for="subject"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                {!!Form::label('subject','Content *')!!}
                                                {!!Form::textarea('content', old('content'), ['class'=>'form-control cms_textarea','placeholder'=>'Content'])!!}
                                                <span class="error" for="content"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="lang-esp">
                                    <div class="row">
                                        <div class="col-md-12 js--subject-div">
                                            <div class="form-group">
                                                {!!Form::label('subject','Subject *')!!}
                                                {!!Form::text('subject_esp', old('subject_esp'), ['class'=>'form-control','placeholder'=>'Subject'])!!}
                                                <span class="error" for="subject_esp"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                {!!Form::label('subject','Content *')!!}
                                                {!!Form::textarea('content_esp', old('content_esp'), ['class'=>'form-control cms_textarea','placeholder'=>'Content'])!!}
                                                <span class="error" for="content_esp"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="lang-pap">
                                    <div class="row">
                                        <div class="col-md-12 js--subject-div">
                                            <div class="form-group">
                                                {!!Form::label('subject','Subject *')!!}
                                                {!!Form::text('subject_pap', old('subject_pap'), ['class'=>'form-control','placeholder'=>'Subject'])!!}
                                                <span class="error" for="subject_pap"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                {!!Form::label('subject','Content *')!!}
                                                {!!Form::textarea('content_pap', old('content_pap'), ['class'=>'form-control cms_textarea','placeholder'=>'Content'])!!}
                                                <span class="error" for="content_pap"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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