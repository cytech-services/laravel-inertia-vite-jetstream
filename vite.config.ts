import { ConfigEnv, defineConfig } from 'vite'

import vue from '@vitejs/plugin-vue'

export default defineConfig(({ command }: ConfigEnv) => {
	return {
		plugins: [vue()],

		base: command === 'build' ? '/dist/' : '',

		publicDir: false,

		build: {
			manifest: true,
			outDir: 'public/dist',
			rollupOptions: {
				input: {
					app: '/resources/js/app.js',
				},
			},
		},

		server: {
			strictPort: true,
			port: 3030,
			// https: true,
			hmr: {
				host: 'localhost',
			},
		},

		resolve: {
			alias: {
				'@': '/resources/js',
			},
		},

		optimizeDeps: {
			include: [
				'vue',
				'@inertiajs/inertia',
				'@inertiajs/inertia-vue3',
				'@inertiajs/progress',
				'axios',
			],
		},
	}
})
