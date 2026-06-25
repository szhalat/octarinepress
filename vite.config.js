import path from 'path';
import {defineConfig, loadEnv} from 'vite'
import copy from "./.vite/copy";
import tailwindcss from "@tailwindcss/vite";
import externalGlobals from "rollup-plugin-external-globals";

const ROOT = path.resolve('../../../')
const BASE = __dirname.replace(ROOT, '');

// WordPress globals mapping for Vite
const wordPressGlobals = {
    '@wordpress/block-editor': 'wp.blockEditor',
    '@wordpress/blocks': 'wp.blocks',
    '@wordpress/components': 'wp.components',
    '@wordpress/compose': 'wp.compose',
    '@wordpress/core-data': 'wp.coreData',
    '@wordpress/data': 'wp.data',
    '@wordpress/edit-post': 'wp.editPost',
    '@wordpress/element': 'wp.element',
    '@wordpress/i18n': 'wp.i18n',
    '@wordpress/icons': 'wp.icons',
	    '@wordpress/plugins': 'wp.plugins',
	    '@wordpress/api-fetch': 'wp.apiFetch',
	    '@wordpress/hooks': 'wp.hooks',
	    '@wordpress/date': 'wp.date',
	    '@wordpress/rich-text': 'wp.richText',

    // React
    'react': 'React',
    'react-dom': 'ReactDOM',
    'lodash': 'lodash',
};

const externalize = (id) => {
    return id in wordPressGlobals;
};

const globalize = (id) => {
    return wordPressGlobals[id] || null;
};

export default ({mode}) => {
    process.env = {...process.env, ...loadEnv(mode, process.cwd())}

    return defineConfig({
        base: process.env.NODE_ENV === 'production' ? `${BASE}/dist/` : BASE,
        publicDir: 'public',
        server: {
            host: 'localhost',
            open: process.env.VITE_SITE_URL,
            cors: process.env.VITE_SITE_URL,
        },
	        define: {
	            global: {},
	        },
	        esbuild: {
	            jsx: 'transform',
	            jsxFactory: 'wp.element.createElement',
	            jsxFragment: 'wp.element.Fragment',
	        },
	        build: {
            manifest: 'manifest.json',
            assetsDir: '.',
            outDir: `dist`,
            emptyOutDir: true,
            rollupOptions: {
                input: [
                    'assets/js/main.js',
                    'assets/js/octarinepress-blocks.js',
                    'assets/css/styles.css',
                    'assets/css/editor.css',
                ],
                output: {
                    entryFileNames: '[name]-[hash].js',
                    assetFileNames: '[name]-[hash].[ext]',
                    chunkFileNames: '[name]-[hash].js',
                },
                external: externalize,
            },
        },
        plugins: [
            {
                name: 'php',
                handleHotUpdate({file, server}) {
                    if (file.endsWith('.php')) {
                        server.ws.send({type: 'full-reload', path: '*'})
                    }
                },
            },
            {
                name: 'js',
                handleHotUpdate({file, server}) {
                    if (file.endsWith('.js' || '.jsx' || '.ts' || '.tsx')) {
                        server.ws.send({type: 'full-reload', path: '*'})
                    }
                },
            },
            externalGlobals(globalize),
            tailwindcss(),
            copy({
                targets: [
                    {
                        src: `assets/img/**/*.{png,jpg,jpeg,svg,webp}`,
                    },
                    {
                        src: `assets/fonts/**/*.{woff2,woff}`,
                    },
                ],
            }),
        ],
    })
}
