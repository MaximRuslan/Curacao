<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="_token" content="{{ csrf_token() }}">
    <meta name="_base_url" content="{{ url('/') }}">
    <link rel="shortcut icon" href="{!! \App\Library\Helper::siteFavicon() !!}"/>

    <link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700" rel="stylesheet">

    <link rel="stylesheet" href="{!! asset(mix('resources/css/admin/login_style.css')) !!}">

    <title>@yield('page_name') {!! config('site.page_title') !!}</title>
    @yield('contentHeader')
</head>
<body data-spy="scroll" data-target="#navbar-menu">
@yield('container_content')
<nav class="navbar navbar-custom navbar-expand-lg navbar-light">
    <div class="container" style="text-align:center; @yield('container_css')">
        @yield('extra_image')
        <img src="{{asset(config('site.logo'))}}" height="60"/>
    <!-- <a class="logo col-md-6" href="{{ route('client1.home') }}">
        </a> -->
    </div>
</nav>
<div>
    @yield('content')
</div>
<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <p class="text-muted copyright">
                    @lang('keywords.copyright',['url'=>'<a href="https://www.sportential.com/" target="_blank">www.sportential.com</a>'])
                </p>
            </div>
            <div class="col-md-3 ml-auto">
                {{-- <ul class="social-icons text-md-right">
                    <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                    <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                    <li><a href="#"><i class="fa fa-google-plus"></i></a></li>
                </ul> --}}
            </div>
        </div> <!-- end row -->
    </div> <!-- end container -->
</footer>

<a href="#" class="back-to-top" id="back-to-top"> <i class="fa fa-angle-up"></i> </a>
@include('client1.includes.php_js')
<script src="{!! asset(mix('resources/js/admin/login_app.js')) !!}"></script>

@yield('contentFooter')
</body>
</html>
