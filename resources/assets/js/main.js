import Vue from 'vue'
import VueRouter from 'vue-router'
import routes from './routes'

Vue.use(VueRouter)

import header from './components/Header.vue'
import nav from './components/Nav.vue'
import main from './components/Main.vue'

Vue.component('my-header', header)
Vue.component('my-nav', nav)
Vue.component('my-main', main)

const router = new VueRouter({
    routes
})

const app = new Vue({
    router
}).$mount('#app')