<script setup lang="ts">
const pageTitle = useVueroContext<string>('page-title')
onMounted(() => {

})

useHead({
  title: 'turespacio Dashboard',
})
</script>

<template>
  <CompanyDashboard />
</template>
