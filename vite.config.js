import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import i18n from 'laravel-vue-i18n/vite';
import * as glob from "glob";
import path from "node:path";
import { fileURLToPath } from "node:url";


let sass = Object.fromEntries(
    glob
        .sync("resources/styles/**/*.scss")
        .map((file) => [
            path.relative(
                "resources/styles",
                file.slice(0, file.length - path.extname(file).length)
            ),
            fileURLToPath(new URL(file, import.meta.url)),
        ])
);
sass = Object.values(sass);

let js = Object.fromEntries(
    glob
        .sync("resources/js/*.js")
        .map((file) => [
            path.relative(
                "resources/js",
                file.slice(0, file.length - path.extname(file).length)
            ),
            fileURLToPath(new URL(file, import.meta.url)),
        ])
);
js = Object.values(js);

let input = [sass, js];
input = [].concat(...input);

let inputEnv = process.env.APP_ENV === 'development' ? ['resources/styles/app/app.scss', 'resources/js/app.js', 'resources/styles/admin/admin.scss', 'resources/js/admin-panel.js'] : input;

export default defineConfig({
    build: {
        outDir: './public/build/',
        rollupOptions: {
            output: {
                assetFileNames: (assetInfo) => {
                    // Get file extension
                    // TS shows asset name can be undefined so I'll check it and create directory named `compiled` just to be safe
                    let extension = assetInfo.name?.split('.').at(1) ?? 'compiled'

                    // This is optional but may be useful (I use it a lot)
                    // All images (png, jpg, etc) will be compiled within `images` directory,
                    // all svg files within `icons` directory
                    // if (/png|jpe?g|gif|tiff|bmp|ico/i.test(extension)) {
                    //     extension = 'images'
                    // }

                    // if (/svg/i.test(extension)) {
                    //     extension = 'icons'
                    // }

                    // Basically this is CSS output (in your case)
                    return `${extension}/[name].[hash][extname]`
                },
                chunkFileNames: 'js/chunks/[name].[hash].js', // all chunks output path
                entryFileNames: 'js/[name].[hash].js' // all entrypoints output path
            }
        }
    },
    plugins: [
        vue({
            template: {
                compilerOptions: {
                    isCustomElement: (tag) => ['cmp-'].includes(tag),
                },
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
        laravel({
            // input: ['resources/styles/app/app.scss', 'resources/js/app.js', 'resources/styles/admin/admin.scss', 'resources/js/admin-panel.js'],
            input: inputEnv,
            refresh: true,
        }),
        i18n({
            // you can also change your langPath here
            // langPath: 'locales' 
            additionalLangPaths: [
                'public/locales' // Load translations from this path too! 
            ]
        })
    ],
    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
            boot: './bootstrap',
        },
    },
    server: {
        https: false,
        host: '192.168.178.26',
        hmr: {
            host: '192.168.178.26'
        },
    },
});
