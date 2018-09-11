require('./bootstrap')

window.Vue = require('vue')

Vue.component('resource-target', require('./components/Target.vue'))

const app = new Vue({
    el: '#app',

    data: {
        resources: {},

        config: {},
    },

    methods: {
        loadAllResources() {
            me = this

            axios.get('/health/resources').then(function(response) {
                me.resources = response.data

                me.checkAllResources()
            })
        },

        checkAllResources() {
            _.map(this.resources, this.checkResource)
        },

        checkResource(resource) {
            if (!resource || resource.loading) {
                return
            }

            this.$set(resource, 'loading', true)

            axios
                .get('/health/resources/' + resource.slug)
                .then(function(response) {
                    resource.targets = response.data.targets

                    resource.loading = false
                })
        },

        showResult(resource, target) {
            const message = !target.result.healthy ? target.result.errorMessage : this.config.alert.success.message

            const type = !target.result.healthy ? this.config.alert.error.type : this.config.alert.success.type

            swal(resource.name, message, type)
        },

        loadConfig() {
            me = this

            return axios
                .get('/health/config')
                .then(function(response) {
                    me.config = response.data
                })
        },
    },

    mounted() {
        me = this

        me.loadConfig().then(function() {
            me.loadAllResources()
        })
    },
})
