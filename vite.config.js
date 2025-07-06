import { defineConfig } from 'vite'
import path from 'node:path';

export default defineConfig({
    build: {
        emptyOutDir: false,
        rollupOptions: {
            input: ['resources/js/init.js', 'resources/scss/tom-select.scss'],
            output: {
                entryFileNames: `init.min.js`,
                assetFileNames: file => {
                    return '[name].[ext]'
                }
            }
        },

        outDir: 'public',
    },

    resolve: {
        alias: {
            '@moonshine': path.resolve('./vendor/moonshine/moonshine/src/UI/resources/js'),
        },
    },
})
