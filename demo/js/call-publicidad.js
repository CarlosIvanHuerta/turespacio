// URL del endpoint (ajústala según sea necesario)
const endpointUrl = '/conx/access-publicidad.php';

// Función para obtener los datos del endpoint y renderizar el carousel
async function fetchAndRenderCarousel() {
    try {
        // Hacer la solicitud al endpoint
        const response = await fetch(endpointUrl);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        console.log('Publicidad cargada correctamente. ssss');

        const data = await response.json();
        console.log(data);
        // Seleccionar elementos del DOM
        const carouselInner = document.querySelector('#carouselExample .carousel-inner');
        const carouselIndicators = document.querySelector('#carouselExample .carousel-indicators');

        // Limpiar contenido existente
        carouselInner.innerHTML = '';
        carouselIndicators.innerHTML = '';

        // Recorrer cada elemento del array para generar el carousel
        data.post.forEach((item, index) => {
            // Crear el elemento de la diapositiva
            const isActive = index === 0 ? 'active' : '';
            const carouselItem = `
                <div class="carousel-item ${isActive}">
                    <img src="${item.path_img}" class="d-block w-100" alt="${item.text_important}" style="max-height: 165px;">
                    <div class="carousel-caption d-none d-md-block">
                        <h5>${item.text_important}</h5>
                        ${item.url_externa !== 'Sin publicidad' ? `<a href="${item.url_externa}" target="_blank" class="btn btn-primary">Ver más</a>` : ''}
                    </div>
                </div>
            `;
            carouselInner.innerHTML += carouselItem;

            // Crear el indicador correspondiente
            const indicator = `<button type="button" data-bs-target="#carouselExample" data-bs-slide-to="${index}" class="${isActive}" aria-label="Slide ${index + 1}"></button>`;
            carouselIndicators.innerHTML += indicator;
        });
    } catch (error) {
        console.error('Error al cargar el carrusel:', error);
    }
}
// Llamadas de Inicialización...
$(async () => {
    console.log('Cargando publicidad...');
    await fetchAndRenderCarousel()
})
