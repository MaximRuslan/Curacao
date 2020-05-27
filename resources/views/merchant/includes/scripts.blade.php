<script>
    var resizefunc = [];
</script>

<!-- jQuery  -->
<script src="{!! asset(mix('resources/js/merchant/app.js')) !!}"></script>
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
</script>
<script src="{!! asset(mix('resources/js/merchant/common.js')) !!}"></script>