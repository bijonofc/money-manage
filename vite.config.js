import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import laravel from 'laravel-vite-plugin'
import path from 'path'

export default defineConfig({
    plugins: [
        laravel({
            input: {
                app: 'resources/js/app.js',      // your admin or main app
            },
            refresh: true,

        }),
        vue(),
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources/js'),
        },
        extensions: ['.js', '.json', '.vue']
    },
    css: {
        devSourcemap: true, // Enable source maps for CSS in development
    },
    build: {
        outDir: 'public/build',
        assetsDir: 'assets',
        rollupOptions: {},
    },

    server: {
        cors: true, // ✅ সব origin allow করবে
    }
})
