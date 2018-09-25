@extends('pragmarx/health::default.html')

@section('html.body')
    <div id="app">
        <health-panel
            :config="config"

        >
        </health-panel>
    </div>
@stop
