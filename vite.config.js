import { defineConfig } from 'vite'
import path from 'node:path';
import laravel from 'laravel-vite-plugin'

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/init.js', 'resources/scss/tom-select.scss'],
            refresh: true,
        }),
    ],

    build: {
        emptyOutDir: false,
        outDir: 'public',

        rollupOptions: {
            output: {
                entryFileNames: `init.min.js`,
                assetFileNames: file => {
                    return '[name].[ext]'
                }
            }
        },
    },

    resolve: {
        alias: {
            '@moonshine': path.resolve('./vendor/moonshine/moonshine/src/UI/resources/js'),
        },
    },
})
