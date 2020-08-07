require('./bootstrap');

import { InertiaApp } from '@inertiajs/inertia-vue';
import VueMeta from 'vue-meta';
window.Vue = require('vue');

Vue.use(InertiaApp);
Vue.use(VueMeta, {
    refreshOnceOnNavigation: true,
    keyName: 'meta',
});

Vue.prototype.$route = (...args) => route(...args).url();

// load components
const files = require.context('./components', true, /\.vue$/i)
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

// load vue and inertiajs
const app = document.getElementById('app');
new Vue({
    render: h => h(InertiaApp, {
        props: {
            initialPage: JSON.parse(app.dataset.page),
            resolveComponent: name => require(`./Views/${name}`).default,
        },
    }),
}).$mount(app);
