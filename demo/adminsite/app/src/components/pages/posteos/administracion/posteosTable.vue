<script setup lang="ts">
import { reactive } from 'vue'

const api = createApi()
const token = useUserToken()
const userData = useUserSession()
const columns = reactive(['id','Tile', 'PostDate', 'PostStatus', 'Content', 'Carousel'])
const isLoading = ref(false)
const tableData = ref(null)
const options = reactive({
  async requestFunction(data: any) {
    isLoading.value = true
    data.role = userData.roleUser
    return await api
        .post('/posts/getDataTable', data,
            {
              headers: {
                Authorization: `Bearer ${token.value}` // Asumiendo que el token está en localStorage
              }
            })
        .then(function (data) {
          return data.data
        })
        .catch(function (e) {
          console.log(e)
        })
        .finally(() => {
          isLoading.value = false
        })
  },
  pagination: {
    virtual: false,
  },
  listColumns: {

  },
  footerHeadings: true,
  perPage: 10,
  perPageValues: [5, 10, 15],
  addSortedClassToCells: true,
  filterByColumn: true,
  skin: 'table is-striped is-hoverable is-fullwidth is-bordered',
  filterable: ['Tile', 'PostDate'],
  sortIcon: {
    base: 'fontawesome',
    up: 'fas fa-long-arrow-alt-up',
    down: 'fas fa-long-arrow-alt-down',
    is: 'fas fa-sort',
  },
  sortable: ['PostDate'],
  columnsDropdown: false,
  headings: {
    Descripcion: 'Descripción',
    PostDate: 'Fecha de creación',
    PostStatus: 'Estatus',
    Tile: 'Título',
    id: '#',
  },
  columnsClasses: {
    Descripcion: 'is-weight-600 has-text-justified is-uppercase',
    Clave: 'is-weight-600 has-text-justified is-uppercase',
    Grupo: 'is-weight-600 has-text-justified is-uppercase',
    PostDate: 'is-weight-600 has-text-centered is-uppercase',
    Baja: 'is-weight-600 has-text-justified is-uppercase',
    id: 'has-text-centered hast-text-danger',
    PostStatus: 'has-text-centered',
    Carousel: 'is-weight-600 has-text-centered is-uppercase',
  },
  texts: {
    count: 'Mostrando {from} al {to} de {count} registros|{count} registros|Un registro',
    first: 'Primero',
    last: 'Último',
    filter: 'Filtro:',
    filterPlaceholder: 'Buscar:',
    limit: 'Registros:',
    page: 'Pagína:',
    noResults: 'No hay resultados',
    filterBy: 'Filtrar por {column}',
    loading: 'Consultado Base de datos, por favor espere...',
    defaultOption: 'Todos',
    //defaultOption: 'Elige {column}',
    columns: 'Columnas',
  },
})

const togglePublish = async (value: number, id: number) => {
  await api
      .post('/posts/togleCorousel', {
        number: value,
        id
          },
          {
            headers: {
              Authorization: `Bearer ${token.value}` // Asumiendo que el token está en localStorage
            }
          })
      .then(function (data) {
        let index = tableData.value.data.findIndex(x => x.id === id)
        if (index !== -1) {
          tableData.value.data[index].Carousel = value
        }
      })
      .catch(function (e) {
        console.log(e)
      })
      .finally(() => {
        isLoading.value = false
      })
}
</script>
<template>
  <div>
    <v-server-table :columns="columns" :options="options" ref="tableData">
      <template #Carousel="props">
        <div v-if="props.row.Carousel === 1">
          <VTag color="success" label="EN HOME" style="cursor: pointer;" @click="togglePublish(0, props.row.id)" />
        </div>
        <div v-else>
          <VTag color="danger" label="OCULTO" style="cursor: pointer;" @click="togglePublish(1, props.row.id)"/>
        </div>
      </template>
    </v-server-table>
  </div>
</template>

<style scoped lang="scss">

</style>