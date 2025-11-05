// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: "2024-11-01",
  devtools: { enabled: true },

  // Application Configuration
  app: {
    head: {
      title: "Aviano Housing Platform",
      titleTemplate: "%s - Aviano Housing",
      meta: [
        { charset: "utf-8" },
        { name: "viewport", content: "width=device-width, initial-scale=1" },
        {
          name: "description",
          content:
            "All-in-one platform for off-base housing management at Aviano Air Base",
        },
      ],
      link: [{ rel: "icon", type: "image/x-icon", href: "/favicon.ico" }],
    },
  },

  // Runtime Configuration
  runtimeConfig: {
    // Private keys (server-side only)
    // Public keys (exposed to client)
    public: {
      apiBase: process.env.NUXT_PUBLIC_API_BASE || "http://localhost:8000/api",
      appUrl: process.env.NUXT_PUBLIC_APP_URL || "http://localhost:3000",
      mapboxToken: process.env.NUXT_PUBLIC_MAPBOX_TOKEN || "",
      openRouteServiceKey: process.env.NUXT_PUBLIC_OPENROUTESERVICE_KEY || "",
    },
  },

  // CSS Configuration
  css: [
    // '~/assets/css/main.css',  // Temporaneamente commentato
    "maplibre-gl/dist/maplibre-gl.css",
  ],

  // Modules
  modules: [
    "@pinia/nuxt",
    "@nuxtjs/i18n",
    "@nuxtjs/tailwindcss",
    "@vueuse/nuxt",
    "@nuxt/image",
    "@nuxtjs/color-mode",
    // '@nuxt/icon',  // Temporarily disabled - installation issue
    "dayjs-nuxt",
  ],

  // Pinia Configuration
  pinia: {
    storesDirs: ["./stores/**"],
  },

  // I18n Configuration (IT/EN full support)
  i18n: {
    locales: [
      {
        code: "en",
        iso: "en-US",
        name: "English",
        file: "en-US.json",
      },
      {
        code: "it",
        iso: "it-IT",
        name: "Italiano",
        file: "it-IT.json",
      },
    ],
    defaultLocale: "en",
    strategy: "no_prefix",
    detectBrowserLanguage: {
      useCookie: true,
      cookieKey: "aviano_lang",
      redirectOn: "root",
      alwaysRedirect: false,
    },
    langDir: "locales",
    lazy: true,
    vueI18n: "./i18n.config.ts",
  },

  // TailwindCSS Configuration
  tailwindcss: {
    cssPath: "~/assets/css/main.css",
    configPath: "tailwind.config.ts",
    exposeConfig: false,
    viewer: true,
  },

  // Color Mode Configuration
  colorMode: {
    preference: "system",
    fallback: "light",
    classSuffix: "",
  },

  // Nuxt Image Configuration
  image: {
    quality: 80,
    format: ["webp", "png", "jpg"],
    screens: {
      xs: 320,
      sm: 640,
      md: 768,
      lg: 1024,
      xl: 1280,
      xxl: 1536,
    },
  },

  // DayJS Configuration
  dayjs: {
    locales: ["en", "it"],
    defaultLocale: "en",
    defaultTimezone: "Europe/Rome",
    plugins: [
      "utc",
      "timezone",
      "relativeTime",
      "duration",
      "calendar",
      "localizedFormat",
    ],
  },

  // TypeScript Configuration
  typescript: {
    strict: true,
    shim: false,
    typeCheck: false,
  },

  // Vite Configuration
  vite: {
    define: {
      "process.env.DEBUG": false,
    },
    build: {
      rollupOptions: {
        output: {
          manualChunks: {
            maplibre: ["maplibre-gl"],
            radix: ["radix-vue"],
          },
        },
      },
    },
    optimizeDeps: {
      include: ["maplibre-gl", "radix-vue"],
    },
  },

  // Development Server Configuration
  devServer: {
    port: 3000,
    host: "0.0.0.0",
  },

  // Nitro Configuration (for server-side rendering)
  nitro: {
    compressPublicAssets: true,
    routeRules: {
      "/": { prerender: true },
    },
  },
});
