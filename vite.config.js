import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    server: {
        host: '127.0.0.1',
    },
    plugins: [
        tailwindcss(),
        laravel({
            input: ['resources/tailwind/app.css', 'resources/js/app.js'],
            refresh: true,
        })
    ],
});
