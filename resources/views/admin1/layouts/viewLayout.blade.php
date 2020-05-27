<!DOCTYPE html>
<html lang="en">
<head>
    @include('admin1.includes.head')
    <title>@yield('page_name') {!! config('site.page_title') !!}</title>
    @yield('contentHeader')
</head>
<body class="fixed-left">
<div id="wrapper">
    @yield('content')
    @include('admin1.popups.profile')
    @include('admin1.popups.change_password')
</div>
<div class="ss_full_loader">
    <div class="ssfl_circle"></div>
    <p class="ssfl_text"></p>
</div>
@include('admin1.includes.php_js')
@include('admin1.includes.scripts')
@yield('contentFooter')
</body>
</html>
