<template>
    <div class="container-fluid" v-if="config.loaded">
        <div class="row">
            <div class="col-md-8">
                <h1>{{ config.title }}</h1>
            </div>

            <div class="col-md-4 text-right">
                <button @click="filterType = 'all'" :class="'btn nav-button'+selectedFilterButtonClass('all')">
                    all ({{ allCount }})
                </button>

                <button @click="filterType = 'failing'" :class="'btn nav-button'+selectedFilterButtonClass('failing')">
                    failing ({{ failingCount }})
                </button>

                <button @click="filterType = 'healthy'" :class="'btn nav-button'+selectedFilterButtonClass('healthy')">
                    healthy ({{ healthyCount }})
                </button>

                <button @click="refreshAll()" class="btn btn-primary nav-button">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         viewBox="0 0 20 20"
                         width="20px"
                         fill="white"
                         :class="isLoading ? 'spin-svg' : ''"
                    >
                        <path d="M10 3v2a5 5 0 0 0-3.54 8.54l-1.41 1.41A7 7 0 0 1 10 3zm4.95 2.05A7 7 0 0 1 10 17v-2a5 5 0 0 0 3.54-8.54l1.41-1.41zM10 20l-4-4 4-4v8zm0-12V0l4 4-4 4z"/>
                    </svg>

                    refresh all
                </button>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                    <span class="row">
                        <template v-for="resource in resources">
                            <resource-target
                                v-for="target in filter(resource.targets)"
                                :key="target.id"
                                :target="target"
                                :resource="resource"
                                :config="config"
                                v-on:check-resource="checkResource(resource)"
                                v-on:show-result="showResult(resource, target)"
                            >
                            </resource-target>
                        </template>
                    </span>
            </div>
        </div>
    </div>
</template>

<script>
Vue.component('resource-target', require('./Target.vue'))

export default {
    props: ['config'],

    data() {
        return {
            resources: {},
            filterType: 'all',
        }
    },

    methods: {
        loadAllResources() {
            let $this = this

            axios.get('/health/resources').then(function(response) {
                $this.resources = response.data

                $this.refreshAll()
            })
        },

        refreshAll() {
            _.map(this.resources, this.checkResource)
        },

        filter(targets) {
            let $this = this

            return _.filter(targets, function(target) {
                return (
                    !target.result ||
                    $this.filterType == 'all' ||
                    ($this.filterType == 'healthy' && target.result.healthy) ||
                    ($this.filterType == 'failing' && !target.result.healthy)
                )
            })
        },

        checkResource(resource) {
            if (!resource || resource.loading) {
                return
            }

            this.$set(resource, 'loading', true)

            axios
                .get('/health/resources/' + resource.slug + '?flush=1')
                .then(function(response) {
                    resource.targets = response.data.targets

                    resource.loading = false
                })
        },

        selectedFilterButtonClass(button) {
            if (this.filterType == button) {
                return ' btn-primary'
            }

            return ' btn-warning'
        },

        getAllTargets(type) {
            let targets = []

            _.each(this.resources, function(resource) {
                _.each(resource.targets, function(target) {
                    if (
                        type == 'all' ||
                        (type == 'failing' &&
                            target.result &&
                            !target.result.healthy) ||
                        (type == 'healthy' &&
                            target.result &&
                            target.result.healthy)
                    ) {
                        targets.push(target)
                    }
                })
            })

            return targets
        },
    },

    computed: {
        allCount() {
            return this.getAllTargets('all').length
        },

        failingCount() {
            return this.getAllTargets('failing').length
        },

        healthyCount() {
            return this.getAllTargets('healthy').length
        },

        isLoading() {
            return _.reduce(
                this.resources,
                function(current, resource) {
                    return current || resource.loading
                },
                false,
            )
        },
    },

    mounted() {
        this.loadAllResources()
    },
}
</script>

<style>
@keyframes spin {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(359deg);
    }
}
.spin-svg {
    animation: spin 2s linear infinite;
}
</style>
