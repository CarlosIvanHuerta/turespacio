<?php
http_response_code(404); // Asegura el código HTTP 404
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Página no encontrada - 404</title>
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- FontAwesome -->
  <script src="https://kit.fontawesome.com/554d524219.js" crossorigin="anonymous"></script>
  <style>
    body {
      background-color: #f8f9fa;
    }
    .error-container {
      height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-align: center;
    }
    .error-code {
      font-size: 8rem;
      font-weight: 700;
      color: #dc3545;
    }
    .error-message {
      font-size: 1.5rem;
      color: #6c757d;
    }
    .btn-home {
      margin-top: 1rem;
    }
  </style>
</head>
<body>

<div class="error-container">
  <div class="error-code">404</div>
  <div class="error-message">Lo sentimos, la publicación que buscas no fue encontrada.</div>
  <p class="text-muted">Es posible que el enlace esté roto o que la publicación haya sido eliminada.</p>
  <a href="/" class="btn btn-primary btn-home">Volver al inicio</a>
</div>

</body>
</html>
