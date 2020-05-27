@extends('client1.layouts.master')

@section('pageTitle')
    Terms & Conditions Frog Wallet Web & Mobile Application
@stop

@section('contentHeader')
    <style>
        .card-box {
            display: flex;
            align-items: center;
            height: 100%;
        }

        .card-box i {
            margin-right: 5px;
        }

        .scroll-box {
            display: flex;
            flex-direction: column;
            height: 69vh;
            justify-content: last-baseline;
        }

        .point-number {
            overflow-y: auto;
            flex: 1;
        }

        .point-action {
            flex: auto;
        }

    </style>

    <style>
        .terms-page {
            display: flex;
            flex-direction: column;
            height: 100vh;
        }

        .terms-page .navbar {
            flex: 0 0 auto;
        }

        .terms-page .footer {
            flex: 0 0 auto;
        }

        .terms-page #login {
            flex: 1 1 auto;
            overflow: hidden;
            margin: 40px 0;
            padding: 0;
            display: flex;
            flex-direction: column;
        }

        .terms-page > #login > .container {
            height: 100%;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .terms-page .footer {
            position: static;
            margin: 0;
        }

        .terms-page .point-number {
            flex: 1 1 auto;
            overflow: auto;
            border: 1px solid #dadada;
        }

        #loan_signature {
            border: 1px solid #dadada;
            margin: 0 auto;
        }
    </style>
@stop

@section('body_class')
    terms-page
@stop

@section('content')
    <section class="section" id="login">
        <div class="container">
            <div class="scroll-box">
                <h3>Terms And Conditions</h3>
                <div class="point-number">
                    {!! $cms !!}
                </div>
                @if(auth()->user()->terms_accepted!=1)
                    {{--  <div style="margin: 0 auto; width: 70%;">
                          <h5 class="mt-2">Signature</h5>
                      </div>
                      <canvas id="loan_signature" width="600" height="200"
                              style="width: 600px; height: 200px;"></canvas>--}}

                    <div class="text-right mt-1 point-action">
                        {{--<button class="btn btn-danger clearSignature">Clear</button>--}}
                        <button class="btn btn-inverse disabled ml-2 acceptTerms" disabled>Accept</button>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection

@section('contentFooter')
    <script>
        $(document).on('click', '.acceptTerms', function (e) {
            e.preventDefault();
            // if (signaturePad.isEmpty()) {
            //     return alert("Please provide a signature first.");
            // } else {
            $.ajax({
                dataType: 'json',
                method: 'post',
                url: ajaxURL + 'accept-terms',
                // data: {
                //     signature: signaturePad.toDataURL('image/png')
                // },
                success: function (data) {
                    window.location = data['url'];
                }
            });
            // }
        });
        // var canvas = document.getElementById('loan_signature');
        // var signaturePad = new SignaturePad(canvas, {
        //     backgroundColor: "rgb(255,255,255)"
        // });
        // $(document).on('click', '.clearSignature', function (e) {
        //     e.preventDefault();
        //     signaturePad.clear();
        // });

        $('.point-number').scroll(function () {

            var scrollHeight = $('.point-number')[0].scrollHeight;
            var scrolled = $('.point-number')[0].scrollTop + $('.point-number').height() + 10;
            if (scrolled >= scrollHeight) {
                $('.acceptTerms').addClass('btn-primary');
                $('.acceptTerms').removeClass('btn-inverse disabled');
                $('.acceptTerms').prop('disabled', false);
            } else {
                $('.acceptTerms').removeClass('btn-primary');
                $('.acceptTerms').addClass('btn-inverse disabled');
                $('.acceptTerms').prop('disabled', true);
            }
        });
    </script>
@endsection
