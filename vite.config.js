import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import laravel from 'laravel-vite-plugin'
import { quasar, transformAssetUrls } from '@quasar/vite-plugin'

export default defineConfig({
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
