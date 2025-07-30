<script setup lang="ts">
import {reactive, ref} from 'vue'

const api = createApi()
const token = useUserToken()
const userData = useUserSession()
const columns = reactive(['Id','UrlExterna', 'PathImg', 'TextImportant', 'ClicksPublicidad', 'Estatus', 'Cliente'])
const isLoading = ref(false)
const tableData = ref(null)
const imageFile = ref<File | null>(null)
const isOpen = ref(false)
const notyf = useNotyf()
const errorImagen = ref('')
const inputsForms = ref({
  UrlExterna: '',
  TextImportant: '',
  Cliente: '',
  Imagen: ''
})
const options = reactive({
  async requestFunction(data: any) {
    isLoading.value = true
    data.role = userData.roleUser
    return await api
        .post('/publicidad/getDataTable', data,
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
  filterable: ['UrlExterna', 'TextImportant'],
  sortIcon: {
    base: 'fontawesome',
    up: 'fas fa-long-arrow-alt-up',
    down: 'fas fa-long-arrow-alt-down',
    is: 'fas fa-sort',
  },
  sortable: ['UrlExterna'],
  columnsDropdown: false,
  headings: {
    PathImg: 'Imagen',
    UrlExterna: 'Enlace',
    TextImportant: 'Texto principal',
    ClicksPublicidad: 'Clicks al enlace',
    Tile: 'Título',
    id: '#',
  },
  columnsClasses: {
    TextImportant: 'is-weight-600 has-text-justified is-uppercase',
    ClicksPublicidad: 'is-weight-600 has-text-centered is-uppercase',
    Cliente: 'is-weight-600 has-text-centered is-uppercase',
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
      .post('/publicidad/toglePublicidad', {
        number: value,
        id
          },
          {
            headers: {
              Authorization: `Bearer ${token.value}` // Asumiendo que el token está en localStorage
            }
          })
      .then(function (data) {
        let index = tableData.value.data.findIndex(x => x.Id === id)
        if (index !== -1) {
          tableData.value.data[index].Estatus = value
        }
      })
      .catch(function (e) {
        console.log(e)
      })
      .finally(() => {
        isLoading.value = false
      })
}
const handleImageUpload = (event: Event) => {
  const fileInput = (event.target as HTMLInputElement).files?.[0] || null
  imageFile.value = null
  errorImagen.value = '' // Limpiamos el error previo

  if (!fileInput) {
    errorImagen.value = 'Por favor, selecciona una imagen.'
    return
  }

  // Validamos que sea un archivo de imagen
  if (!fileInput.type.startsWith('image/')) {
    errorImagen.value = 'El archivo debe ser una imagen (PNG, JPG, etc.).'
    return
  }

  // Usar FileReader para comprobar las dimensiones
  const reader = new FileReader()
  reader.readAsDataURL(fileInput)

  reader.onload = (e) => {
    const image = new Image()
    image.src = e.target?.result as string

    image.onload = () => {
      if (image.width !== 1600 || image.height !== 165) {
        errorImagen.value = `La imagen debe tener dimensiones de 1600px x 164px. La imagen cargada tiene ${image.width}px x ${image.height}px.`
        inputsForms.value.Imagen = '' // Limpiar nombre del archivo en caso de error
      } else {
        // Imagen válida
        inputsForms.value.Imagen = fileInput.name
        errorImagen.value = '' // No hay error
        imageFile.value = fileInput
      }
    }
  }

  reader.onerror = () => {
    errorImagen.value = 'Hubo un problema al leer la imagen.'
  }
}
const savePublicidad = async () => {
  const formData = new FormData();
  formData.append("file", imageFile.value)
  formData.append("url", inputsForms.value.UrlExterna)
  formData.append("texto", inputsForms.value.TextImportant)
  formData.append("cliente", inputsForms.value.Cliente)

  try {
    // Enviar solicitud POST al backend
    const response = await api.post('/stores/publicidad', formData,{
      headers: {
        'Content-Type': 'multipart/form-data',
        Authorization: `Bearer ${token.value}` // Asumiendo que el token está en localStorage
      }
    })
    console.log(response)
    // Manejo exitoso de la solicitud
    //alert('¡Post creado exitosamente!');
    notyf.success({
      message: 'Registro exitoso',
      duration: 3000,
      position: {
        x: 'center',
        y: 'center',
      },
    })
    inputsForms.value.TextImportant = ''
    inputsForms.value.Cliente = ''
    inputsForms.value.UrlExterna = ''
    inputsForms.value.Imagen = ''
    imageFile.value = null
    isOpen.value = false
  } catch (error) {
    // Manejar errores si la solicitud falla
    console.error(error);
  }
}
</script>

<template>
  <div>
    <VModal
        is="form"
        :open="isOpen"
        title="Registrar nueva publicidad"
        size="big"
        actions="right"
        noclose
        @close="isOpen = false"
        @submit.prevent="isOpen = false"
    >
      <template #content>
        <div class="columns is-multiline">
          <div class="column is-12">
            <VField>
              <VControl icon="icon-park-outline:link-three" :is-valid="inputsForms.UrlExterna !== '' && inputsForms.UrlExterna !== ''" :has-error="inputsForms.UrlExterna.length === 0">
                <VInput
                    type="text"
                    placeholder="Ingresa el enlace de la publicidad"
                    v-model="inputsForms.UrlExterna"
                />
                <p class="help has-text-success" v-if="inputsForms.UrlExterna.length > 0" >
                  Url externa ok
                </p>
                <p class="help has-text-danger" v-else>
                  Ingrese una url externa
                </p>
              </VControl>
            </VField>
            <VField>
              <VControl icon="icon-park-outline:add-text-two" :is-valid="inputsForms.TextImportant !== '' && inputsForms.TextImportant !== ''" :has-error="inputsForms.TextImportant.length === 0">
                <VInput
                    type="text"
                    placeholder="Texto principal"
                    v-model="inputsForms.TextImportant"
                />
                <p class="help has-text-success" v-if="inputsForms.TextImportant.length > 0" >
                  Texto importante ok
                </p>
                <p class="help has-text-danger" v-else>
                  Ingrese un texto importante
                </p>
              </VControl>
            </VField>
            <VField>
              <VControl icon="icon-park-outline:personal-collection" :is-valid="inputsForms.Cliente !== '' && inputsForms.Cliente !== ''" :has-error="inputsForms.Cliente.length === 0">
                <VInput
                    type="text"
                    placeholder="Ingrese el cliente"
                    v-model="inputsForms.Cliente"
                />
                <p class="help has-text-success" v-if="inputsForms.Cliente.length > 0" >
                  Cliente de la publicidad ok
                </p>
                <p class="help has-text-danger" v-else>
                  Ingrese un cliente
                </p>
              </VControl>
            </VField>
          </div>
          <div class="column is-12">
            <VField grouped>
              <VControl>
                <div class="file has-name">
                  <label class="file-label">
                    <input
                        class="file-input"
                        type="file"
                        name="resume"
                        @change="handleImageUpload"
                    >
                    <span class="file-cta">
                    <span class="file-icon">
                      <i class="fas fa-cloud-upload-alt" />
                    </span>
                    <span class="file-label"> Selecciona imagen… </span>
                  </span>
                    <span class="file-name light-text"> {{ inputsForms.Imagen }} </span>
                  </label>
                </div>
              </VControl>
            </VField>
            <!-- Mostrar error si ocurre -->
            <p class="help has-text-danger" v-if="errorImagen">
              {{ errorImagen }}
            </p>
            <!-- Confirmación cuando la imagen es válida -->
            <p class="help has-text-success" v-if="inputsForms.Imagen && !errorImagen">
              Imagen válida
            </p>
          </div>
        </div>
      </template>
      <template #action>
        <VButton
            type="submit"
            color="primary"
            raised
            @click="savePublicidad"
        >
          Registrar
        </VButton>
      </template>
    </VModal>
    <VButton
        color="primary"
        icon="fas fa-plus"
        bold @click="isOpen = true"
        elevated
    >
      Agregar Publicidad
    </VButton>
    <VCard :radius="'rounded'">
      <v-server-table :columns="columns" :options="options" ref="tableData">
        <template #UrlExterna="props">
          <a :href="props.row.UrlExterna" target="_blank"> {{ props.row.UrlExterna }}</a>
        </template>
        <template #Estatus="props">
          <div v-if="props.row.Estatus === 1">
            <VTag color="success" label="ACTIVO" style="cursor: pointer;" @click="togglePublish(0, props.row.Id)" />
          </div>
          <div v-else>
            <VTag color="danger" label="OCULTO" style="cursor: pointer;" @click="togglePublish(1, props.row.Id)"/>
          </div>
        </template>
        <template #PathImg="props">
          <img :src="props.row.PathImg" alt="img" >
        </template>
      </v-server-table>
    </VCard>
  </div>
</template>

<style scoped lang="scss">

</style>