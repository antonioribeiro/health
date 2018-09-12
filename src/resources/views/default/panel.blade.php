@extends('pragmarx/health::default.html')

@section('html.body')
    <div id="app">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8">
                    <h1>{{ config('health.title') }}</h1>
                </div>

                <div class="col-md-4 text-right">
                    <button @click="checkAllResources()" class="btn btn-primary nav-button">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             viewBox="0 0 20 20"
                             width="20px"
                             fill="white"
                        >
                            <path d="M10 3v2a5 5 0 0 0-3.54 8.54l-1.41 1.41A7 7 0 0 1 10 3zm4.95 2.05A7 7 0 0 1 10 17v-2a5 5 0 0 0 3.54-8.54l1.41-1.41zM10 20l-4-4 4-4v8zm0-12V0l4 4-4 4z"/>
                        </svg>

                        refresh
                    </button>
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
                                v-on:check-resource="checkResource(resource)"
                                v-on:show-result="showResult(resource, target)"
                            >
                            </resource-target>
                        </template>
                    </span>
                </div>
            </div>
        </div>
    </div>
@stop
