import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import AppLayout from '../Layouts/AppLayout.vue';
import AuthLayout from '../Layouts/AuthLayout.vue';

const pages = import.meta.glob('../Pages/**/*.vue', { eager: true });

createInertiaApp({
    resolve: (name) => {
        const page = pages[`../Pages/${name}.vue`];

        if (! page) {
            throw new Error(`Inertia page not found: ${name}`);
        }

        page.default.layout = name.startsWith('Auth/') ? AuthLayout : AppLayout;

        return page;
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el);
    },
});
