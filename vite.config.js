import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import laravel from 'laravel-vite-plugin'
import { quasar, transformAssetUrls } from '@quasar/vite-plugin'

export default defineConfig({
  server: {
    host: '0.0.0.0',
    port: 5173,
    hmr: {
      host: process.env.VITE_HMR_HOST || 'localhost',
      port: process.env.VITE_HMR_HOST ? 443 : 5173,
      protocol: process.env.VITE_HMR_HOST ? 'wss' : 'ws'
    }
  },
  resolve: {
    alias: {
      '@': '/resources/js',
      '@img': '/resources/img'
    }
  },
  plugins: [
    laravel({
      input: ['resources/css/app.css', 'resources/js/app.js'],
      refresh: true,
      publicDirectory: 'public',
      buildDirectory: 'build',
      // Configuración para manejar imágenes
      assets: [
        {
          alias: '/img',
          path: 'resources/img',
          recursive: true
        }
      ]
    }),
    vue({
      template: { transformAssetUrls }
    }),
    quasar({
      sassVariables: 'src/quasar-variables.sass'
    })
  ]
})
