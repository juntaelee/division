import ucc from './components/UCC.vue'

const routes = [
    { path: '/', redirect: '/ucc' },
    { path: '/ucc', component: ucc },
    { path: '/bar', component: {template: '<div>bar</div>'} }
]

export default routes