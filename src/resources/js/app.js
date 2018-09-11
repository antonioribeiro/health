require('./bootstrap')

window.Vue = require('vue')

Vue.component('resource-target', require('./components/Target.vue'))

const app = new Vue({
    el: '#app',

    data: {
        resources: {},
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
            console.log('check all')
            _.map(this.resources, this.checkResource)
        },

        checkResource(resource) {
            if (resource.loading) {
                console.log('already loading')
                return
            }

            console.log('START loading')

            this.$set(resource, 'loading', true)

            axios
                .get('/health/resources/' + resource.slug)
                .then(function(response) {
                    resource.targets = response.data.targets

                    resource.loading = false

                    console.log('END loading')
                })
        },
    },

    mounted() {
        this.loadAllResources()
    },
})
