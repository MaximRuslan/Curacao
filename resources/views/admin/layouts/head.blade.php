<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="_token" content="{{ csrf_token() }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="_base_url" content="{{ url('/') }}">

    <link rel="shortcut icon" href="{!! asset('assets/images/favicon_1.ico') !!}">

    <title>@yield('page_name') {!! config('site.page title') !!}</title>

    <link rel="stylesheet" href="{{url(config('theme.admin.plugins'))}}/morris/morris.css">

    <link href="{{url(config('theme.admin.css'))}}/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="{{url(config('theme.admin.css'))}}/icons.css" rel="stylesheet" type="text/css"/>
    <link href="{{url(config('theme.admin.css'))}}/style.css" rel="stylesheet" type="text/css"/>
    <link href="{{url(config('theme.admin.css'))}}/custom.css" rel="stylesheet" type="text/css"/>
    <link href="{{url(config('theme.common.css'))}}/lightbox.min.css" rel="stylesheet" type="text/css"/>

    @yield('extra-styles')
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
    <script src="{{url(config('theme.admin.js'))}}/modernizr.min.js"></script>
</head>
