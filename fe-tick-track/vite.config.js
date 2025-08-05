import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
  plugins: [vue()],
  resolve: {
    alias: {
      '@': '/src' // Pastikan ini sesuai dengan struktur proyek Anda
    }
  }
})