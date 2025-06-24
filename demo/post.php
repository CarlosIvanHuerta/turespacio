<?php
// Conexión a base de datos
require_once 'conx/access-db.php';
$db = new Database();

// Obtener el slug desde la URL
$slug = $_GET['slug'] ?? null;

// Si no hay slug, redirigir o mostrar error
if (!$slug) {
   http_response_code(404);
   echo "Publicación no encontrada.";
   exit();
}

// Obtener los datos del post desde tu método
$post = $db->getPostBySlug($slug); // Este método debe retornar un array asociativo
if (!$post || !isset($post['post'][0]) || !is_array($post['post'][0])) {
   http_response_code(404);
   echo "Publicación no encontrada.";
   exit();
}
$postData = $post['post'][0];
?>
<!DOCTYPE html>
<html lang="es">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <title><?= htmlspecialchars($postData['post_title']) ?></title>

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

  <!-- jQuery y Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

   <!-- Popper desde CDN -->
   <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.8/umd/popper.min.js" crossorigin="anonymous"></script>

   <!-- Custom Files JS -->
   <script src="js/custom-navbar.js"></script>

   <style>
      body {
         font-family: 'Segoe UI', sans-serif;
         background-color: #f8f9fa;
      }

      .post-header {
         background-size: cover;
         background-position: center;
         height: 300px;
         border-radius: 0.75rem;
      }

      .post-title {
         font-size: 2.5rem;
         font-weight: bold;
      }

      .post-meta {
         font-size: 0.9rem;
         color: #6c757d;
      }

      .post-content img {
         max-width: 100%;
         height: auto;
         float: left;
         margin: 0 1rem 1rem 0;
         border-radius: 0.5rem;
         box-shadow: 0 0 10px rgba(0,0,0,0.1);
      }

      @media (max-width: 768px) {
      .post-content img {
         float: none;
         display: block;
         margin: 1rem auto;
      }
      }
      .share-buttons .btn {
         margin-right: 0.5rem;
      }
   </style>
</head>

<body>

   <?php require_once 'custom-loader.php'?>

   <div class="page-container">
      <div class="content-wrap">

         <?php require_once 'home-content/navbar.php'?>

         <div class="container" id="container-full-post" style="margin: 6% 4% 2%">

            <div style="display: flex;">

               <div style="width: 4%; display:flex; ">
                  <div style="background-color: #76b438;border-radius: 20px 0px 0px 20px;width:95%;display:flex;">
                     <div
                        style="background-color:#f8f9fa; border-radius: 20px 0px 0px 20px; width:90%; margin-left: auto; margin-right: 0;">
                     </div>
                  </div>
               </div>

               <div class="container-post-data" style="width: 90%;">
                  <!-- Título -->
                  <h1 class="post-title"><?= htmlspecialchars($postData['post_title']) ?></h1>

                  <!-- Imagen destacada -->
                  <div class="post-header mb-4"
                     style="background-image: url('<?= htmlspecialchars($postData['featured_image']) ?>');">
                  </div>

                  <!-- Meta -->
                  <div class="post-meta mb-3">
                     <i class="bi bi-person"></i> <?= htmlspecialchars($postData['author']) ?> ·
                     <i class="bi bi-calendar-event"></i> <?= date('d M Y', strtotime($postData['post_date'])) ?> ·
                     <i class="bi bi-folder2-open"></i> <?= htmlspecialchars($postData['categories']) ?>
                  </div>

                  <!-- Botones de compartir -->
                  <div class="mb-4 share-buttons">
                     <h6>Compartir este artículo:</h6>
                     <a class="btn btn-outline-primary btn-sm"
                        href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode('https://tusitio.com/' . $slug) ?>"
                        target="_blank">
                        <i class="bi bi-facebook"></i> Facebook
                     </a>
                     <a class="btn btn-outline-info btn-sm"
                        href="https://twitter.com/intent/tweet?url=<?= urlencode('https://tusitio.com/' . $slug) ?>&text=<?= urlencode($postData['post_title']) ?>"
                        target="_blank">
                        <i class="bi bi-twitter-x"></i> Twitter
                     </a>
                     <a class="btn btn-outline-success btn-sm"
                        href="https://wa.me/?text=<?= urlencode($postData['post_title'] . ' https://tusitio.com/' . $slug) ?>"
                        target="_blank">
                        <i class="bi bi-whatsapp"></i> WhatsApp
                     </a>
                     <!--<button class="btn btn-outline-secondary btn-sm" onclick="copyLink()">
                        <i class="bi bi-link-45deg"></i> Copiar enlace
                     </button>-->
                  </div>

                  <!-- Contenido del post -->
                  <div class="post-content">
                     <?= $postData['post_content'] ?>
                  </div>

               </div>
            </div>
         </div>

         <?php require_once 'home-content/contact.php'?>
      </div>
   </div>
   <!-- Footer -->
   <footer class="bg-dark text-white text-center py-4">
      <p>© Turespacio <?php echo(date('Y')) ?></p>
   </footer>

   <!-- Bootstrap JS y script de copiar enlace -->
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
   <script>
      window.addEventListener('load', function () {
         $('#page-loader').fadeOut()
      });
   </script>

</body>

</html>