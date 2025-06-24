<?php ini_set('display_errors', 0); // No mostrar errores PHP al usuario ?>
<!DOCTYPE html>
<html lang="es">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <meta name="color-scheme" content="light dark">
   <title>Turespacio - Tips de Viaje</title>

   <!-- Bootstrap 5 CSS -->
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
   <!-- FontAwesome -->
   <script src="https://kit.fontawesome.com/554d524219.js" crossorigin="anonymous"></script>

   <!-- Estilos personalizados -->
   <link rel="shortcut icon" href="assets/favicon.png" type="image/x-icon">
   <link rel="stylesheet" href="css/custom-loader.css">
   <link rel="stylesheet" href="css/custom-fonts.css">
   <link rel="stylesheet" href="css/custom-navbar.css">
   <link rel="stylesheet" href="css/custom-styles.css">
   <link rel="stylesheet" href="css/custom-category-post.css">

   <!-- jQuery y Bootstrap JS -->
   <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

   <!-- Popper desde CDN -->
   <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.8/umd/popper.min.js" crossorigin="anonymous">
   </script>

   <!-- Custom Files JS -->
   <script src="js/custom-navbar.js"></script>
   <script src="js/call-post-tips-viaje.js"></script>

</head>

<body>

   <?php require_once 'custom-loader.php'?>

   <div class="page-container">
      <div class="content-wrap">

         <?php require_once 'home-content/navbar.php'?>

         <div class="cover-category">
            <img src="assets/btips.jpg" class="img-cover-cat-tips">
         </div>

         <div class="container" id="container-post-cat-tips">
         </div>

         <div class="container text-center" id="container-load-more-mexico">
            <button type="button" onclick="getNextPostTipsTravel()" class="btn btn-primary" id="load-more-mexico">Cargar más</button>
         </div>

      </div>
   </div>

   <!-- Footer -->
   <footer class="bg-dark text-white text-center py-4 mt-5">
      <p>© Turespacio <?php echo(date('Y')) ?></p>
   </footer>

</body>

</html>