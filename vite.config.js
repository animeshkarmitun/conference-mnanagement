import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    build: {
        // Generate manifest.json in outDir
        manifest: true,
        // Output directory for production build
        outDir: 'public/build',
        // Ensure assets are properly hashed
        rollupOptions: {
            output: {
                manualChunks: undefined,
            },
        },
    },
    // Ensure proper base URL in production
    base: process.env.APP_ENV === 'production' ? '/build/' : '/',
});
