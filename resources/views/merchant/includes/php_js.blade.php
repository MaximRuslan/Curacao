<script type="text/javascript">
    var site_url = "{!! url('/') !!}/";
    var ajax_url = site_url + 'ajax/';
    var merchant_url = site_url + 'merchant/';
    var merchant_ajax_url = merchant_url + 'ajax/';
    var max_file_size = "{!! config('app.max_file_size') !!}";
    var keywords = {!! json_encode(__('keywords')) !!};
</script>