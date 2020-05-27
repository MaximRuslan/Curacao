<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="_token" content="{{ csrf_token() }}">
    <meta name="_base_url" content="{{ url('/') }}">

    <title>{!! config('app.name') !!}</title>

    <link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700" rel="stylesheet">

    <link href="{{url(config('theme.front.css'))}}/bootstrap.min.css" rel="stylesheet">

    <link href="{{url(config('theme.front.css'))}}/owl.carousel.css" rel="stylesheet">
    <link href="{{url(config('theme.front.css'))}}/owl.theme.default.min.css" rel="stylesheet">

    <link href="{{url(config('theme.front.css'))}}/font-awesome.min.css" rel="stylesheet">
    <link href="{{url(config('theme.front.css'))}}/style.css" rel="stylesheet">
    <link href="{{url(config('theme.front.plugins'))}}/bootstrap-datepicker/css/bootstrap-datepicker.min.css"
          rel="stylesheet">
    <link href="{{url(config('theme.front.plugins'))}}/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">

    <link href="{{url(config('theme.front.plugins'))}}/loader/jquery.loader.css" rel="stylesheet">
    <link href="{{url(config('theme.admin.css'))}}/custom.css" rel="stylesheet" type="text/css"/>
    {{--<link href="{{url(config('theme.common.css'))}}/lightbox.min.css" rel="stylesheet" type="text/css" />--}}
    <link href="{{url(config('theme.admin.css'))}}/icons.css" rel="stylesheet" type="text/css"/>
    @yield('extra-styles')
    <link href="{{url(config('theme.admin.plugins'))}}/sweet-alert2/sweetalert2.css" rel="stylesheet">
    <style>
        .loader {
            border: 16px solid #f3f3f3; /* Light grey */
            border-top: 16px solid #3498db; /* Blue */
            border-radius: 50%;
            width: 120px;
            height: 120px;
            animation: spin 2s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>