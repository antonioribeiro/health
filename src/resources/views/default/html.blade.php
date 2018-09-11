@extends('pragmarx/health::html')

@section('head')
    <link rel="stylesheet" href="/health/assets/css/app.css">

    <script src="/health/assets/js/app.js"></script>
@stop

@section('body')
    @include('pragmarx/health::default.partials.style')

    @yield('html.header')

    @yield('html.body')

    @yield('html.footer')

    @yield('scripts')
@stop
