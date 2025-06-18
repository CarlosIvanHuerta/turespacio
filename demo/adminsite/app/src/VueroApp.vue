<script setup lang="ts">
// This is the global app setup function
const { locale } = useI18n()

const rtlCodes = [
  'ar',
  'arc',
  'dv',
  'fa',
  'ha',
  'he',
  'khw',
  'ks',
  'ku',
  'ps',
  'ur',
  'yi',
]

useHead(() => ({
  title: 'TURESPACIO - Vue 3 Admin',
  link: [
    {
      rel: 'icon',
      href: '/favicon.svg',
      type: 'image/svg+xml',
    },
    {
      rel: 'alternate icon',
      href: '/favicon.ico',
      type: 'image/x-icon',
      sizes: '16x16',
    },
    {
      rel: 'apple-touch-icon',
      href: '/apple-touch-icon.png',
      type: 'image/png',
      sizes: '180x180',
    },
    {
      rel: 'mask-icon',
      href: '/favicon.svg',
      type: 'image/svg+xml',
      color: '#FFFFFF',
    },
  ],
  meta: [
    // Critical Tags
    { charset: 'utf-8' },
    {
      name: 'viewport',
      content: 'width=device-width, initial-scale=1, shrink-to-fit=no',
    },
    // PWA theme color
    {
      name: 'theme-color',
      content: '#ffffff',
    },
    {
      name: 'msapplication-TileColor',
      content: '#232326',
    },
    // SEO
    {
      name: 'robots',
      content: 'index,follow,max-image-preview:large',
    },
    {
      name: 'description',
      content:
        'turespacio sitio que te mostrará el mejor destino, para tus próximas vacaciones.',
    },
    // Open Graph
    {
      property: 'og:site_name',
      content: 'turespacio red para tus próximas vacaciones',
    },
    {
      property: 'og:locale',
      content: 'en_US',
    },
    {
      property: 'og:type',
      content: 'article',
    },
    {
      property: 'og:url',
      content: 'https://vuero.cssninja.io/',
    },
    {
      property: 'og:image:type',
      content: 'image/png',
    },
    {
      property: 'og:image:width',
      content: '1200',
    },
    {
      property: 'og:image:height',
      content: '630',
    },
    {
      property: 'og:image',
      content:
        'https://turespacio.com/inv/tlogo.png',
    },
    // Twitter
    {
      name: 'twitter:site',
      content: '@cssninjaStudio',
    },
  ],
  htmlAttrs: {
    lang: locale.value,
    dir: rtlCodes.includes(locale.value) ? 'rtl' : 'ltr',
  },
}))
</script>

<template>
  <div>
    <Suspense>
      <RouterView v-slot="{ Component }">
        <Transition
          name="fade-slow"
          mode="out-in"
        >
          <component :is="Component" />
        </Transition>
      </RouterView>
    </Suspense>
  </div>
</template>
