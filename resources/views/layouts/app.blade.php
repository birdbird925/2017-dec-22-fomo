<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="/css/app.css" rel="stylesheet">

    <!-- Scripts -->
    @stack('head-scripts')
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
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
