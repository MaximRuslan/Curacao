@extends('client1.layouts.master')

@section('pageTitle')
    Terms & Conditions Frog Wallet Web & Mobile Application
@stop

@section('contentHeader')

@stop
{{--@section('extra-styles')
    <style>
        .loader {
            border: 16px solid #f3f3f3; /* Light grey */
            border-top: 16px solid #3498db; /* Blue */
            border-radius: 50%;
            width: 120px;
            height: 120px;
            animation: spin 2s linear infinite;
        }

        .cus--label {
            font-size: 17px;
            font-weight: 500;
        }

        .point-number ol {
            list-style-type: none;
            counter-reset: item;
            margin: 0;
            padding: 0;
        }

        .point-number ol > li {
            display: table;
            counter-increment: item;
            margin-bottom: 0.6em;
        }

        .point-number ol > li:before {
            content: counters(item, ".") ". ";
            display: table-cell;
            padding-right: 0.6em;
            font-size: 17px;
            font-weight: 500;
        }

        .point-number ol > li > li {
            margin: 0;
        }

        .point-number ol > li > li:before {
            content: counters(item, ".") " ";
        }

        ul.a {
            list-style-type: lower-alpha;
        }

        ul.no {
            list-style-type: decimal;
        }

        ul.i {
            list-style-type: lower-roman;
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
@endsection--}}
@section('content')
    <div class="froggy-listing">
        <div class="froggy-listing__header"
             style="background-image: url({!! asset('resources/img/client/froggy-cover.jpg') !!});">
            <div class="container">
                <h3>Terms & Conditions Frog Wallet Web & Mobile Application</h3>
            </div>
        </div>
        <div class="froggy-listing__content">
            <div class="container">
                {!! $cms->value !!}
            </div>
        </div>
    </div>
@endsection
