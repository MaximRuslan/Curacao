<!-- Top Bar Start -->
<div class="topbar">


    <!-- LOGO -->
    <div class="topbar-left">
        <div class="text-center">
            <a href="{{route('merchant.home.index')}}" class="logo">
                <i class="icon-c-logo "><img width="100%;" src="{{asset(config('site.admin_logo'))}}" height="60"/></i>
                <span><img src="{{asset(config('site.admin_logo'))}}" height="60"/></span>
            </a>
        </div>
    </div>

    <!-- Button mobile view to collapse sidebar menu -->
    <nav class="navbar-custom">

        <ul class="list-inline float-right mb-0">
            @if(\App\Library\Helper::authMerchantUser()->type==1)
                <li class="list-inline-item" style="width: 200px;">
                    {!! Form::select('branch',$branches,$selected_branch,['id'=>'js--branch-id','style'=>'width:100% !important;','class'=>'form-control select2Single']) !!}
                </li>
            @endif
            <li class="list-inline-item dropdown notification-list">
                <a class="nav-link dropdown-toggle waves-effect waves-light nav-user" data-toggle="dropdown" href="#" role="button"
                   aria-haspopup="false" aria-expanded="false">
                    <i class="user-profile-icon fa fa-user-circle fa-3x "></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right profile-dropdown " aria-labelledby="Preview">
                    <!-- item-->
                    <div class="dropdown-item noti-title">
                        <h5 class="text-overflow">
                            <small>{!! $merchant->first_name !!} {!! $merchant->last_name !!}</small>
                        </h5>
                    </div>
                    <a href="#" class="dropdown-item notify-item changePasswordOpen">
                        <i class="fa fa-key"></i>@lang('keywords.change password')
                    </a>
                    <a href="{!! route('merchant.logout') !!}" class="dropdown-item notify-item">
                        <i class="fa fa-sign-out"></i>@lang('keywords.logout')
                    </a>

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
<!-- Top Bar End -->