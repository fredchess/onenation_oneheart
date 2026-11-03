import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

// Assets of the redesigned public site (served on the PREVIEW_HOSTS hosts).
// The legacy theme keeps using Laravel Mix (webpack.mix.js).
export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/ndop/css/app.css', 'resources/ndop/js/app.js'],
            refresh: ['resources/views/ndop/**'],
        }),
        tailwindcss(),
    ],
});
