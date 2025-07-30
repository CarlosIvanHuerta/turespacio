<?php ini_set('display_errors', 0); // No mostrar errores PHP al usuario
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="color-scheme" content="light dark">
  <title>Turespacio - Blog de Viajes</title>

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
   <link rel="stylesheet" href="css/custom-carrousel-card-post.css">

  <!-- jQuery y Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

   <!-- Popper desde CDN -->
   <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.8/umd/popper.min.js" crossorigin="anonymous"></script>

   <!-- NotifyJS -->
   <script src="https://cdn.jsdelivr.net/npm/notifyjs-browser@0.4.2/dist/notify.js"></script>

   <!-- Custom Files JS -->
   <script src="js/custom-navbar.js"></script>
   <!--<script src="js/custom-home.js"></script>-->
   <script src="js/call-functions-home.js"></script>
    <script src="js/call-publicidad.js"></script>

</head>

<body>

   <?php require_once 'custom-loader.php'?>

   <div class="page-container">
      <div class="content-wrap">

      <?php require_once 'home-content/navbar.php'?>

      <?php require_once 'home-content/carrusel-post.php'?>

      <?php require_once 'home-content/tips-viaje.php'?>

      <?php require_once 'home-content/mexico.php'?>

      <?php require_once 'home-content/newsletter.php'?>

      <?php require_once 'home-content/quienes-somos.php'?>

      <?php require_once 'home-content/contact.php'?>
      </div>
   </div>

   <!-- Footer -->
   <footer class="bg-dark text-white text-center py-4">
      <p>© Turespacio <?php echo(date('Y')) ?></p>
   </footer>

   <!-- Google tag (gtag.js) -->
   <script async src="https://www.googletagmanager.com/gtag/js?id=G-79W7XS67VC"></script>
   <script>
   window.dataLayer = window.dataLayer || [];
   function gtag(){dataLayer.push(arguments);}
   gtag('js', new Date());

   gtag('config', 'G-79W7XS67VC');
   </script>

</body>

</html>