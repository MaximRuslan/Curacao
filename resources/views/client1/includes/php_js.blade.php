<script>
    var siteURL = '{!! url('/') !!}/';
    var ajaxURL = siteURL + 'ajax/';
    var clientURL = '{!! url('client') !!}/';
    var clientAjaxURL = clientURL + 'ajax/';
    var dateFormat = "{!! config('site.date_format.js') !!}";
    var keywords = {!! json_encode(__('keywords')) !!};
    var max_file_size = "{!! config('app.max_file_size') !!}";
</script>