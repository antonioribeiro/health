@extends('pragmarx/health::default.html')

@section('html.body')
    <div id="app">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8">
                    <h1>{{ config('health.title') }}</h1>
                </div>

                <div class="col-md-4 text-right">
                    <button @click="checkAllResources()" class="btn btn-primary">refresh</button>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <span class="row">
                        <template v-for="resource in resources">
                            <resource-target
                                v-for="target in resource.targets" :key="target.id"
                                :target="target"
                                :resource="resource"
                                @check-resource="checkResource(resource)"
                            >
                            </resource-target>
                        </template>
                    </span>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    {{--<script>--}}
        {{--$('.btn').bind('click', function () {--}}
            {{--var resource = $(this).data('name');--}}

            {{--var message = $(this).prop('title') ? $(this).prop('title') : '{{ config('health.alert.success.message') }}';--}}

            {{--var type = $(this).prop('title') ? '{{ config('health.alert.error.type') }}' : '{{ config('health.alert.success.type') }}';--}}

            {{--swal(resource, message, type);--}}
        {{--});--}}
    {{--</script>--}}
@stop
