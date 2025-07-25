<script setup lang="ts">
import { ref } from 'vue'

const props = defineProps({
  datos: {
    required: true,
    default: [],
    type: Array,
  }
})

const chartOptions = ref({
  chart: {
    plotBackgroundColor: null,
    plotBorderWidth: null,
    plotShadow: false,
    type: 'pie',
  },
  title: {
    text: 'Actividades realizadas en la pagína',
    align: 'center',
  },
  tooltip: {
    pointFormat: '{series.name}: <b>{point.y}</b>',
  },
  accessibility: {
    point: {
      valueSuffix: '',
    },
  },
  plotOptions: {
    pie: {
      allowPointSelect: true,
      cursor: 'pointer',
      dataLabels: {
        enabled: true,
        format: '<span style="font-size: 1.2em"><b>{point.name}</b>' +
          '</span><br>' +
          '<span style="opacity: 0.6">{point.y} ' +
          '</span>',
        connectorColor: 'rgba(128,128,128,0.5)'
      },
    },
  },
  series: [{
    name: 'Resultado',
    data: [
      { name: 'Petrol', y: 938899 },
      { name: 'Diesel', y: 1229600 },
      { name: 'Electricity', y: 325251 },
      { name: 'Other', y: 238751 },
    ],
  }],
  credits: {
    enabled: false,
  },
})
const updateChart = () => {
  chartOptions.value.series[0].data = []
  chartOptions.value.series[0].data.push({ name: 'Visitas', y: props.datos[0].vistasPagina })
  chartOptions.value.series[0].data.push({ name: 'Scroll', y: props.datos[0].scroll })
  chartOptions.value.series[0].data.push({ name: 'Primera Visita', y: props.datos[0].primeraVisita })
  chartOptions.value.series[0].data.push({ name: 'Sesiones', y: props.datos[0].sesionesIniciadas })
  chartOptions.value.series[0].data.push({ name: 'Usuarios Enganchados', y: props.datos[0].usuariosEnganchados })
}
onMounted(() => {
  updateChart()
})

defineExpose({ chartOptions, updateChart })
</script>

<template>
  <highchartsg :options="chartOptions" />
</template>

<style scoped lang="scss">

</style>
