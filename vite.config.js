import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { viteStaticCopy } from 'vite-plugin-static-copy';
import lodash from 'lodash';

export default defineConfig({
    build: {
        manifest: true,
        rtl: true,
        outDir: 'public/build/',
        cssCodeSplit: true,
        // buildDirectory: 'assets',
        rollupOptions: {
            output: {
                assetFileNames: (css) => {
                    if (css.name.split('.').pop() == 'css') {
                        return 'css/' + `[name]` + '.min.' + 'css';
                    } else {
                        return 'icons/' + css.name;
                    }
                },
                entryFileNames: 'js/' + `[name]` + `.js`,
            },
        },
    },
    plugins: [
        laravel(
            {
                input: [
                    'resources/scss/bootstrap.scss',
                    'resources/scss/icons.scss',
                    'resources/scss/app.scss',
                    'resources/scss/preloader.scss',
                ],
                refresh: true
            }
        ),
        viteStaticCopy({
            targets: [
                {
                    src: 'resources/fonts',
                    dest: ''
                },
                {
                    src: 'resources/images',
                    dest: ''
                },
                {
                    src: 'resources/js',
                    dest: ''
                },
                {
                    src: 'resources/libs',
                    dest: ''
                },
            ]
        }),
        
    ],
});
