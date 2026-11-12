import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/js/app.js', 
                'resources/js/admin.js', 
                'resources/js/public.js'
            ],
            refresh: true,
            fonts: [
                bunny('Bricolage Grotesque', {
                    weights: [600, 700, 800],
                }),
                bunny('Plus Jakarta Sans', {
                    weights: [400, 500, 600, 700],
                }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        // host: '0.0.0.0', // Membuka akses untuk semua IP di jaringan lokal (true juga bisa, tapi 0.0.0.0 lebih eksplisit)
        // port: 5173,
        // hmr: {
        //     host: '192.168.1.6', // Ganti dengan IP lokal komputer Anda saat ini
        // },
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});