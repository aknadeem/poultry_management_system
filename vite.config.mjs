import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/inertia/app.js'],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    build: {
        rolldownOptions: {
            output: {
                // Vite 8 / Rolldown still accepts manualChunks (mapped to codeSplitting groups).
                manualChunks(id) {
                    if (
                        /node_modules[\\/](vue|@vue|@inertiajs)([\\/]|$)/.test(id)
                    ) {
                        return 'vendor';
                    }

                    return null;
                },
            },
        },
    },
});
