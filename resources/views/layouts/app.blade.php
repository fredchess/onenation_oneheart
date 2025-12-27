
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>One Nation One Heart - @yield("title", "Connexion") </title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{asset("admin_assets/css/bootstrap.css")}}">
    <link rel="stylesheet" href="{{asset("admin_assets/vendors/bootstrap-icons/bootstrap-icons.css")}}">
    <link rel="stylesheet" href="{{asset("admin_assets/css/app.css")}}">
    <link rel="stylesheet" href="{{asset("admin_assets/css/pages/auth.css")}}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css" integrity="sha512-1sCRPdkRXhBV2PBLUdRb4tMg1w2YPf37qatUFeS7zlBy7jJI8Lf4VHwWfZZfpXtYSLy85pkm9GaYVYMfw5BC1A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-SZLSCY3SQV"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-SZLSCY3SQV');
    </script>
</head>

<body>
    <div id="auth">

        <div class="row h-100">
            @yield('content')
        </div>

    </div>
</body>

</html>
