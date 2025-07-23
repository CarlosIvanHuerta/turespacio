<script setup lang="ts">
import {inject, ref, onUnmounted} from 'vue'
import Editor from '@tinymce/tinymce-vue'

const token = useUserToken()
const dataUser = useUserSession()
const emitter = inject("emitter")
// Variables reactivas para los campos del formulario
const api = createApi()
const notyf = useNotyf()
const title = ref('') // Título del post
const content = ref('') // Contenido del post
const category = ref('') // Categoría del post
const estatus = ref('Borrador') // Categoría del post
const labels = ref('') // Categoría del post
const nameImage = ref('') // Categoría del post
const editorBody = ref(null) // Categoría del post

// Referencias para almacenar la imagen seleccionada y la vista previa
const selectedImage = ref<File | null>(null) // Aquí se almacena el archivo
const previewImage = ref<string | null>(null) // Aquí se almacena la URL para la vista previa


// Variable para manejar mensajes de error
const errorMessage = ref('')

// Función para enviar los datos al backend
const submitPost = async () => {
  errorMessage.value = ''; // Limpiar mensajes de error previos
  console.log("entramos")
  // Validar que los campos requeridos estén completos
  if (!title.value || !content.value) {
    errorMessage.value = 'Por favor completa todos los campos obligatorios.';
    return;
  }
  if (!selectedImage.value) {
    alert("¡Primero selecciona una imagen!");
    return;
  }

  const formData = new FormData();
  formData.append("file", selectedImage.value)
  formData.append("title", title.value);
  formData.append("content", content.value);
  formData.append("category", category.value);
  formData.append("estatus", estatus.value);
  formData.append("labels", JSON.stringify(labels.value)); // Convertimos etiquetas a JSON
  formData.append("type", "create");
  formData.append("ut", dataUser.user.id);


  try {
    // Enviar solicitud POST al backend
    const response = await api.post('/stores/posts', formData,{
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
    title.value = ''
    selectedImage.value = null
    nameImage.value = ''
    content.value = ''
    category.value = ''
  } catch (error) {
    // Manejar errores si la solicitud falla
    errorMessage.value = 'Hubo un problema al crear el post. Inténtalo nuevamente.';
    console.error(error);
  }
}

const editorConfig = {
  language: 'es', // select language
  height: 500,
  menubar: true,
  branding: false,
  plugins: [
    // Core editing features
    'anchor', 'autolink', 'charmap', 'codesample', 'emoticons', 'image', 'link', 'lists', 'media', 'searchreplace', 'table', 'visualblocks', 'wordcount',
    // Your account includes a free trial of TinyMCE premium features
  ],
  content_css: "default",
  skin: "oxide",
  toolbar: 'undo redo|  forecolor backcolor  | blocks fontfamily fontsize | bold italic underline strikethrough color | link image media table mergetags | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
  tinycomments_mode: 'embedded',
  tinycomments_author: 'Carlos Iván Huerta',
  // Subir imágenes con Axios
  images_upload_handler: async (blobInfo, progress) => new Promise(async (resolve, reject) => {
    const formData = new FormData();
    formData.append('file', blobInfo.blob(), blobInfo.filename());
    const token = useUserToken()
    try {
      const response = await api.post('/images/save', formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
          Authorization: `Bearer ${token.value}` // Asumiendo que el token está en localStorage
        },
      })
      console.log(response)
      resolve(response.data.url) // TinyMCE inserta la URL en el contenido
    }
    catch (error) {
      //console.log(error)
      reject('Error al subir la imagen: ' + error)
    }
  })
}
// Método para manejar la selección de la imagen
const handleImageUpload = (event: Event) => {
  const file = (event.target as HTMLInputElement).files?.[0] || null
  // Validar si el archivo es una imagen
  if (file && file.type.startsWith("image/")) {
    selectedImage.value = file
    // Generar la vista previa de la imagen
    previewImage.value = URL.createObjectURL(file)
    nameImage.value = file.name
  }
  else {
    alert("Por favor, selecciona una imagen válida en formato JPG, PNG o GIF.")
  }
}
// Método para enviar la imagen al backend

emitter.on('eventSelectCategoria', (data) => {
  console.log(data.data.value)
  category.value = data !== null ? data.data.value : ''
})
emitter.on('eventSelectEstatus', (data) => {
  estatus.value = data !== null ? data.data.value : ''
})
emitter.on('eventSelectTags', (data) => {
  labels.value = data !== null ? data.data : []
  console.log(labels.value)
})
onUnmounted(() => {
  emitter.all.clear()
})
</script>

<template>
  <div class="columns">
    <div class="column is-6 mt-5">
      <h1 class="title has-text-centered">Crear Nuevo Post</h1>

      <!-- Mensaje de error -->
      <div v-if="errorMessage" class="notification is-danger mt-3">
        {{ errorMessage }}
      </div>

      <!-- Formulario para crear el post -->
      <VCard radius="regular">
        <form @submit.prevent="submitPost">
          <!-- Campo: Título -->
          <div class="field">
            <label class="label">Título *</label>
            <div class="control">
              <input
                  v-model="title"
                  class="input"
                  type="text"
                  placeholder="Ingresa el título del post"
                  required
              />
            </div>
          </div>

          <!-- Campo: Contenido del Post -->
          <div class="">
            <editor
                api-key="3hb9nq1vetcf5g2vgmzjvvim4b52df371bxprdgd6s5bv1cw"
                v-model="content"
                :init="editorConfig"
                ref="editorBody"

            />
          </div>

          <!-- Selección: Categoría -->
          <div class="columns">
            <div class="column is-4">
              <select-mutable
                  :enabl="false"
                  :multiple="false"
                  :label="'Categoría del post'"
                  :placeh="'Selecciona la categoría'"
                  :append-to-body="true"
                  :emit-event="'SelectCategoria'"
                  :url-post="'catalogos/selects'"
                  :select-type="'categoriasPost'"
                  :value-default="{label: 'Borrador', value: 'Borrador'}"
                  :default-value="false"
              />
            </div>
            <div class="column is-4">
              <select-mutable
                  :enabl="false"
                  :multiple="false"
                  :label="'Estatus del post'"
                  :placeh="'Selecciona el estatus'"
                  :append-to-body="true"
                  :emit-event="'SelectEstatus'"
                  :url-post="'catalogos/selects'"
                  :select-type="'postStatusAndVisibility'"
                  :value-default="{label: 'Borrador', value: 'Borrador'}"
                  :default-value="true"
              />
            </div>
            <div class="column is-4">
              <select-mutable
                  :enabl="false"
                  :multiple="true"
                  :label="'Etiquetas del post'"
                  :placeh="'Selecciona etiquetas'"
                  :append-to-body="true"
                  :emit-event="'SelectTags'"
                  :url-post="'catalogos/selects'"
                  :select-type="'tagsPost'"
                  :value-default="{label: 'Borrador', value: 'Borrador'}"
                  :default-value="false"
              />

            </div>

          </div>
          <div>
            <!-- Título -->
            <span class="has-text-weight-bold is-size-4">Selecciona la Imagen Destacada</span>

            <div class="file has-name">
              <label class="file-label">
                <input class="file-input" type="file" name="resume" @change="handleImageUpload" />

                <span class="file-cta">
      <span class="file-icon">
        <i class="fas fa-upload"></i>
      </span>
      <span class="file-label"> Elije la imagen… </span>
    </span>
                <span class="file-name"> {{ nameImage }} </span>
              </label>
            </div>

            <!-- Vista previa de la imagen -->
            <div v-if="previewImage" style="margin-top: 20px;">
              <h4>Vista Previa:</h4>
              <img :src="previewImage" alt="Vista previa de imagen" style="max-width: 100%; height: auto; border-radius: 8px; border: 1px solid #ccc;" />
            </div>

          </div>
          <!-- Botón para enviar -->
          <div class="field">
            <button type="submit" class="button is-primary is-fullwidth mt-3" :disabled="!selectedImage">
              Crear Post
            </button>
          </div>
        </form>
      </VCard>

    </div>
    <div class="column is-6 mt-5">
      <h1 class="title has-text-centered">Visa previa</h1>
        <VCard radius="regular">
          <div class="has-text-centered">
              <p class="is-size-2 has-text-weight-bold">{{ title }}</p>
            <span v-html="content"></span>
          </div>
        </VCard>
    </div>
  </div>
</template>

<style scoped>
/* Responsive styling (opcional) */
textarea {
  resize: none;
}
</style>