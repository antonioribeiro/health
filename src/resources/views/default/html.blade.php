@extends('pragmarx/health::html')

@section('head')
    <link rel="stylesheet" href="/health/assets/css/app.css">
@stop

@section('body')
    @include('pragmarx/health::default.partials.style')

    @yield('html.header')

    @yield('html.body')

    @yield('html.footer')

    <script src="/health/assets/js/app.js"></script>

    @yield('scripts')

    @if (config('app.env') == 'local')
        <script src="http://localhost:35729/livereload.js"></script>
    @endif
@stop
