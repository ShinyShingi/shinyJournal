import { defineConfig } from 'vite';
import laravel, { refreshPaths } from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import { createServer as createViteServer } from 'vite';
import https from 'https';
import fs from 'fs';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: [
                ...refreshPaths,
                'app/Livewire/**',
            ],
        }),
        vue({
            template: {
                transformAssetUrls: {
                    // The Vue plugin will re-write asset URLs, when referenced
                    // in Single File Components, to point to the Laravel web
                    // server. Setting this to `null` allows the Laravel plugin
                    // to instead re-write asset URLs to point to the Vite
                    // server instead.
                    base: null,

                    // The Vue plugin will parse absolute URLs and treat them
                    // as absolute paths to files on disk. Setting this to
                    // `false` will leave absolute URLs un-touched so they can
                    // reference assets in the public directory as expected.
                    includeAbsolute: false,
                },
            },
        }),
    ],

    css: {
        devSourcemap: true,
        loaderOptions: {
            sass: {
                additionalData: `@import "../../../resources/css/main.scss";`, // Import your custom SCSS file
            },
        }
    },

    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
        }
    },

    server: {
        host: '0.0.0.0',
        port: process.env.DEV_PORT || 4469,
        hmr: { host: "localhost", clientPort: process.env.DEV_PORT || 4469 },
        strictPort: true,
        // https: {
        //     https: {
        //         key: fs.readFileSync('ssl/localhost-key.pem'),
        //         cert: fs.readFileSync('ssl/localhost-cert.pem'),
        //     },
        // },
    },
});
