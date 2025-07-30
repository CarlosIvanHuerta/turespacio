<script setup lang="ts">
import PublicidadTable from "/@src/components/pages/publicidad/administracion/publicidadTable.vue"

const pageTitle = useVueroContext<string>('page-title')
onMounted(() => {
  pageTitle.value = 'Administración de Publicidad 🗞️'
})

useHead(() => ({
  title: `Administración de Publicidad 🗞️`,
}))
definePage({
  meta: {
    requiresAuth: true,
  }, // Aquí todo debería estar correctamente cerrado.
})
</script>

<template>
  <publicidad-table></publicidad-table>
</template>
