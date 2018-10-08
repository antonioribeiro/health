@extends('pragmarx/health::html')

@section('head')
    <script>
        window.laravel = @json($laravel)
    </script>

    <style>
        {!! file_get_contents(config('health.dist_path').'/css/app.css')  !!}
    </style>
@stop

@section('body')
    @include('pragmarx/health::default.partials.style')

    @yield('html.header')

    @yield('html.body')

    @yield('html.footer')

    <script>
        {!! file_get_contents(config('health.dist_path').'/js/app.js')  !!}
    </script>

    @if (config('app.env') == 'local')
        <script src="http://localhost:35729/livereload.js"></script>
    @endif
@stop
