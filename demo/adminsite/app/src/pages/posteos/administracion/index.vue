<script setup lang="ts">
import PosteosTable from "/@src/components/pages/posteos/administracion/posteosTable.vue";

const pageTitle = useVueroContext<string>('page-title')
onMounted(() => {
  pageTitle.value = 'Administración de Posteos 📚'
})

useHead(() => ({
  title: `Administración de Posteos 📚`,
}))
definePage({
  meta: {
    requiresAuth: true,
  }, // Aquí todo debería estar correctamente cerrado.
})
</script>

<template>
  <posteos-table></posteos-table>
</template>
