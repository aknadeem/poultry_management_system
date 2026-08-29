import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import AppLayout from '../Layouts/AppLayout.vue';
import AuthLayout from '../Layouts/AuthLayout.vue';

const pages = import.meta.glob('../Pages/**/*.vue');

createInertiaApp({
    resolve: async (name) => {
        const page = await resolvePageComponent(
            `../Pages/${name}.vue`,
            pages,
        );

        page.default.layout = name.startsWith('Auth/') ? AuthLayout : AppLayout;

        return page;
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el);
    },
});
