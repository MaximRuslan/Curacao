<!DOCTYPE html>
<html>
<head>
    @include('merchant.includes.head')

    <title>@yield('page_name') | {!! config('app.name') !!}</title>
    @yield('header')
</head>


<body class="fixed-left">

<!-- Begin page -->
<div id="wrapper">

@include('merchant.includes.header')


@include('merchant.includes.sidebar')


<!-- ============================================================== -->
    <!-- Start right Content here -->
    <!-- ============================================================== -->
    <div class="content-page">
        <!-- Start content -->
        <div class="content">
            <div class="container-fluid">

                <!-- Page-Title -->
                <div class="row">
                    <div class="col-sm-12">
                        <div class="btn-group pull-right m-t-15">
                            @yield('action_buttons')
                        </div>
                        <h4 class="page-title">@yield('page_name')</h4>
                        <ol class="breadcrumb">
                        </ol>
                    </div>
                </div>

                @yield('content')


            </div> <!-- container -->

        </div> <!-- content -->

        @include('merchant.includes.footer')

    </div>


    <!-- ============================================================== -->
    <!-- End Right content here -->
    <!-- ============================================================== -->
    @include('merchant.popups.change_password')
    @yield('popups')
</div>
<!-- END wrapper -->


@include('merchant.includes.php_js')
@include('merchant.includes.scripts')
@yield('footer')

</body>
</html>