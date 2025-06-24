<script setup lang="ts">
import { ref } from 'vue'
import {useNotyf} from "/@src/composables/notyf.ts"
import Editor from '@tinymce/tinymce-vue'

// Variables reactivas para los campos del formulario
const api = createApi()
const notyf = useNotyf()
const title = ref('') // Título del post
const content = ref('') // Contenido del post
const category = ref('') // Categoría del post
const post = ref({
  Title: '',
  body: '',
}) // Categoría del post

// Variable para manejar mensajes de error
const errorMessage = ref('')

// Lista de categorías opcional (puedes obtenerlas dinámicamente desde tu API)
const categories = ref([])

// Función para enviar los datos al backend
const submitPost = async () => {
  errorMessage.value = ''; // Limpiar mensajes de error previos

  // Validar que los campos requeridos estén completos
  if (!title.value || !content.value) {
    errorMessage.value = 'Por favor completa todos los campos obligatorios.';
    return;
  }

  try {
    // Enviar solicitud POST al backend
    const response = await api.post('/api/posts', {
      title: title.value,
      content: content.value,
      category: category.value ? category.value : null, // Enviar la categoría si está disponible
    });

    // Manejo exitoso de la solicitud
    alert('¡Post creado exitosamente!');
    title.value = '';
    content.value = '';
    category.value = '';
  } catch (error) {
    // Manejar errores si la solicitud falla
    errorMessage.value = 'Hubo un problema al crear el post. Inténtalo nuevamente.';
    console.error(error);
  }
}
const getDataSelects = () => {

  api.post('catalogos/selects',
      {
        select: 'categoriasPost'
      },
      {
      headers: {
        Authorization: `Bearer ${token.value}` // Asumiendo que el token está en localStorage
      }
  })
      .then(response => {
        console.log(response.data);
        categories.value = response.data;
      })
      .catch(error => {
        console.error('Error:', error);
      });

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
      <form @submit.prevent="submitPost" class="box">

        <!-- Campo: Título -->
        <div class="field">
          <label class="label">Título *</label>
          <div class="control">
            <input
                v-model="post.Title"
                class="input"
                type="text"
                placeholder="Ingresa el título del post"
                required
            />
          </div>
        </div>

        <!-- Campo: Contenido del Post -->
        <div class="field">
          <editor
              api-key="3hb9nq1vetcf5g2vgmzjvvim4b52df371bxprdgd6s5bv1cw"
              v-model="post.body"
              :init="editorConfig"
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
            />

          </div>

        </div>

        <!-- Botón para enviar -->
        <div class="field">
          <button type="submit" class="button is-primary is-fullwidth mt-3">
            Crear Post
          </button>
        </div>
      </form>
    </div>
    <div class="column is-6 mt-5">
      <h1 class="title has-text-centered">Visa previa</h1>
      <div class="box">
        <div class="has-text-centered">
            <p class="is-size-2 has-text-weight-bold">{{ post.Title }}</p>
          <span v-html="post.body"></span>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Responsive styling (opcional) */
textarea {
  resize: none;
}
</style>