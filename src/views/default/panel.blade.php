@extends('pragmarx/health::default.html')

@section('html.body')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>{{ config('health.title') }}</h1>
            </div>

            <div class="col-md-12">
                <div class="row">
                    @foreach($health as $item)
                        @include(
                            config('health.views.partials.well'),
                            [
                                'itemName' => $item['name'],
                                'itemHealth' => $item['health']['healthy'],
                                'itemMessage' => $item['health']['message'],
                                'columnSize' => $item['columnSize'] ?: $item['column_size']
                            ]
                        )
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script>
        $('.btn').bind('click', function () {
            var resource = $(this).data('name');

            var message = $(this).prop('title') ? $(this).prop('title') : '{{ config('health.alert.success.message') }}';

            var type = $(this).prop('title') ? '{{ config('health.alert.error.type') }}' : '{{ config('health.alert.success.type') }}';

            sweetAlert(resource, message, type);
        });
    </script>
@stop
