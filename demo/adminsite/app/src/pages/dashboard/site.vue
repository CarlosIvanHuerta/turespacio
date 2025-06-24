<script setup lang="ts">
const layoutSwitcher = useLayoutSwitcher()

definePage({
  meta: {
    requiresAuth: true,
  }, // Aquí todo debería estar correctamente cerrado.
})
</script>

<template>
  <component
    :is="(layoutSwitcher.dynamicLayoutComponent as any)"
    v-bind="layoutSwitcher.dynamicLayoutProps"
    close-on-change
    default-sidebar="dashboard"
    theme="color-curved"
  >
    <!-- Content Wrapper -->
    <RouterView v-slot="{ Component }">
      <Transition
        name="fade-fast"
        mode="out-in"
      >
        <component :is="Component" />
      </Transition>
    </RouterView>
  </component>
</template>
