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
                                'columnSize' => $item['columnSize']
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
            var message = $(this).prop('title');

            if (! message) {
                sweetAlert(resource, '{{ config('health.alert.success.message') }}' , '{{ config('health.alert.success.type') }}');
            } else {
                sweetAlert(resource, message, '{{ config('health.alert.error.type') }}');
            }
        });
    </script>
@stop
