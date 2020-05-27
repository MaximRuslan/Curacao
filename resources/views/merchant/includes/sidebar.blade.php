<!-- ========== Left Sidebar Start ========== -->

<div class="left side-menu">
    <div class="sidebar-inner slimscrollleft">
        <!--- Divider -->
        <div id="sidebar-menu">
            <ul>
                <li class="text-muted menu-title">Navigation</li>
                @foreach(config('merchant_sidebar') as $key=>$value)
                    @if(isset($value['values']))
                        <li class="has_sub">
                            <a href="javascript:void(0);" class="waves-effect">
                                <i class="{!! $value['icon'] !!}"></i>
                                <span>{!! __($value['name']) !!}</span><span class="menu-arrow"></span>
                            </a>
                            <ul class="list-unstyled">
                                @foreach($value['values'] as $p_key=>$p_value)
                                    <li>
                                        <a href="{!! Helper::returnUrl($p_value) !!}" class="waves-effect">
                                            {!! __($p_value['name']) !!}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @else
                        @if(\App\Library\Helper::authMerchantUser()->type==1 || !isset($value['permission']) || (isset($value['permission']) && \App\Library\Helper::authMerchantUser()[$value['permission']]==1))
                            <li>
                                <a href="{!! Helper::returnUrl($value) !!}" class="waves-effect">
                                    <i class="{!! $value['icon'] !!}"></i>
                                    <span>{!! __($value['name']) !!}</span>
                                </a>
                            </li>
                        @endif
                    @endif
                @endforeach
            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
<!-- Left Sidebar End -->