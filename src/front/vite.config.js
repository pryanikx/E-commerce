import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';

export default defineConfig({
  plugins: [react()],
  server: {
    port: 3000,
    strictPort: true, // Fail if port 3000 is in use
    host: '0.0.0.0', // Allow access from network (e.g., for testing)
  },
});