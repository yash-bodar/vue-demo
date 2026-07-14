import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import tailwindcss from '@tailwindcss/vite'
import vue from '@vitejs/plugin-vue'
import fs from 'fs'

export default defineConfig({
  base: '/vue-demo/public/',
  plugins: [
    laravel({
      input: ['resources/css/app.css', 'resources/js/app.js'],
      refresh: true,
    }),
    tailwindcss(),
    vue(),
  ],
  server: {
    watch: {
      ignored: ['**/storage/framework/views/**'],
    },
    host: process.env.VITE_DEV_HOST || 'localhost',
    port: 5173,
    cors: true,
    strictPort: true,
  },
})
