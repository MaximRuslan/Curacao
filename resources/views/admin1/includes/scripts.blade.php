<script src="{!! asset(mix('resources/js/admin/app.js')) !!}"></script>
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var resizefunc = [];
</script>


@if(env('BROADCAST_DRIVER')=='redis')
    <!-- <script src="//{{ Request::getHost() }}:6002/socket.io/socket.io.js"></script> -->
    <script src="{!!asset('js/app.js')!!}"></script>
@endif
<script>
    var dateFormat = "{!! config('site.date_format.js') !!}";
    profile.init();
    $(document).ready(function () {
        $('.select2_country').select2();
    });
</script>