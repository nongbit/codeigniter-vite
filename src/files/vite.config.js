import { resolve } from 'path'
import { defineConfig } from 'vite'

export default defineConfig({
  css: { devSourcemap: true },
  server: { host: '0.0.0.0' },
  build: {
    outDir: resolve(__dirname, 'public<OUTDIR>'),
    emptyOutDir: false,
    manifest: true,
    rollupOptions: {
      input: {
        'app': resolve(__dirname, 'app/Views<ENTRYPOINT>/assets/js/app.js'),
      },
    }
  }
})