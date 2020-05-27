<!DOCTYPE html>
<html>
@include('admin.layouts.head')
<script type="text/javascript">
    var dateFormat = "{!! config('site.date_format.js') !!}";

    var siteURL = "{!! config('app.url') !!}";
    var isAdmin = "{!! auth()->user()->hasAnyRole(['admin','super admin']) ? 'true' : 'false' !!}";
</script>
<body class="fixed-left">
<!-- Begin page -->
<div id="wrapper">
@include('admin.layouts.header')
<!-- ========== Left Sidebar Start ========== -->
    @include('admin.layouts.sidebar')
    @role('manager')
    @include('admin.layouts.manager_sidebar')
    @endrole
    <!-- Left Sidebar End -->
    <div class="content-page">
        <!-- Start content -->
        <div class="content">
            <div class="container-fluid">
                @yield('content')
            </div> <!-- container -->
        </div> <!-- content -->
        @include('admin.layouts.footer')
    </div>
    <!-- Right Sidebar -->
    <!-- /Right-bar -->

    <div class="ss_full_loader">
        <div class="ssfl_circle"></div>
        <p class="ssfl_text"></p>
    </div>


</div>
<!-- END wrapper -->
@include('common.profile_modal')
@include('common.change_password_modal')
@include('late_cash_payout_modal')
{{--<div class="loader"></div>--}}
<script>
    var siteURL = "{!! url('/') !!}/";
    var ajaxURL = siteURL + 'ajax/';
    var adminSiteURL = siteURL + 'admin/';
    var adminAjaxURL = adminSiteURL + 'ajax/';
</script>
@include('admin.layouts.scripts')
</body>
</html>
