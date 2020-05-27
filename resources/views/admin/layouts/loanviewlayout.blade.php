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
    <!-- ========== Left Sidebar Start ========== -->

    <!-- Left Sidebar End -->
    <div class="content-page-view">
        <!-- Start content -->
        <div class="content-view">
            <div class="container-fluid">
                @yield('content')
            </div> <!-- container -->
        </div> <!-- content -->
        @include('admin.layouts.footer',['class'=>'footer-view'])
    </div>
    <!-- Right Sidebar -->
    <!-- /Right-bar -->

    <div class="ss_full_loader">
        <div class="ssfl_circle"></div>
        <p class="ssfl_text"></p>
    </div>


</div>
<!-- END wrapper -->
<script>
    var siteURL = "{!! url('/') !!}/";
    var ajaxURL = siteURL + 'ajax/';
    var adminSiteURL = siteURL + 'admin/';
    var adminAjaxURL = adminSiteURL + 'ajax/';
</script>
@include('admin.layouts.scripts')
</body>
</html>
