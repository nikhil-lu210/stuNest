import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
                'resources/js/property-wizard.js',
                'resources/js/admin-create-user.js',
                'resources/js/admin-select2.js',
            ],
            refresh: true,
        }),
    ],
});
