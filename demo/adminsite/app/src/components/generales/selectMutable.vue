<script setup lang="ts">
import { ref, watch, onMounted, onBeforeUnmount, getCurrentInstance, vModelDynamic, inject } from 'vue'

const internalInstance = getCurrentInstance()
const emitter = inject("emitter")
const input = ref('')
const api = createApi()
const optionsP = ref([])
const selectedOption = ref(null)
const props = defineProps({
  idElement: { default: 0, type: Number, required: false },
  enabl: { default: false, type: Boolean, required: true },
  multiple: { default: false, type: Boolean, required: true },
  label: { default: 'Agregue prop label', type: String, required: true },
  emitEvent: { default: 'Agregue prop emit event', type: String, required: true },
  urlPost: { default: 'Agregue prop urlPost', type: String, required: true },
  placeh: { default: 'Agregue un place holder', type: String, required: true },
  selectType: { default: 'catalogos', type: String, required: true },
  appendToBody: { default: false, type: Boolean, required: true },
  defaultValue: { default: false, type: Boolean, required: true },
  valueDefault: { default: {}, type: Object, required: true },
})

watch(selectedOption, (newselectedOption) => {
  emitter.emit('event' + props.emitEvent, { data: newselectedOption })
  //console.log('event' + props.emitEvent)
})

const buscarVarlor = (buscar: Number) => {
  let valor = optionsP.value.findIndex(item => parseInt(item.Id) === buscar)
  selectedOption.value = optionsP.value[valor]
}
const getData = async () => {
  optionsP.value = []
  const token = useUserToken()

  await api.post(props.urlPost + '', {
    select: props.selectType,
  }, {
    headers: {
      Authorization: `Bearer ${token.value}` // Asumiendo que el token está en localStorage
    }
  })
      .then(({ data }) => {
        let datos = data.data
        optionsP.value = datos
        if (props.defaultValue) {
          selectedOption.value = props.valueDefault
        }
      })
}

onMounted(async () => {
  await getData()
})
onBeforeUnmount(() => {
  // clearing all events
  emitter.all.clear()
})
defineExpose({ input, buscarVarlor, selectedOption, optionsP })
</script>

<template>
  <div>
    <label class="is-weight-600">{{ label }}:</label>
    <select-v
        v-model="selectedOption"
        :options="optionsP"
        :disabled="props.enabl"
        :multiple="multiple"
        :placeholder="props.placeh"
        appendToBoody
    >
      <template #no-options="">
        No existen opciones para mostrar.
      </template>
    </select-v>
  </div>
</template>
<style lang="scss">
.style-chooser .vs__search::placeholder,
.style-chooser .vs__dropdown-toggle,
.style-chooser .vs__selected,
.style-chooser .vs__dropdown-menu {
  background: #535353;
  border: none;
  color: #ffffff;
  text-transform: lowercase;
  font-variant: small-caps;
}

.vs__dropdown-menu {
  background: #535353;
  border: none;
  color: #ffffff;
  text-transform: lowercase;
  font-variant: small-caps;
}

.style-chooser .vs__clear,
.style-chooser .vs__open-indicator {
  fill: #394066;
}
</style>