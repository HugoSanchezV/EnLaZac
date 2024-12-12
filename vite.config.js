import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';


// import { readFileSync } from 'node:fs';

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
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
    //    server: {
    //         host: 'localhost', // Usar localhost
    //         port: 5173, // Puerto configurado para Vite
    //         https: {
    //             key: readFileSync('./key.pem'), // Ruta al archivo de clave privada
    //             cert: readFileSync('./cert.pem'), // Ruta al archivo del certificado
    //         },
    //         proxy: {
    //             '/': 'http://127.0.0.1:8000', // Backend Laravel
    //         },
    //     },
});
