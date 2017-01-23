@include('pragmarx/health::material.defines')
@extends('pragmarx/health::html')

@section('head')
    <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png" />
    <link rel="icon" type="image/png" href="../assets/img/favicon.png" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
    <meta name="viewport" content="width=device-width" />

    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

    <link href="//cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css" rel="stylesheet">

    <link href="//maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" rel="stylesheet">

    <link href='//fonts.googleapis.com/css?family=Roboto:400,700,300|Material+Icons' rel='stylesheet' type='text/css'>

    @include('pragmarx/health::'.TEMPLATE.'.partials.css.material')

    @include('pragmarx/health::'.TEMPLATE.'.partials.css.health')
@stop

@section('body')
    @yield('html.header')

    @yield('html.body')

    @yield('html.footer')

    <!--   jQuery   -->
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.1/jquery.min.js" type="text/javascript"></script>

    <!--   Bootstrap   -->
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" type="text/javascript"></script>

    <!--   Material   -->
    @include('pragmarx/health::'.TEMPLATE.'.partials.js.material')

    <!--  Charts Plugin -->
    <script src="//cdnjs.cloudflare.com/ajax/libs/chartist/0.10.1/chartist.min.js"></script>

    <!--  Notifications Plugin    -->
    <script src="//cdnjs.cloudflare.com/ajax/libs/mouse0270-bootstrap-notify/3.1.7/bootstrap-notify.min.js"></script>

    <!--   Sweet Alert   -->
    <script src="//cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>

    @include('pragmarx/health::'.TEMPLATE.'.partials.js.material-dashboard')
    @include('pragmarx/health::'.TEMPLATE.'.partials.js.health')

    <script type="text/javascript">
        $(document).ready(function(){

            // Javascript method's body can be found in assets/js/demos.js
//            demo.initDashboardPageCharts();

        });
    </script>

    @yield('scripts')
@stop
