<!DOCTYPE html >
<html lang="en">
<head>
    @include('client1.includes.head')
    <title>@yield('pageTitle') &bull; {!! config('app.name') !!}</title>
    @yield('contentHeader')
</head>
<body class="@yield('body_class')">
@include('client1.includes.header')
<div class="wrap">
    @yield('content')
</div>
@include('client1.includes.footer')
@if(auth()->check())
    @include('client1.popups.profile')
    @include('client1.popups.change_password_modal')
@endif

@include('client1.includes.php_js')
@include('client1.includes.scripts')
@yield('contentFooter')
</body>
</html>
