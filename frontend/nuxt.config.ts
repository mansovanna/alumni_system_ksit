// https://nuxt.com/docs/api/configuration/nuxt-config

import tailwindcss from "@tailwindcss/vite";
// Provide a minimal typing for `process.env` so TypeScript won't error in this config file
declare const process: { env: { [key: string]: string | undefined } };
export default defineNuxtConfig({
  compatibilityDate: "2025-07-15",
  devtools: { enabled: false },
  css: ["~/assets/css/main.css"],
  vite: {
    plugins: [tailwindcss()],
  },
  experimental: {
    asyncContext: true,
  },
  modules: ["@pinia/nuxt"],
  runtimeConfig: {
    public: {
      apiBase:
        process.env.NUXT_PUBLIC_API_BASE || "http://localhost:8000/api/v1",
    },
  },
});
