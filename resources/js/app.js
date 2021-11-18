require('./bootstrap');

// Import modules...
import { createApp, h } from 'vue';
import { createInertiaApp, Head, Link} from '@inertiajs/inertia-vue3'
import { InertiaProgress } from '@inertiajs/progress';
import { createPopper } from "@popperjs/core";
import '@fortawesome/fontawesome-free/js/all.min';
import mitt from 'mitt';

createInertiaApp({
  resolve: name => require(`../vues/Pages/${name}`),
  setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .mixin({ methods: { route } })
            .component('InertiaHead', Head)
            .component('InertiaLink', Link)
            .use(plugin);
        let req = require.context('../vues/Components/', true, /\.(js|vue)$/i);
        req.keys().map(key => {
            let name = key.match(/\w+/)[0];
            app.component('instant' + name, req(key).default);
        });

        const emitter = mitt();
        app.config.globalProperties.emitter = emitter;
        return app.mount(el);
    },
});

InertiaProgress.init({ color: '#4B5563' });
