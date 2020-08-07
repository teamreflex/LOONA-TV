require('./bootstrap');

import { InertiaApp } from '@inertiajs/inertia-vue';
import VueMeta from 'vue-meta';
import Clipboard from 'v-clipboard';
import { library } from '@fortawesome/fontawesome-svg-core';
import { faTwitter, faGithub } from '@fortawesome/free-brands-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

window.Vue = require('vue');

Vue.use(InertiaApp);
Vue.use(VueMeta, {
    refreshOnceOnNavigation: true,
    keyName: 'meta',
});
Vue.use(Clipboard);

// fontawesome icons
library.add(faTwitter, faGithub);
Vue.component('fa-icon', FontAwesomeIcon);

// setup ziggy
Vue.prototype.$route = (...args) => route(...args).url();

// load components
// const files = require.context('./components', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

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
