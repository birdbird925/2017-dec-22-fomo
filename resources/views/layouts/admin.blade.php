<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<link rel="icon" type="image/png" href="assets/img/favicon.ico">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>FOMO Admin Panel</title>
	<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
	<meta name="viewport" content="width=device-width" />

	<!-- Bootstrap core CSS     -->
	<link href="/css/bootstrap.min.css" rel="stylesheet" />
	<link href="/css/animate.min.css" rel="stylesheet"/>
	<link href="/css/light-bootstrap-dashboard.css" rel="stylesheet"/>
	<link href="/css/sweetalert.css" rel="stylesheet">
	<link rel="stylesheet" href="/css/material.min.css">
	<link rel="stylesheet" href="/css/dataTables.material.min.css">
	<link rel="stylesheet" href="/css/lightslider.min.css">
	<link rel="stylesheet" href="/css/datepicker.min.css" />

	<!--     Fonts and icons     -->
	<link href="http://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
	<link href='http://fonts.googleapis.com/css?family=Roboto:400,700,300' rel='stylesheet' type='text/css'>
	<link href="/css/pe-icon-7-stroke.css" rel="stylesheet" />
	<link href="/css/app.css" rel="stylesheet">
</head>
<body>
<div class="wrapper" id="admin-wrapper">
  	<div class="sidebar" data-color="" data-image="/images/sidebar-5.jpg">
	{{-- <div class="sidebar" data-color="azure" data-image="/images/sidebar-5.jpg"> --}}
  		<div class="sidebar-wrapper">
      		<div class="logo">
        		<a href="/admin" class="simple-text">
					<img src="/images/logo-white.svg" alt="">
					<span>FOMO</span>
        		</a>
      		</div>

            <ul class="nav">
                <li class="@yield('dashboard-sidebar')">
                    <a href="/admin">
                        <i class="pe-7s-graph"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
								<li class="@yield('customize-sidebar')">
                    <a href="/admin/customize/product">
                        <i class="pe-7s-wristwatch"></i>
                        <p>Watch</p>
                    </a>
                </li>
				<li class="@yield('order-sidebar')">
					<a href="/admin/order">
						<i class="pe-7s-note2"></i>
						<p>
							@if($newOrder)
								<div class="new-notification">
									{{$newOrder}}
								</div>
							@endif
							Order
						</p>
					</a>
				</li>
				<li class="@yield('customer-sidebar')">
					<a href="/admin/customer">
						<i class="pe-7s-users"></i>
						<p>Customer</p>
					</a>
				</li>
				<li class="@yield('voucher-sidebar')">
					<a href="/admin/voucher">
						<i class="pe-7s-ticket"></i>
						<p>Voucher</p>
					</a>
				</li>
				<li class="@yield('cms-sidebar')">
					<a href="/admin/cms">
						<i class="pe-7s-box2"></i>
						<P>CMS</P>
					</a>
				</li>
				<li class="@yield('message-sidebar')">
					<a href="/admin/message">
						<i class="pe-7s-mail"></i>
						<p>
							@if($newMessage)
								<div class="new-notification">
									{{$newMessage}}
								</div>
							@endif
							Message
						</p>
					</a>
				</li>
            </ul>
    	</div>
    </div>

    <div class="main-panel">
        <nav class="navbar navbar-default navbar-fixed">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

					<ul class="page-direction">
						@yield('page-direction')
					<ul>
                </div>
                <div class="collapse navbar-collapse">
                    <ul class="nav navbar-nav navbar-right">
						<li>
                           <a href="/">
                               Tour
                            </a>
                        </li>
                        <li>
                           <a href="/account">
                               Account
                            </a>
                        </li>
						<li>
							<a href="/logout">Logout</a>
						</li>
                    </ul>
                </div>
            </div>
        </nav>


        <div class="content">
            <div class="container-fluid">
                <div class="row">
				    @yield('content')
                </div>
            </div>
        </div>

    </div>
</div>


</body>

  <!--   Core JS Files   -->
  <script src="/js/jquery-1.10.2.js" type="text/javascript"></script>
	<script src="/js/admin/bootstrap.min.js" type="text/javascript"></script>
	<script src="/js/admin/light-bootstrap-dashboard.js"></script>
	<script src="/js/admin/sweetalert.min.js"></script>
	<script src="/js/admin/jquery.dataTables.min.js"></script>
	<script src="/js/admin/datepicker.min.js"></script>
	<script src="/js/lightslider.min.js"></script>
	<script src="/js/konva.js"></script>
  @stack('scripts')
	<script src="/js/main.js"></script>
	</script>
</html>
