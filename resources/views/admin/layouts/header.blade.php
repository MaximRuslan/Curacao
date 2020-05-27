<div class="topbar">

    <!-- LOGO -->
    <div class="topbar-left">
        <div class="text-center">
            <a href="{{route('dashboard')}}" class="logo">
                <i class="icon-c-logo "><img width="100%;" src="{{asset(config('site.logo'))}}" height="60"/></i>
                <span><img src="{{asset(config('site.logo'))}}" height="60"/></span>
            </a>
        </div>
    </div>

    <!-- Button mobile view to collapse sidebar menu -->
    <nav class="navbar-custom">

        <ul class="list-inline float-right mb-0">
            @if(auth()->user()->hasRole('super admin'))
                <li class="list-inline-item">
                    {!! Form::select('country',['1'=>'All']+$country,$selected_country,['id'=>'js__country_site','class'=>'form-control']) !!}
                </li>
            @endif
            @if(session()->has('branch_id'))
                <li class="list-inline-item">
                    {!! Form::text('',\App\Models\Branch::find(session('branch_id'))->title,['class'=>'form-control text-center','readonly']) !!}
                </li>
            @endif
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
                    <!-- item-->
                    <div class="dropdown-item noti-title">
                        <h5 class="text-overflow">
                            <small>Welcome {{auth()->user()->firstname.' '.auth()->user()->lastname}}</small>
                        </h5>
                    </div>
                    <a href="#profileModal" data-toggle="modal" class="dropdown-item notify-item">Profile</a>
                    <a href="#changePasswordModal" data-toggle="modal" class="dropdown-item notify-item">Change
                        Password</a>

                    <a href="{!! url('logout') !!}"
                       {{--onclick="event.preventDefault(); document.getElementById('logout-form').submit();"--}}
                       class="dropdown-item notify-item">
                        Logout</a>

                    {{--<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        {{ csrf_field() }}
                    </form>--}}
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
