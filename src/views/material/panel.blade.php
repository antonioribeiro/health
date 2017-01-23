@extends('pragmarx/health::'.TEMPLATE.'.html')

@section('html.body')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="btn btn-block btn-info title">
                    <h2>
                        {{ config('health.title') }}
                    </h2>
                </div>
            </div>

            <div class="col-md-12">
                <div class="row">
                    @foreach($health as $item)
                        @include(
                            'pragmarx/health::'.TEMPLATE.'.partials.well',
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

            var message = $(this).prop('title') ? $(this).prop('title') : '{{ config('health.alert.success.message') }}';

            var type = $(this).prop('title') ? '{{ config('health.alert.error.type') }}' : '{{ config('health.alert.success.type') }}';

            sweetAlert(resource, message, type);
        });
    </script>
@stop
