<template>
    <div :class="'col-sm-12 col-md-6 col-lg-4 col-xl-'+resource.columnSize">
        <div :class="'btn btn-block target-card shadow '+colorClassBackground+' '+resource.style.buttonLines"
             :title="resource.name"
             :data-name="resource.name"
        >
            <div v-if="resource.style.buttonLines === 'multi'">
                <div class="row">
                    <div class="col-6">
                        <p class="title text-left">
                            {{ resource.name }}
                        </p>

                        <p class="subtitle text-left">
                            <span v-if="target.name !== 'default'">
                                {{ target.display }}
                            </span>

                            <span v-else>&nbsp;</span>
                        </p>
                    </div>

                    <div class="col-6">
                        <div class="row d-flex">
                            <div class="col-12 text-right">
                                <span @click="showResult()" :class="colorClass">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="info-icon" viewBox="0 0 20 20"><path d="M10 20a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm2-13c0 .28-.21.8-.42 1L10 9.58c-.57.58-1 1.6-1 2.42v1h2v-1c0-.29.21-.8.42-1L13 9.42c.57-.58 1-1.6 1-2.42a4 4 0 1 0-8 0h2a2 2 0 1 1 4 0zm-3 8v2h2v-2H9z"/></svg>
                                </span>

                                <span @click="$emit('check-resource')" :class="colorClass">
                                    <span v-if="!resource.loading && target.result && target.result.healthy">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="info-icon" viewBox="0 0 20 20"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM6.7 9.29L9 11.6l4.3-4.3 1.4 1.42L9 14.4l-3.7-3.7 1.4-1.42z"/></svg>
                                    </span>

                                    <span v-if="!resource.loading && target.result && !target.result.healthy">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="info-icon" viewBox="0 0 20 20"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm1.41-1.41A8 8 0 1 0 15.66 4.34 8 8 0 0 0 4.34 15.66zm9.9-8.49L11.41 10l2.83 2.83-1.41 1.41L10 11.41l-2.83 2.83-1.41-1.41L8.59 10 5.76 7.17l1.41-1.41L10 8.59l2.83-2.83 1.41 1.41z"/></svg>
                                    </span>

                                    <span v-if="!target.result || resource.loading">
                                        <svg class="info-icon" viewBox="0 0 38 38" xmlns="http://www.w3.org/2000/svg" stroke="gray"><g fill="none" fill-rule="evenodd"><g transform="translate(1 1)" stroke-width="2"><circle stroke-opacity=".5" cx="18" cy="18" r="18"/><path d="M36 18c0-9.94-8.06-18-18-18"><animateTransform attributeName="transform" type="rotate" from="0 18 18" to="360 18 18" dur="1s" repeatCount="indefinite"/> </path></g></g></svg>
                                    </span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 chart">
                        <target-chart
                            :height="config.database.graphs.height"
                            :chart-data="chartData"
                            :labels="graphLabels"
                            :data="graphData"
                            :backgrounds="graphBackgrounds"
                            v-on:bar-clicked="barClicked"
                        >
                        </target-chart>
                    </div>
                </div>
            </div>

            <h3 v-else>
                <i :class="'fa fa-'+(resource.style.opacity.healthy ? 'check-circle' : 'times-circle')"></i>
                {{ resource.name }}
            </h3>
        </div>
    </div>
</template>

<script>
Vue.component('target-chart', require('./Chart.vue'))

export default {
    props: ['resource', 'target', 'config'],

    computed: {
        colorClass() {
            return !this.target.result
                ? 'color-neutral'
                : this.target.result.healthy
                    ? 'color-success'
                    : 'color-danger'
        },

        colorClassBackground() {
            return this.colorClass + '-background'
        },

        graphLabels() {
            let labels = []

            _.forEach(this.target.checks, function(check) {
                labels.push(
                    check.value_human
                        ? check.value_human
                        : check.target_display,
                )
            })

            return labels
        },

        graphData() {
            let data = []

            _.forEach(this.target.checks, function(check) {
                data.push(check.value ? check.value : check.runtime)
            })

            return data
        },

        graphBackgrounds() {
            let colors = []

            _.forEach(this.target.checks, function(check) {
                colors.push(check.healthy ? '#8cca82' : '#FF7C74')
            })

            return colors
        },

        chartData() {
            if (!this.graphsAreEnabled()) {
                return this.emptyGraphData()
            }

            return this.generateGraphData()
        },
    },

    methods: {
        barClicked(activeElement) {
            const check = this.target.checks[activeElement[0]._index]

            this.showResultAlert(
                check.resource_name,
                check.error_message,
                check.healthy,
            )
        },

        generateGraphData() {
            return {
                labels: this.graphLabels,
                datasets: [
                    {
                        backgroundColor: this.graphBackgrounds,
                        data: this.graphData,
                    },
                ],
            }
        },

        graphsAreEnabled() {
            return (
                this.config.database.enabled &&
                (this.config.database.graphs.enabled ||
                    this.config.database.graphs.enabled !==
                        this.resource.graphEnabled)
            )
        },

        emptyGraphData() {
            return {
                labels: [],
                datasets: [
                    {
                        backgroundColor: [],
                        data: [],
                    },
                ],
            }
        },

        showResult() {
            this.showResultAlert(
                this.resource.name,
                this.target.result.errorMessage,
                this.target.result.healthy,
            )
        },

        showResultAlert(name, message, healthy) {
            message = !healthy ? message : this.config.alert.success.message

            const type = !healthy
                ? this.config.alert.error.type
                : this.config.alert.success.type

            swal(name, message, type)
        },
    },
}
</script>
