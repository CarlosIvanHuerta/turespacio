<script setup lang="ts">
import { reactive } from 'vue'

const columns = reactive(['EstadoCivil', 'CreatedAt', 'Estatus', 'Accion'])
</script>
<template>
  <div>
    <v-server-table :columns="columns" :options="options" ref="tableData">
    </v-server-table>
  </div>
</template>

<style scoped lang="scss">

</style>