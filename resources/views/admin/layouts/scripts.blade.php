<script>
    var resizefunc = [];
    var max_file_size = "{!! config('app.max_file_size') !!}";
</script>

<!-- jQuery  -->
<script src="{{url(config('theme.admin.js'))}}/jquery.min.js"></script>
<script src="{{url(config('theme.admin.js'))}}/popper.min.js"></script><!-- Popper for Bootstrap -->
<script src="{{url(config('theme.admin.js'))}}/bootstrap.min.js"></script>
<script src="{{url(config('theme.admin.js'))}}/detect.js"></script>
<script src="{{url(config('theme.admin.js'))}}/fastclick.js"></script>
<script src="{{url(config('theme.admin.js'))}}/jquery.slimscroll.js"></script>
<script src="{{url(config('theme.admin.js'))}}/jquery.blockUI.js"></script>
<script src="{{url(config('theme.admin.js'))}}/waves.js"></script>
<script src="{{url(config('theme.admin.js'))}}/wow.min.js"></script>
<script src="{{url(config('theme.admin.js'))}}/jquery.nicescroll.js"></script>
<script src="{{url(config('theme.admin.js'))}}/jquery.scrollTo.min.js"></script>
<script src="{{url(config('theme.admin.js'))}}/custom.js"></script>

<script src="{{url(config('theme.admin.plugins'))}}/peity/jquery.peity.min.js"></script>

<!-- jQuery  -->
<script src="{{url(config('theme.admin.plugins'))}}/waypoints/lib/jquery.waypoints.min.js"></script>
<script src="{{url(config('theme.admin.plugins'))}}/counterup/jquery.counterup.min.js"></script>

<script src="{{url(config('theme.admin.plugins'))}}/morris/morris.min.js"></script>
<script src="{{url(config('theme.admin.plugins'))}}/raphael/raphael-min.js"></script>

<script src="{{url(config('theme.admin.plugins'))}}/jquery-knob/jquery.knob.js"></script>

<script src="{{url(config('theme.admin.plugins'))}}/notifyjs/js/notify.js"></script>
<script src="{{url(config('theme.admin.plugins'))}}/notifications/notify-metro.js"></script>

<script src="{{url(config('theme.admin.js'))}}/jquery.core.js"></script>
<script src="{{url(config('theme.admin.js'))}}/jquery.app.js"></script>
<script src="{{url(config('theme.admin.js'))}}/jquery.form.js"></script>
<script src="{{url(config('theme.common.js'))}}/application.js"></script>
<script src="{{url(config('theme.common.js'))}}/lightbox.min.js"></script>
@if(auth()->check())
    <script>
        window.user_id = "{!! auth()->user()->id !!}";
        window.branch_id = "{!! session('branch_id') !!}";
    </script>
@endif
@if(env('BROADCAST_DRIVER')=='redis')
    <script src="//{{ Request::getHost() }}:6001/socket.io/socket.io.js"></script>
    <script src="{!!asset('js/app.js')!!}"></script>
@endif
<script type="text/javascript">
    var termsURL = '{{url('terms')}}';
    var loanShowUrl = '{{route('loan-applications.show','')}}/';
    var userAddressURL = '{{route('get.territory.address')}}';
    var transactionTableURL = '{{route('get.transactions','')}}/';
    var loanTypeURL = '{!! route('get-loan-type-info') !!}';
</script>
@yield('extra-js')

@yield('custom-js')
