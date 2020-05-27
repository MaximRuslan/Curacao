<div class="topbar">
    <!-- LOGO -->
    <div class="topbar-left">
        <div class="text-center">
            <a href="{{route('admin1.home')}}" class="logo">
                <i class="icon-c-logo "><img width="100%;" src="{{asset(config('site.admin_logo'))}}" height="60"/></i>
                <span><img src="{{asset(config('site.admin_logo'))}}" height="60"/></span>
            </a>
        </div>
    </div>
    <!-- Button mobile view to collapse sidebar menu -->
    <nav class="navbar-custom">
        <ul class="list-inline float-right mb-0">
            @if(auth()->user()->hasRole('super admin'))
                <li class="list-inline-item" style="width: 200px;">
                    {!! Form::select('country',['0'=>'All']+$country,$selected_country,['id'=>'js__country_site','style'=>'width:100% !important;','class'=>'form-control select2_country']) !!}
                </li>
            @endif
            @if(session()->has('branch_id'))
                <li class="list-inline-item">
                    {!! Form::text('',\App\Models\Branch::find(session('branch_id'))->title,['class'=>'form-control text-center','readonly']) !!}
                </li>
            @endif
            <li class="list-inline-item">
                {!! Form::text('',implode(',',auth()->user()->getRoleNames()->toArray()),['class'=>'form-control text-center','readonly']) !!}
            </li>
            <li class="list-inline-item dropdown notification-list">
                <a class="nav-link user-profile-link dropdown-toggle waves-effect waves-light nav-user"
                   data-toggle="dropdown" href="#" role="button"
                   aria-haspopup="false" aria-expanded="false">
                    @if(auth()->user()->profile_pic)
                        <img src="{{asset('uploads/'.auth()->user()->profile_pic)}}" alt="user"
                             class="rounded-circle">
                    @else
                        <i class="user-profile-icon fa fa-user-circle fa-3x "></i>
                    @endif
                </a>
                <div class="dropdown-menu dropdown-menu-right profile-dropdown " aria-labelledby="Preview">
                    <div class="dropdown-item noti-title">
                        <h5 class="text-overflow">
                            <small>Welcome {{auth()->user()->firstname.' '.auth()->user()->lastname}}</small>
                        </h5>
                    </div>
                    <a href="#" class="dropdown-item notify-item profileOpen">Profile</a>
                    <a href="#" class="dropdown-item notify-item changePasswordOpen">Change Password</a>

                    <a href="{!! url('logout') !!}" class="dropdown-item notify-item">Logout</a>
                </div>
            </li>
        </ul>
        <ul class="list-inline menu-left mb-0">
            <li class="float-left">
                <button class="button-menu-mobile open-left waves-light waves-effect">
                    <i class="dripicons-menu"></i>
                </button>
            </li>
        </ul>
    </nav>
</div>
