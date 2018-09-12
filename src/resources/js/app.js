require('./bootstrap')

window.Vue = require('vue')

Vue.component('health-panel', require('./components/Panel.vue'))

const app = new Vue({
    el: '#app',

    data: {
        config: { loaded: false },
    },

    methods: {
        loadConfig() {
            let me = this

            return axios.get('/health/config').then(function(response) {
                response.data.loaded = true

                me.config = response.data

                $('.chart').css(
                    'height',
                    me.config.database.graphs.height + 'px',
                )
            })
        },
    },

    mounted() {
        this.loadConfig()
    },
})
