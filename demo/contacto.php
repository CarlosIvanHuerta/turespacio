<?php ini_set('display_errors', 0); // No mostrar errores PHP al usuario ?>
<!DOCTYPE html>
<html lang="es">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <meta name="color-scheme" content="light dark">
   <title>Turespacio - Contacto</title>

   <!-- Bootstrap 5 CSS -->
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
   <!-- FontAwesome -->
   <script src="https://kit.fontawesome.com/554d524219.js" crossorigin="anonymous"></script>

   <!-- Estilos personalizados -->
   <link rel="shortcut icon" href="assets/favicon.png" type="image/x-icon">
   <link rel="stylesheet" href="css/custom-loader.css">
   <link rel="stylesheet" href="css/custom-fonts.css">
   <link rel="stylesheet" href="css/custom-navbar.css">
   <link rel="stylesheet" href="css/custom-contact-section.css">

   <!-- jQuery y Bootstrap JS -->
   <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

   <!-- Popper desde CDN -->
   <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.8/umd/popper.min.js" crossorigin="anonymous">
   </script>

   <!-- Custom Files JS -->
   <script src="js/custom-navbar.js"></script>

</head>

<body>

   <?php require_once 'custom-loader.php'?>

   <div class="page-container">
      <div class="content-wrap">
         <?php require_once 'home-content/navbar.php'?>

         <div class="container-fluid container-contact">
            <div class="row">
               <div class="col-md-6 d-flex flex-column justify-content-center align-items-center text-center">
                  <div class="messages-contact">
                     <p id="contact-pgf-1">Te decimos a dónde ir</p>
                     <p id="contact-pgf-2">y cómo no perderte en el intento</p>
                     <a href="https://wa.me/+525518180412" id="message-whatsapp" class="btn btn-primary">
                        <i class="fa-brands fa-whatsapp"></i> WhatsApp
                     </a>
                  </div>
               </div>
               <div class="col-md-6">
                  <img src="assets/contact/a_donde_ir_whatsapp.svg" id="airplane-contact" alt="Imagen de contacto">
               </div>
            </div>
         </div>

      </div>
   </div>

   <!-- Footer -->
   <footer class="bg-dark text-white text-center py-4 ">
      <p>© Turespacio <?php echo(date('Y')) ?></p>
   </footer>

   <script>
   $(() => {
      // Mostrar loader por si se oculta por CSS
      setTimeout(() => {
         $('#page-loader').fadeOut()
      }, 1500);
   })
   </script>

</body>

</html>