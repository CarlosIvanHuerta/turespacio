const CATEGORY_SLUT_POST = {
   POST_TRAVEL_TIPS: 'tips-de-viaje',
   POST_MEXICO : "mexico",
   POST_MUNDO : "el-mundo",
   POST_QUE_HACER : "que-hacer",
   POST_BEACH : "beach",
   POST_TURIBREVES : "turibreves",
}

const CATEGORY_NAME_POST = {
   POST_TRAVEL_TIPS: 'Tips de viaje',
   POST_MEXICO: "México",
   POST_MUNDO : "El Mundo",
   POST_QUE_HACER : "Qué hacer",
   POST_BEACH : "Beach",
   POST_TURIBREVES : "TuriBreves",
}

const CATEGORY_TERM_ID_POST = {
   POST_TRAVEL_TIPS: 193,
   POST_MEXICO: 173,
   POST_MUNDO : 118,
   POST_QUE_HACER : 1280,
   POST_BEACH : 1,
   POST_TURIBREVES : 33,
}

let offset = 6
const limitNextPost = 3

// Obtiene los últimos 5 Post de la categoría Tips de Viaje
const getPostCategoryWorld = () => {
   const params = new URLSearchParams({
      stage: 'categoryWorld',
      tax_slut: CATEGORY_SLUT_POST.POST_MUNDO,
      tax_name: CATEGORY_NAME_POST.POST_MUNDO,
      tax_id: CATEGORY_TERM_ID_POST.POST_MUNDO,
      limit: 6
   });

   return fetch(`conx/functions-data.php?${params.toString()}`)
      .then(response => response.json())
      .then(data => {
         if (data.success && data.counter !== 0) {
            renderPostCategoryWorld(data)
         } else {
            console.error('Fallo en la conexión:', data.message);
         }
      })
      .catch(error => {
         console.error('Error en getPostTravelTips al realizar request: ', error);
      });
}

const renderPostCategoryWorld = (data) => {
   try {

      const container = document.querySelector('#container-post-cat-mundo')
      //container.innerHTML = ''
      const posts = data.post
      let currentRow = null

      posts.forEach((post, index) => {
         // Si es el primer card de cada grupo de 3, creamos un nuevo carousel-item
         if (index % 3 === 0) {
            currentRow = document.createElement('div')
            currentRow.className = 'row custom-row-post'
            container.appendChild(currentRow)
         }

         // Crea la card
         let postContent = sanitizateTextWordPress(post.post_content);
         const cardPost = document.createElement('div');
         cardPost.className = 'col-md-4 mb-4';
         cardPost.innerHTML = `
         <div class="card card-carousel-mx">
            <div class="img-card-container-category">
               <img class="img-card-carousel-category" alt="Imagen del post" src="${post.thumbnail_url}">
            </div>
            <div class="card-body card-body-carousel-mx">
               <h4 class="card-title card-title-carousel-mx">${post.post_title}</h4>
               <p class="card-text card-text-carousel-mx">${truncateText(postContent, 120)}</p>
               <a href="${post.post_name}" class="link-read-more">Más</a>
            </div>
         </div> `;
         currentRow.appendChild(cardPost);
      });

   } catch (error) {
      console.error('Error al renderizar Post México:', error);
   }
}

const truncateText = (htmlContent, maxLength) => {
   const tempDiv = document.createElement('div');
   tempDiv.innerHTML = htmlContent;
   const text = tempDiv.textContent || tempDiv.innerText || '';
   return text.length > maxLength ? text.substring(0, maxLength) + '...' : text;
}

// Sanitizar contenido generado por WordPress
const sanitizateTextWordPress = text => {
   return text
      // Eliminar comentarios tipo wp:*
      .replace(/<!--\s*\/?wp:[^>]*?-->/gi, '')
      // Eliminar cualquier comentario HTML
      .replace(/<!--[\s\S]*?-->/g, '')
      // Eliminar etiquetas específicas (pero no su contenido)
      .replace(/<\/?(p|a|i|strong)[^>]*>/gi, '')
      // Reemplazar entidad &nbsp; por espacio
      .replace(/&nbsp;/gi, ' ')
      // Reemplazar múltiples espacios por uno solo
      .replace(/\s+/g, ' ')
      // Eliminar espacios al principio y final
      .trim();
}

const getNextPostWorld = () => {
   $('#page-loader').show()

   const params = new URLSearchParams({
      stage: 'getNextPostByCategory',
      tax_slut: CATEGORY_SLUT_POST.POST_MUNDO,
      tax_name: CATEGORY_NAME_POST.POST_MUNDO,
      tax_id: CATEGORY_TERM_ID_POST.POST_MUNDO,
      limit: limitNextPost,
      offset: (offset)
   });

   return fetch(`conx/functions-data.php?${params.toString()}`)
      .then(response => response.json())
      .then(data => {
         if (data.success && data.counter !== 0) {
            renderPostCategoryWorld(data)
            offset += limitNextPost
            // Ocultar loader al finalizar las llamadas
            $('#page-loader').fadeOut()
         } else {
            console.error('Fallo en la conexión:', data.message);
         }
      })
      .catch(error => {
         console.error('Error en getPostTravelTips al realizar request: ', error);
      });
}

// Llamadas de Inicialización...
$(() => {
   // Mostrar loader por si se oculta por CSS
   $('#page-loader').show()

   Promise.all([
      getPostCategoryWorld()
   ]).then(() => {
      // Ocultar loader al finalizar las llamadas
      $('#page-loader').fadeOut()

   }).catch(error => {
      console.error('Error en las llamadas de inicialización:', error)
      $('#page-loader').fadeOut() // Asegurarse de ocultar el loader en caso de error
   })
})