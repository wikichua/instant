require('./bootstrap');

// Import modules...
import { createApp, h } from 'vue';
import { App as InertiaApp, plugin as InertiaPlugin } from '@inertiajs/inertia-vue3';
import { InertiaProgress } from '@inertiajs/progress';
import { createPopper } from "@popperjs/core";
import '@fortawesome/fontawesome-free/js/all.min';
import mitt from 'mitt';

const el = document.getElementById('app');

const app = createApp({
    render: () =>
    h(InertiaApp, {
        initialPage: JSON.parse(el.dataset.page),
        resolveComponent: (name) => require(`../vues/Pages/${name}`).default,
    }),
})
.mixin({ methods: { route } })
.use(InertiaPlugin);

let req = require.context('../vues/Components/', true, /\.(js|vue)$/i);
req.keys().map(key => {
    let name = key.match(/\w+/)[0];
    return app.component('instant' + name, req(key).default);
});

const emitter = mitt();
app.config.globalProperties.emitter = emitter;

app.mount(el);

InertiaProgress.init({ color: '#4B5563' });
