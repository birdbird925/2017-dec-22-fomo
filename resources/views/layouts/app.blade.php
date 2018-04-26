<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- social media meta tag --}}
    <meta property="og:title" content="FOMO - a full customization experience for your watch">
    <meta property="og:description" content="A full customization experience for your watch">
    <meta property="og:image" content="https://fomo.watch/images/fomo-hero-screenshot.jpg">
    <meta property="og:image:type" content="image/jpg">
    <meta property="og:url" content="https://fomo.watch/">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- <title>{{ config('app.name', 'Laravel') }}</title> --}}
    <title>FOMO - a full customization experience for your watch</title>

    <!-- Styles -->
    <link href="/css/app.css" rel="stylesheet">

    <!-- Scripts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css">
    @stack('head-scripts')
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>
    <!-- Facebook Pixel Code -->
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window,document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '377930176006641');
        fbq('track', 'PageView');
        @yield('facebook.pixel.event')
    </script>
    <noscript>
        <img height="1" width="1"
        src="https://www.facebook.com/tr?id=377930176006641&ev=PageView
        &noscript=1"/>
    </noscript>
    <!-- End Facebook Pixel Code -->

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-114684249-1"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'UA-114684249-1');
    </script>
</head>
<body class="home-wrap @yield('body-class')">
    @include('layouts.partials.header')
    @if(!Auth::check())
        @include('auth.popup')
    @endif
    @include('layouts.partials.popup')
    @include('layouts.partials.navigation')
    <div class="wrapper">
        @yield('content')
    </div>
    @include('layouts.partials.footer')

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>
    <script src="/js/instafeed.min.js"></script>
    <script src="/js/lightslider.min.js"></script>
    <script src="/js/konva.js"></script>
    <script src="/js/admin/sweetalert.min.js"></script>
    <script src="/js/main.js"></script>
    @stack('scripts')
</body>
</html>
