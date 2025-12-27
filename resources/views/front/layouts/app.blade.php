<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <title>@yield('title', "Accueil ") - One Nation One Heart</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <script src="{{ asset('lovecare/js/jquery.js') }}"></script>

    <link rel="shortcut icon" href="{{asset("lovecare/images/logo.png")}}" type="image/x-icon">

    <meta property="og:type" content="Association" />
    <meta property="og:title" content="@yield("meta_title", "Smile Together - One Nation One Heart")" />
    <meta property="og:description" content="@yield("meta_description", "Faites un don à des orphélins avec One Nation One Heart")" />
    <meta property="og:url" content="@yield("meta_url", "https://onoh.org/")" />
    <meta property="og:image" content="@yield("meta_image", asset("lovecare/images/logo.png"))" />
    <meta property="og:site_name" content="@yield("meta_site_name", "Faites un don ou devenez bénévole chez One Nation One Heart")" />


    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Patrick+Hand&family=Poppins:wght@200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css" integrity="sha512-1sCRPdkRXhBV2PBLUdRb4tMg1w2YPf37qatUFeS7zlBy7jJI8Lf4VHwWfZZfpXtYSLy85pkm9GaYVYMfw5BC1A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/4.5.6/css/ionicons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css">

    <link rel="stylesheet" href="{{ asset('lovecare/css/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('lovecare/css/flaticon.css') }}">
    <link rel="stylesheet" href="{{ asset('lovecare/css/tiny-slider') }}.css">
    <link rel="stylesheet" href="{{ asset('lovecare/css/glightbox.min') }}.css">
    <link rel="stylesheet" href="{{ asset('lovecare/css/aos.css') }}">
    <link rel="stylesheet" href="{{ asset('lovecare/css/style.css') }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>

    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="/resources/demos/style.css">
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-SZLSCY3SQV"></script>
        <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-SZLSCY3SQV');
        </script>
    @yield('css')
</head>

<body>

    @include('front.layouts.header')

    @yield('content')

    @include('front.layouts.footer')


    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script src="{{ asset('lovecare/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('lovecare/js/tiny-slider.js') }}"></script>
    <script src="{{ asset('lovecare/js/glightbox.min.js') }}"></script>
    <script src="{{ asset('lovecare/js/aos.js') }}"></script>
    <script src="{{ asset('lovecare/js/rellax.min.js') }}"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBVWaKrjvy3MaE7SQ74_uJiULgl1JY0H2s&sensor=false"></script>
    <script src="{{ asset('lovecare/js/google-map.js') }}"></script>
    <script src="{{ asset('lovecare/js/main.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/js/bootstrap-select.min.js" integrity="sha512-yDlE7vpGDP7o2eftkCiPZ+yuUyEcaBwoJoIhdXv71KZWugFqEphIS3PU60lEkFaz8RxaVsMpSvQxMBaKVwA5xg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script type="text/javascript" src="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>

    @yield('script')
    <script>
        $(document).ready(function() {
            $('.selectpicker').selectpicker();
        });
    </script>

</body>

<script>
   // var phoneInputField = document.querySelector("#phone");
   // window.phoneInput = window.intlTelInput(phoneInputField, {
   //     utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
   //     onlyCountries: ["CM"],
   // });
 </script>
<script src="{{ asset('lovecare/js/donationsform.js') }}"></script>

</html>
