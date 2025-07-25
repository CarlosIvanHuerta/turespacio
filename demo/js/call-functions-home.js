const CATEGORY_SLUT_POST = {
   TOP_TRAVEL_TIPS: 'tips-de-viaje',
   TOP_MEXICO : "mexico"
}

const CATEGORY_NAME_POST = {
   TOP_TRAVEL_TIPS: 'Tips de viaje',
   TOP_MEXICO: "México"
}

const CATEGORY_TERM_ID_POST = {
   TOP_TRAVEL_TIPS: 193,
   TOP_MEXICO: 173
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

// Obtiene los últimos 5 Post de la categoría Tips de Viaje
const getPostTravelTips = () => {
   const params = new URLSearchParams({
      stage: 'topPostTravelTips',
      tax_slut: CATEGORY_SLUT_POST.TOP_TRAVEL_TIPS,
      tax_name: CATEGORY_NAME_POST.TOP_TRAVEL_TIPS,
      tax_id: CATEGORY_TERM_ID_POST.TOP_TRAVEL_TIPS,
      limit: 5
   });

   return fetch(`conx/functions-data.php?${params.toString()}`)
      .then(response => response.json())
      .then(data => {
         if (data.success && data.counter !== 0) {
            renderCarouselTips(data)
         } else {
            console.error('Fallo en la conexión:', data.message);
         }
      })
      .catch(error => {
         console.error('Error en getPostTravelTips al realizar request: ', error);
      });
}

// Obtiene los últimos 5 Post de la categoría Tips de Viaje
const getPostMexico = () => {
   const params = new URLSearchParams({
      stage: 'topPostMexico',
      tax_slut: CATEGORY_SLUT_POST.TOP_MEXICO,
      tax_name: CATEGORY_NAME_POST.TOP_MEXICO,
      tax_id: CATEGORY_TERM_ID_POST.TOP_MEXICO,
      limit: 6
   });

   return fetch(`conx/functions-data.php?${params.toString()}`)
      .then(response => response.json())
      .then(data => {
         if (data.success && data.counter !== 0) {
            renderCarouselMexico(data)
         } else {
            console.error('Fallo en la conexión: ', data.message);
         }
      })
      .catch(error => {
         console.error('Error en getPostMexico al realizar request: ', error);
      });
}

// Obtiene los últimos 5 Post para el Slider Principal
const getPostSliderMain = () => {
   const params = new URLSearchParams({
      stage: 'topPostSliderMain',
   });

   return fetch(`conx/functions-data.php?${params.toString()}`)
      .then(response => response.json())
      .then(data => {
         if (data.success && data.counter !== 0) {
            renderSliderMain(data)
         } else {
            console.error('Fallo en la conexión:', data.message);
         }
      })
      .catch(error => {
         console.error('Error en getPostSliderMain al realizar request: ', error);
      });
}

const renderCarouselTips = (data) => {
   try {
      const containerCard = document.getElementById('card-container')
      const infoPost = data.post
      const inputOrder = ['t-3', 't-4', 't-5', 't-1', 't-2']

      infoPost.forEach((post, index) => {
         const radioId = inputOrder[index]
         const label = document.createElement('label')
         label.classList.add('item')
         label.setAttribute('for', radioId)

         const card = document.createElement('div')
         card.classList.add('mycard')

         const title = document.createElement('a')
         title.classList.add('cardtitle')
         title.textContent = post.post_title
         title.href = `${post.post_name}` // Asegurarse de que el enlace sea correcto

         const img = document.createElement('img')
         //img.src = 'https://i.pravatar.cc/200'
         img.src = post.thumbnail_url
         img.alt = post.post_title
         img.classList.add('cardimg')

         const content = document.createElement('p')
         content.classList.add('carddescription')

         const maxWords = 20
         const words = post.post_content.split(' ')
         const shortContent = words.slice(0, maxWords).join(' ') + (words.length > maxWords ? '...' : '')
         content.textContent = sanitizateTextWordPress(shortContent)

         card.appendChild(title)
         const imgDiv = document.createElement('div')
         imgDiv.appendChild(img)
         card.appendChild(imgDiv)

         const contentDiv = document.createElement('div')
         contentDiv.appendChild(content)
         card.appendChild(contentDiv)

         label.appendChild(card)
         containerCard.appendChild(label)
      })

      // Asegurar que el tercer radio esté seleccionado por defecto
      const defaultRadio = document.getElementById('t-3')
      if (defaultRadio) defaultRadio.checked = true
   } catch (error) {
      console.error('Error al renderizar el carrusel de Tips de Viaje:', error);
   }
}

const renderCarouselMexico = (data) => {
   try {
      //console.log(`${JSON.stringify(data)}`);

      const container = document.querySelector('#carouselMexicoPostCard .carousel-inner')
      container.innerHTML = ''
      const posts = data.post
      let currentRow = null

      posts.forEach((post, index) => {
         // Si es el primer card de cada grupo de 3, creamos un nuevo carousel-item
         if (index % 3 === 0) {
            const isActive = index === 0 ? 'active' : '';
            const item = document.createElement('div');
            item.className = `carousel-item carousel-item-mex ${isActive}`;
            currentRow = document.createElement('div');
            currentRow.className = 'row';
            item.appendChild(currentRow);
            container.appendChild(item);
         }

         // Crea la card
         let postContent = sanitizateTextWordPress(post.post_content);
         const cardCol = document.createElement('div');
         cardCol.className = 'col-md-4';
         cardCol.innerHTML = `
         <div class="card card-carousel-mx">
            <img class="img-card-carousel" alt="Imagen del post" src="${post.thumbnail_url}">
            <div class="card-body card-body-carousel-mx">
               <h4 class="card-title card-title-carousel-mx">${post.post_title}</h4>
               <p class="card-text card-text-carousel-mx">${truncateText(postContent, 120)}</p>
               <a href="${post.post_name}" class="link-read-more">Más</a>
            </div>
         </div>
      `;
         currentRow.appendChild(cardCol);
      });

   } catch (error) {
      console.error('Error al renderizar el carrusel de México:', error);
   }
}

const renderSliderMain = (data) => {
   try {
      //console.log(`${JSON.stringify(data)}`);

      const indicators = document.getElementById('carousel-indicators-main')
      const inner = document.getElementById('carousel-inner-main')
      const posts = data.post
      posts.forEach((post, index) => {

         // Botón indicador
         const button = document.createElement('button');
         button.type = 'button';
         button.setAttribute('data-bs-target', '#carouselTurespacio');
         button.setAttribute('data-bs-slide-to', index);
         button.setAttribute('aria-label', `Slide ${index + 1}`);
         if (index === 0) {
            button.classList.add('active');
            button.setAttribute('aria-current', 'true');
         }
         indicators.appendChild(button);

         // Slide
         const slideWrapper = document.createElement('a');
         slideWrapper.href = post.post_name || '#';

         const slide = document.createElement('div');
         slide.className = 'carousel-item' + (index === 0 ? ' active' : '');
         slide.style.backgroundImage = `url('${post.thumbnail_url}')`;

         slide.innerHTML = `
            <div class="d-flex h-100 align-items-center justify-content-center">
               <div class="carousel-caption">
                  <h4 class="text-white mb-0 label-carousel-description">${post.post_title}</h4>
               </div>
            </div>
         `;

         slideWrapper.appendChild(slide);
         inner.appendChild(slideWrapper);
      });

   } catch (error) {
      console.error('Error al renderizar el carrusel de México:', error);
   }
}

const truncateText = (htmlContent, maxLength) => {
   const tempDiv = document.createElement('div');
   tempDiv.innerHTML = htmlContent;
   const text = tempDiv.textContent || tempDiv.innerText || '';
   return text.length > maxLength ? text.substring(0, maxLength) + '...' : text;
}

const sendEmail = () => {
   try {

      const name = document.getElementById('name').value ?? null
      const email = document.getElementById('email').value ?? null
      const message = document.getElementById('message').value ?? null

      if ( !name || !email || !message ) {
         return false
      }

      const params = new URLSearchParams({
         stage: 'sendEmailData',
         name,
         email,
         message,
      });

      return fetch(`conx/functions-email.php?${params.toString()}`)
         .then(response => response.json())
         .then(data => {
            if (data.success) {
               console.log(data)
               console.log('Correo enviado correctamente');
               document.getElementById("contact-form").reset();
               $.notify("¡Correo enviado correctamente!", "success");
            } else {
               console.error('Fallo en la conexión:', data.message);
            }
         })
         .catch(error => {
            console.error('Error en sendEmail al realizar request: ', error);
         });
      
   } catch (error) {
      console.error('Error al enviar el correo:', error);
   }
}

// Llamadas de Inicialización...
$(() => {

   // Mostrar loader por si se oculta por CSS
   $('#page-loader').show()

   Promise.all([
      getPostSliderMain(),
      getPostTravelTips(),
      getPostMexico()
   ]).then(() => {
      // Ocultar loader al finalizar las llamadas
      $('#page-loader').fadeOut()

   }).catch(error => {
      console.error('Error en las llamadas de inicialización:', error)
      $('#page-loader').fadeOut() // Asegurarse de ocultar el loader en caso de error
   })
})