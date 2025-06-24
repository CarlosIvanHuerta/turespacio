<script setup lang="ts">
import RegisterPosteos from "/@src/components/pages/posteos/registro/registerPosteos.vue";

const pageTitle = useVueroContext<string>('page-title')
onMounted(() => {

})

useHead({
  title: 'turespacio Dashboard',
})
</script>

<template>
  <register-posteos></register-posteos>
</template>
