<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>
<div id="carouselExample" class="carousel slide" data-bs-ride="carousel" style="max-width: 1600px; /* Limita el ancho máximo del carrusel completo */
            width: 100%; /* Asegura que sea responsivo en pantallas más pequeñas */
            overflow: hidden; /* Evita cualquier contenido adicional fuera del contenedor */
            margin: 0 auto; /* Centra el carousel en el medio */">
    <!-- Indicadores (se llenan dinámicamente) -->
    <div class="carousel-indicators"></div>

    <!-- Contenido del carrusel (se llena dinámicamente) -->
    <div class="carousel-inner" style="max-height: 165px !important; /* Mantén la altura fija en el contenedor del carrusel */
            height: auto; /* Adapta la altura al contenido */"></div>

    <!-- Controles -->
    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Anterior</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Siguiente</span>
    </button>
</div>
<br><br>
<div class="pb-4 mb-4" style="background-image: url('assets/banners/lineaverde.jpg'); background-repeat: repeat-x; ">
   <div class="container" id="container-newsletter">
      <div class="row">

         <div class="col-xs-12 col-md-12 text-center" style="padding: 0% 10%">

            <div class="row">
               <!-- Banner de Industria Reuniones -->
               <div class="col-xs-5 col-md-5">
                  <img src="assets/banners/wonder.jpg" id="banner-wonder">
               </div>

               <div class="col-xs-7 col-md-7">
                  <!-- Newsletter -- >
                  <div style="padding:5em 0;"></div>
                  <form class="row g-3 mb-3 mt-4">
                     <p class="mb-0 pt-0" id="newsletter-title">NEWSLETTER</p>
                     <p class="mb-0 pt-0" id="newsletter-content-01">Últimas noticias del turismo Nacional e
                        Internacional</p>
                     <label for="newsletter-email" class="form-label" id="newsletter-joinus-01">¡Suscríbete!</label>
                     <div class="col-md-7 mt-0">
                        <input type="email" class="form-control" id="newsletter-email" placeholder="Correo Electrónico"
                           style="max-width:500px;">
                     </div>
                     <div class="col-md-5 mt-0">
                        <button type="submit" class="btn btn-primary mb-3" id="newsletter-join-action">Únete</button>
                     </div>
                  </form>
                  -->
                  <script src=https://www.incubatour.com/forms/2149124854/embed.js></script>

               </div>
            </div>

         </div>

      </div>
   </div>
</div>