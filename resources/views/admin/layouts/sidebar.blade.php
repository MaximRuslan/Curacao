<div class="left side-menu">
    <div class="sidebar-inner slimscrollleft">
        <div id="sidebar-menu">
            <ul>
                <li class="text-muted menu-title">Navigation</li>
                @if(auth()->user()->hasRole('super admin|admin|debt collector|loan approval|auditor|credit and processing') || session()->has('branch_id'))
                    @foreach(config('sidebar') as $key=>$value)
                        @if(isset($value['roles']) && auth()->user()->hasRole($value['roles']))
                            @if(isset($value['values']))
                                <li class="has_sub">
                                    <a href="javascript:void(0);" class="waves-effect">
                                        <i class="{!! $value['icon'] !!}"></i>
                                        <span>{!! $value['name'] !!}</span><span class="menu-arrow"></span>
                                    </a>
                                    <ul class="list-unstyled">
                                        @foreach($value['values'] as $p_key=>$p_value)
                                            @if(isset($p_value['roles']) && auth()->user()->hasRole($p_value['roles']))
                                                <li>
                                                    <a href="{!! Helper::returnUrl($p_value) !!}" class="waves-effect">
                                                        {!! $p_value['name'] !!}
                                                    </a>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </li>
                            @else
                                <li>
                                    <a href="{!! Helper::returnUrl($value) !!}" class="waves-effect">
                                        <i class="{!! $value['icon'] !!}"></i>
                                        <span>{!! $value['name'] !!}</span>
                                    </a>
                                </li>
                            @endif
                        @endif
                    @endforeach
                @endif
            </ul>
            {{--<div class="text-center">--}}
                {{--<a href="{{route('dashboard')}}" class="logo">--}}
                    {{--<i class="icon-c-logo ">--}}
                        {{--<img width="100%;" src="{{asset(config('site.logo'))}}" width="100px"/>--}}
                    {{--</i>--}}
                    {{--<span><img src="{{asset(config('site.logo'))}}" width="60"/></span>--}}
                {{--</a>--}}
            {{--</div>--}}
            <div class="clearfix"></div>
        </div>
        {{--<img src="{!! asset(config('site.logo')) !!}" alt="">--}}
        <div class="clearfix"></div>
    </div>
</div>
