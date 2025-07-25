<div class="container-contact">

   <section id="contact">

      <div class="contact-wrapper">

         <form id="contact-form" class="form-horizontal" role="form">
            <h1 class="section-header">Contáctanos</h1>

            <div id="formHeader" class="formHeader">
               <h1 id="messageHeader">¡Dinos Hola!</h1>
            </div>
            <div class="form-group">
               <div class="col-sm-12">
                  <input type="text" class="form-control" id="name" placeholder="Nombre" name="name" value="" required>
               </div>
            </div>
            <div class="form-group">
               <div class="col-sm-12">
                  <input type="email" class="form-control" id="email" placeholder="Correo" name="email"
                     value="" required>
               </div>
            </div>
            <textarea class="form-control" rows="7" placeholder="Mensaje" name="message" id="message" 
               required></textarea>
            <button class="btn btn-primary send-button" id="submit" type="button" value="envio" onclick="sendEmail()">
               <div class="alt-send-button">
                  <i class="fa fa-paper-plane"></i><span class="send-text">¡Enviar!</span>
               </div>
            </button>
         </form>

         <div class="direct-contact-container">

            <div class="container-social-links">
            <img src="assets/main_logo_blanco_v2025.svg" id="img-contact-logo-white" alt="Logo Turespacio">
            <p class="text-center" id="mailto-link"><a href="mailto:info@turespacio" target="_blank" rel="noopener noreferrer">info@turespacio.com</a></p>

            <!--
            <ul class="contact-list">
               <li class="list-item"><i class="fa fa-map-marker fa-2x"><span class="contact-text place">Campos Eliseos 169, Col. Polanco, Miguel Hidalgo, CDMX</span></i></li>
               <li class="list-item"><i class="fa fa-phone fa-2x">
                  <span class="contact-text phone">
                     <a href="tel:1-212-555-5555" title="Queremos escucharte">1-212-555-5555</a>
                  </span>
                  </i>
               </li>

               <li class="list-item"><i class="fa fa-envelope fa-2x">
                  <span class="contact-text gmail">
                     <a href="mailto:myarto@turespacio.com" title="Envíanos un mail">myarto@turespacio.com</a>
                  </span>
                  </i>
               </li>
            </ul>

            <hr>
            -->
            <ul class="social-media-list">
               <li class="link-li-social-media"><a href="https://www.facebook.com/turespacio/" target="_blank"
                     class="contact-icon"> <i class="fa-brands fa-facebook-f" aria-hidden="true"></i></a> </li>
               <li class="link-li-social-media"><a href="https://www.instagram.com/turespacio/" target="_blank"
                     class="contact-icon"> <i class="fa-brands fa-instagram" aria-hidden="true"></i></a> </li>
               <li class="link-li-social-media"><a href="https://x.com/turespacio" target="_blank" class="contact-icon">
                     <i class="fa-brands fa-x-twitter" aria-hidden="true"></i></a> </li>
               <!--<li class="link-li-social-media"><a href="https://www.linkedin.com/company/turespacio/" target="_blank" class="contact-icon"> <i class="fa-brands fa-linkedin" aria-hidden="true"></i></a> </li>-->
            </ul>
            </div>

         </div>

      </div>
   </section>
</div>