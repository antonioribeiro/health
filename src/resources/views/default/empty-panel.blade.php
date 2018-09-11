@extends('pragmarx/health::default.html')

@section('html.body')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>{{ config('health.title') }}</h1>
            </div>

            <div class="col-md-12">
                <p style="font-size: 3em;">
                    You list of resources is empty or something wrong happened, please check your logs.
                </p>
            </div>
        </div>
    </div>
@stop
