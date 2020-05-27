<script>
    var max_file_size = "{!! config('app.max_file_size') !!}";
</script>
<script src="{{url(config('theme.front.js'))}}/jquery.min.js"></script>
<script src="{{url(config('theme.front.js'))}}/popper.min.js"></script>
<script src="{{url(config('theme.front.js'))}}/bootstrap.min.js"></script>
<script src="{{url(config('theme.front.plugins'))}}/moment/moment.js"></script>
<script src="{{url(config('theme.front.plugins'))}}/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<script src="{{url(config('theme.front.plugins'))}}/bootstrap-daterangepicker/daterangepicker.js"></script>

<script src="{{url(config('theme.front.plugins'))}}/loader/jquery.loader.js"></script>

<!-- Jquery easing -->
<script type="text/javascript" src="{{url(config('theme.front.js'))}}/jquery.easing.1.3.min.js"></script>

<!-- Owl Carousel -->
<script type="text/javascript" src="{{url(config('theme.front.js'))}}/owl.carousel.min.js"></script>

<!--common script for all pages-->
<script src="{{url(config('theme.front.js'))}}/jquery.app.js"></script>
<script src="{{url(config('theme.common.js'))}}/application.js"></script>
<script src="{{url(config('theme.admin.js'))}}/jquery.form.js"></script>
<script src="{{url(config('theme.common.js'))}}/lightbox.min.js"></script>
<script type="text/javascript">
    var termsURL = '{{url('terms')}}';
    var loanShowUrl = '{{route('loan-applications.show','')}}/'
    var userAddressURL = '{{route('get.territory.address')}}';
    var transactionTableURL = '{{route('get.transactions','')}}/';
    var loanTypeURL = '{!! route('get-loan-type-info') !!}';
    var siteURL = '{!! url('/') !!}/';
    var ajaxURL = '{!! url('/') !!}/ajax/';
</script>
<script src="{{url(config('theme.admin.plugins'))}}/sweet-alert2/sweetalert2.js"></script>
@yield('extra-js')
<script type="text/javascript">
    $('.owl-carousel').owlCarousel({
        loop: true,
        margin: 10,
        nav: false,
        autoplay: true,
        autoplayTimeout: 4000,
        responsive: {
            0: {
                items: 1
            }
        }
    });
</script>
@yield('custom-js')