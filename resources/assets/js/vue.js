import Vue from 'vue'
import VueRouter from 'vue-router'
import routes from './app'

Vue.use(VueRouter)

const router = new VueRouter({
    routes
})

const app = new Vue({
    router
}).$mount('#app')