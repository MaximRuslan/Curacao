<div id="userModal" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"
     style="display: none;">
    <div class="modal-dialog modal-lg" style="min-width: 1360px !important;">
        {{--        <form action="{{route('users.store')}}" id="user_form" onsubmit="return SaveUser(this)"--}}
        {{--enctype='multipart/form-data'>--}}
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title mt-0">User</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <!-- Wizard with Validation -->

                <div class="row">
                    <div class="col-sm-12">
                        <div class="card-box">
                            <div id="wizard-validation-form" action="#">
                                <div>
                                    <h3>User Info</h3>
                                    <section>
                                        <form id="usersInfoForm">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label">Firstname</label>
                                                        <input type="text" name="firstname" class="form-control"
                                                               placeholder="e.g John" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label">Lastname</label>
                                                        <input type="text" name="lastname" class="form-control"
                                                               placeholder="e.g Smith" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label">Email</label>
                                                        <input type="email" name="email" class="form-control"
                                                               placeholder="e.g johnsmith@gmail.com" required>
                                                        <span class="help-block" id="email_error"></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label">Phone</label>
                                                        <input type="text" name="mobile_no"
                                                               class="form-control numeric-input"
                                                               placeholder="12345678912">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label">Role</label>
                                                        <select name="role_id" class="form-control"
                                                                onchange="toggleStatus(this)">
                                                            @foreach($roles as $role)
                                                                <option value="{{$role->id}}">{{$role->name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 status-group" style="display: none">
                                                    <div class="form-group">
                                                        <label class="control-label">Status</label>
                                                        <select name="status" class="form-control">
                                                            @foreach($status as $item)
                                                                <option value="{{$item->id}}">{{$item->title}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label">Gender</label><br>
                                                        <label class="radio-inline">
                                                            <input type="radio" name="sex" value="1" checked>Male
                                                        </label>
                                                        <label class="radio-inline">
                                                            <input type="radio" name="sex" value="2">Female
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label">Territory</label>
                                                        <select name="territory" class="form-control">
                                                            @foreach($territories as $territory)
                                                                <option value="{{$territory->id}}">{{$territory->title}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label">ID Number</label>
                                                        <input type="text" class="form-control" name="id_number">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label">Profile Pic</label>
                                                        <input type="file" id="profile_pic" name="profile_pic"
                                                               class="form-control">
                                                        <input type="hidden" name="removeImage">
                                                    </div>
                                                </div>
                                                {{--<div class="col-md-6">--}}
                                                {{--<div class="form-group">--}}
                                                {{--<label for="control-label">--}}
                                                {{--<label class="control-label">Preferred Language</label>--}}
                                                {{--{!!Form::label('language','Preferred Language')!!}--}}
                                                {{--{!!Form::select('language',$languages,old('language'),['class'=>'form-control','placeholder'=>'Preferred Language'])!!}--}}
                                                {{--</label>--}}
                                                {{--</div>--}}
                                                {{--</div>--}}
                                                <div class="profile-pic-holder col-md-6" style="display: none">
                                                    <div class="form-group loan-image thumb-small">
                                                        <span class="helper"></span>
                                                        <img src="" class="img-responsive">
                                                        <button type="button" onclick="removeProfilePic(this)"
                                                                class="btn btn-danger delete-btn">Delete image
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="form-group clearfix">
                                                    <label class="col-lg-12 control-label ">(*) Mandatory</label>
                                                </div>
                                            </div>
                                        </form>
                                    </section>
                                    <h3>User Work Info</h3>
                                    <section>
                                        <form id="usersWorkInfoForm" action="">
                                            <div class="text-right mb-4">
                                                <button class="btn btn-primary addUserWorkInfo">
                                                    <i class="fa fa-plus"></i>
                                                </button>
                                            </div>
                                            <table class="table table-bordered table-user">
                                                <thead>
                                                <tr>
                                                    <th>Company Name</th>
                                                    <th>Function</th>
                                                    <th>Telephone</th>
                                                    <th>Mail</th>
                                                    <th>Action</th>
                                                </tr>
                                                </thead>
                                                <tbody class="tableWorkInfo"></tbody>
                                            </table>
                                        </form>
                                    </section>
                                    <h3>User Bank Info</h3>
                                    <section>
                                        <form id="usersBankInfoForm" action="">
                                            <div class="text-right mb-4">
                                                <button class="btn btn-primary addUserBankInfo">
                                                    <i class="fa fa-plus"></i>
                                                </button>
                                            </div>
                                            <table class="table table-bordered table-user">
                                                <thead>
                                                <tr>
                                                    <th>Account Number</th>
                                                    <th>Bank Name</th>
                                                    <th>Action</th>
                                                </tr>
                                                </thead>
                                                <tbody class="tableBankInfo"></tbody>
                                            </table>
                                        </form>
                                    </section>
                                    <h3>User Reference Info</h3>
                                    <section>
                                        <form id="usersReferenceInfoForm" action="">
                                            <div class="text-right mb-4">
                                                <button class="btn btn-primary addUserReferenceInfo">
                                                    <i class="fa fa-plus"></i>
                                                </button>
                                            </div>
                                            <table class="table table-bordered table-user">
                                                <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Telephone 1</th>
                                                    <th>Telephone 2</th>
                                                    <th>Email</th>
                                                    <th>Action</th>
                                                </tr>
                                                </thead>
                                                <tbody class="tableReferenceInfo"></tbody>
                                            </table>
                                        </form>
                                    </section>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End row -->

            </div>
            <div class="modal-footer">
                <input type="hidden" name="id">
                <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Close</button>
            </div>
        </div>
        {{--</form>--}}
    </div>
</div>