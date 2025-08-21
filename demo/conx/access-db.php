<?php
#declare(strict_types=1);
#header('Content-Type: application/json');
ini_set('display_errors', 0); // No mostrar errores PHP al usuario 
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
require_once 'data-env.php';

class Database
{
   private $host;
   private $db_name;
   private $username;
   private $password;
   private $charset;

   private $options = [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES => false,
   ];

   public $conn;

   public function __construct()
   {
      // Cargar las variables de entorno desde el archivo .env
      cargarEntornoDesdeEnv(__DIR__ . '/.env');

      $this->host     = getenv('DB_HOST');
      $this->db_name  = getenv('DB_NAME');
      $this->username = getenv('DB_USER');
      $this->password = getenv('DB_PASS');
      $this->charset  = getenv('DB_CHARSET');

      // Validación explícita
      if (!$this->host || !$this->db_name || !$this->username || !$this->password) {
         throw new Exception("Error: variables de entorno faltantes o mal definidas.");
      }

      $conn = $this->conectar();

      if (is_array($conn) && isset($conn['success']) && $conn['success'] === false) {
         // Lanza una excepción controlada
         throw new Exception($conn['message']);
      }
      $this->conn = $conn; // Asigna la conexión a la propiedad conn
   }

   public function conectar()
   {
      $this->conn = null;
      try {
         $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=" . $this->charset, $this->username, $this->password, $this->options);
      } catch (PDOException $exception) {
         return [
            "success" => false,
            "message" => "Error de conexión: " . $exception->getMessage(),
         ];
      }

      return $this->conn;
   }

   public function getTopTravelTips(string $tax_slut, string $tax_name, int $tax_id, int $limit): array {

      try {

         $startTime = microtime(true); // ⏱ Marca de inicio
         $conn = $this->conn;

         $limit = (is_numeric($limit) && $limit > 0 && $limit <= 100) ? (int)$limit : 6;

         // Query con placeholders
         $query = "
            SELECT 
               p.ID, 
               p.post_name,
               p.post_title, 
               p.post_date,
               p.post_content,
               t.slug,
               t.name,
               t.term_id,
               imguid.meta_value AS thumbnail_id,
               imgguid.guid AS thumbnail_url
            FROM wp_posts p
            INNER JOIN wp_term_relationships tr ON p.ID = tr.object_id
            INNER JOIN wp_term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
            INNER JOIN wp_terms t ON tt.term_id = t.term_id
            -- Join para obtener el thumbnail_id desde postmeta
            LEFT JOIN wp_postmeta imguid ON imguid.post_id = p.ID AND imguid.meta_key = '_thumbnail_id'
            -- Join para obtener la URL de la imagen desde wp_posts (attachment)
            LEFT JOIN wp_posts imgguid ON imgguid.ID = imguid.meta_value AND imgguid.post_type = 'attachment'
            WHERE p.post_status = 'publish'
               AND p.post_type = 'post'
               AND tt.taxonomy = 'category'
               AND (
                  t.slug = :slug
                  OR t.name = :name
                  OR t.term_id = :term_id
               )
            ORDER BY p.post_date DESC
            LIMIT $limit
         ";

         $stmt = $conn->prepare($query);
         $stmt->execute([
            ':slug' => $tax_slut,
            ':name' => $tax_name,
            ':term_id' => $tax_id
         ]);

         $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
         $rowCount = count($posts); // Cantidad de resultados
         $duration = round(microtime(true) - $startTime, 4);

         return[
            'success' => true,
            'counter' => $rowCount,
            'timer' => $duration,
            'post' => $posts
         ];
            
      } catch (PDOException $e) {
         return [
            'success' => false,
            'counter' => 0,
            'timer' => 0,
            'data' => [],
            'message' => 'Error en la consulta getTopTravelTips | ' . $e->getMessage()
         ];
      }
   }

   public function getTopMexico(string $tax_slut, string $tax_name, int $tax_id, int $limit): array {

      try {

         $startTime = microtime(true); // ⏱ Marca de inicio
         $conn = $this->conn;

         $limit = (is_numeric($limit) && $limit > 0 && $limit <= 100) ? (int)$limit : 6;
         
         // Query con placeholders
         $query = "
            SELECT 
               p.ID, 
               p.post_name,
               p.post_title, 
               p.post_date,
               p.post_content,
               t.slug,
               t.name,
               t.term_id,
               imguid.meta_value AS thumbnail_id,
               imgguid.guid AS thumbnail_url
            FROM wp_posts p
            INNER JOIN wp_term_relationships tr ON p.ID = tr.object_id
            INNER JOIN wp_term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
            INNER JOIN wp_terms t ON tt.term_id = t.term_id
            LEFT JOIN wp_postmeta imguid ON imguid.post_id = p.ID AND imguid.meta_key = '_thumbnail_id'
            LEFT JOIN wp_posts imgguid ON imgguid.ID = imguid.meta_value AND imgguid.post_type = 'attachment'
            WHERE p.post_status = 'publish'
               AND p.post_type = 'post'
               AND tt.taxonomy = 'category'
               AND (
                  t.slug = :slug
                  OR t.name = :name
                  OR t.term_id = :term_id
               )
            ORDER BY p.post_date DESC
            LIMIT $limit
            ";

         $stmt = $conn->prepare($query);
         $stmt->execute([
            ':slug' => $tax_slut,
            ':name' => $tax_name,
            ':term_id' => $tax_id
         ]);

         $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
         $rowCount = count($posts); // Cantidad de resultados
         $duration = round(microtime(true) - $startTime, 4);

         return[
            'success' => true,
            'counter' => $rowCount,
            'timer' => $duration,
            'post' => $posts
         ];
            
      } catch (PDOException $e) {
         return [
            'success' => false,
            'counter' => 0,
            'timer' => 0,
            'data' => [],
            'message' => 'Error en la consulta getTopMexico | ' . $e->getMessage()
         ];
      }
   }

   public function getPostSlidesMain(): array {

      try {

         $startTime = microtime(true); // ⏱ Marca de inicio
         $conn = $this->conn;

         #$postSliderArr = implode(',', array(47175, 47171, 47168, 47165, 47162)); // Convertir el array a una cadena separada por comas

         // Query con placeholders
         /*
         $query = "
            SELECT 
               p.ID, 
               p.post_name,
               p.post_title, 
               p.post_date,
               p.post_content,
               t.slug,
               t.name,
               t.term_id,
               imguid.meta_value AS thumbnail_id,
               imgguid.guid AS thumbnail_url
            FROM wp_posts p
            INNER JOIN wp_term_relationships tr ON p.ID = tr.object_id
            INNER JOIN wp_term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
            INNER JOIN wp_terms t ON tt.term_id = t.term_id
            LEFT JOIN wp_postmeta imguid ON imguid.post_id = p.ID AND imguid.meta_key = '_thumbnail_id'
            LEFT JOIN wp_posts imgguid ON imgguid.ID = imguid.meta_value AND imgguid.post_type = 'attachment'
            WHERE p.post_status = 'publish'
               AND p.post_type = 'post'
               AND tt.taxonomy = 'category'
               AND p.ID IN ($postSliderArr)
               AND t.term_id = 173 
            ORDER BY p.post_date DESC
         ";
         */

         $query = "
            SELECT 
               p.ID, 
               p.post_name,
               p.post_title, 
               p.post_date,
               p.post_content,
               t.slug,
               t.name,
               t.term_id,
               imguid.meta_value AS thumbnail_id,
               imgguid.guid AS thumbnail_url
            FROM wp_posts p
            INNER JOIN wp_term_relationships tr ON p.ID = tr.object_id
            INNER JOIN wp_term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
            INNER JOIN wp_terms t ON tt.term_id = t.term_id
            LEFT JOIN wp_postmeta imguid ON imguid.post_id = p.ID AND imguid.meta_key = '_thumbnail_id'
            LEFT JOIN wp_posts imgguid ON imgguid.ID = imguid.meta_value AND imgguid.post_type = 'attachment'
            WHERE p.post_status = 'publish'
               AND p.post_type = 'post'
               AND p.load_carousel = 1
               AND tt.taxonomy = 'category'
            GROUP BY p.ID
            ORDER BY p.post_date DESC
         ";

         $stmt = $conn->prepare($query);
         $stmt->execute();
         $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

         $rowCount = count($posts); // Cantidad de resultados
         $duration = round(microtime(true) - $startTime, 4);

         return[
            'success' => true,
            'counter' => $rowCount,
            'timer' => $duration,
            'post' => $posts
         ];
            
      } catch (PDOException $e) {
         return [
            'success' => false,
            'counter' => 0,
            'timer' => 0,
            'data' => [],
            'message' => 'Error en la consulta getPostSlidesMain | ' . $e->getMessage()
         ];
      }
   }

   public function getPostBySlug($slut): array {

      try {

         $startTime = microtime(true); // ⏱ Marca de inicio
         $conn = $this->conn;

         // Query con placeholders
         $query = "
            SELECT 
            p.ID,
            p.post_title,
            p.post_content,
            p.post_date,
            u.display_name AS author,
            GROUP_CONCAT(t.name SEPARATOR ', ') AS categories,
            img.guid AS featured_image
            FROM wp_posts p
            -- Autor
            JOIN wp_users u ON p.post_author = u.ID
            -- Relación con taxonomías
            LEFT JOIN wp_term_relationships tr ON p.ID = tr.object_id
            LEFT JOIN wp_term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
            LEFT JOIN wp_terms t ON tt.term_id = t.term_id AND tt.taxonomy = 'category'
            -- Imagen destacada
            LEFT JOIN wp_postmeta pm ON pm.post_id = p.ID AND pm.meta_key = '_thumbnail_id'
            LEFT JOIN wp_posts img ON img.ID = pm.meta_value
            WHERE p.post_status = 'publish'
            AND p.post_type = 'post'
            AND p.post_name = :slut
            GROUP BY p.ID
            LIMIT 1;
         ";

         $stmt = $conn->prepare($query);
         $stmt->execute([$slut]);
         $post = $stmt->fetchAll(PDO::FETCH_ASSOC);

         $rowCount = count($post); // Cantidad de resultados
         $duration = round(microtime(true) - $startTime, 4);

         return[
            'success' => true,
            'counter' => $rowCount,
            'timer' => $duration,
            'post' => $post
         ];

      } catch (PDOException $e) {
         return [
            'success' => false,
            'counter' => 0,
            'timer' => 0,
            'data' => [],
            'message' => 'Error en la consulta getPostSlut | ' . $e->getMessage()
         ];
      }
   }

   // Post x Página Categoría
   public function getPostCategoryMex(string $tax_slut, string $tax_name, int $tax_id, int $limit): array {

      try {

         $startTime = microtime(true); // ⏱ Marca de inicio
         $conn = $this->conn;

         $limit = (is_numeric($limit) && $limit > 0 && $limit <= 100) ? (int)$limit : 6;

         // Query con placeholders
         $query = "
            SELECT 
               p.ID, 
               p.post_name,
               p.post_title, 
               p.post_date,
               p.post_content,
               t.slug,
               t.name,
               t.term_id,
               imguid.meta_value AS thumbnail_id,
               imgguid.guid AS thumbnail_url
            FROM wp_posts p
            INNER JOIN wp_term_relationships tr ON p.ID = tr.object_id
            INNER JOIN wp_term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
            INNER JOIN wp_terms t ON tt.term_id = t.term_id
            LEFT JOIN wp_postmeta imguid ON imguid.post_id = p.ID AND imguid.meta_key = '_thumbnail_id'
            LEFT JOIN wp_posts imgguid ON imgguid.ID = imguid.meta_value AND imgguid.post_type = 'attachment'
            WHERE p.post_status = 'publish'
               AND p.post_type = 'post'
               AND tt.taxonomy = 'category'
               AND (
                  t.slug = :slug
                  OR t.name = :name
                  OR t.term_id = :term_id
               )
            ORDER BY p.post_date DESC
            LIMIT $limit
            ";

         $stmt = $conn->prepare($query);
         $stmt->execute([
            ':slug' => $tax_slut,
            ':name' => $tax_name,
            ':term_id' => $tax_id
         ]);

         $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
         $rowCount = count($posts); // Cantidad de resultados
         $duration = round(microtime(true) - $startTime, 4);

         return[
            'success' => true,
            'counter' => $rowCount,
            'timer' => $duration,
            'post' => $posts
         ];

      } catch (PDOException $e) {
         return [
            'success' => false,
            'counter' => 0,
            'timer' => 0,
            'data' => [],
            'message' => 'Error en la consulta getPostCategoryMex | ' . $e->getMessage()
         ];
      }
   }

   public function getPostCategoryTipsTravel(string $tax_slut, string $tax_name, int $tax_id, int $limit): array {

      try {

         $startTime = microtime(true); // ⏱ Marca de inicio
         $conn = $this->conn;

         $limit = (is_numeric($limit) && $limit > 0 && $limit <= 100) ? (int)$limit : 6;

         // Query con placeholders
         $query = "
            SELECT 
               p.ID, 
               p.post_name,
               p.post_title, 
               p.post_date,
               p.post_content,
               t.slug,
               t.name,
               t.term_id,
               imguid.meta_value AS thumbnail_id,
               imgguid.guid AS thumbnail_url
            FROM wp_posts p
            INNER JOIN wp_term_relationships tr ON p.ID = tr.object_id
            INNER JOIN wp_term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
            INNER JOIN wp_terms t ON tt.term_id = t.term_id
            LEFT JOIN wp_postmeta imguid ON imguid.post_id = p.ID AND imguid.meta_key = '_thumbnail_id'
            LEFT JOIN wp_posts imgguid ON imgguid.ID = imguid.meta_value AND imgguid.post_type = 'attachment'
            WHERE p.post_status = 'publish'
               AND p.post_type = 'post'
               AND tt.taxonomy = 'category'
               AND (
                  t.slug = :slug
                  OR t.name = :name
                  OR t.term_id = :term_id
               )
            ORDER BY p.post_date DESC
            LIMIT $limit
            ";

         $stmt = $conn->prepare($query);
         $stmt->execute([
            ':slug' => $tax_slut,
            ':name' => $tax_name,
            ':term_id' => $tax_id
         ]);

         $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
         $rowCount = count($posts); // Cantidad de resultados
         $duration = round(microtime(true) - $startTime, 4);

         return[
            'success' => true,
            'counter' => $rowCount,
            'timer' => $duration,
            'post' => $posts
         ];

      } catch (PDOException $e) {
         return [
            'success' => false,
            'counter' => 0,
            'timer' => 0,
            'data' => [],
            'message' => 'Error en la consulta getPostCategoryMex | ' . $e->getMessage()
         ];
      }
   }

   public function getPostCategoryWorld(string $tax_slut, string $tax_name, int $tax_id, int $limit): array {

      try {

         $startTime = microtime(true); // ⏱ Marca de inicio
         $conn = $this->conn;

         $limit = (is_numeric($limit) && $limit > 0 && $limit <= 100) ? (int)$limit : 6;

         // Query con placeholders
         $query = "
            SELECT 
               p.ID, 
               p.post_name,
               p.post_title, 
               p.post_date,
               p.post_content,
               t.slug,
               t.name,
               t.term_id,
               imguid.meta_value AS thumbnail_id,
               imgguid.guid AS thumbnail_url
            FROM wp_posts p
            INNER JOIN wp_term_relationships tr ON p.ID = tr.object_id
            INNER JOIN wp_term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
            INNER JOIN wp_terms t ON tt.term_id = t.term_id
            LEFT JOIN wp_postmeta imguid ON imguid.post_id = p.ID AND imguid.meta_key = '_thumbnail_id'
            LEFT JOIN wp_posts imgguid ON imgguid.ID = imguid.meta_value AND imgguid.post_type = 'attachment'
            WHERE p.post_status = 'publish'
               AND p.post_type = 'post'
               AND tt.taxonomy = 'category'
               AND (
                  t.slug = :slug
                  OR t.name = :name
                  OR t.term_id = :term_id
               )
            ORDER BY p.post_date DESC
            LIMIT $limit
            ";

         $stmt = $conn->prepare($query);
         $stmt->execute([
            ':slug' => $tax_slut,
            ':name' => $tax_name,
            ':term_id' => $tax_id
         ]);

         $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
         $rowCount = count($posts); // Cantidad de resultados
         $duration = round(microtime(true) - $startTime, 4);

         return[
            'success' => true,
            'counter' => $rowCount,
            'timer' => $duration,
            'post' => $posts
         ];

      } catch (PDOException $e) {
         return [
            'success' => false,
            'counter' => 0,
            'timer' => 0,
            'data' => [],
            'message' => 'Error en la consulta getPostCategoryMex | ' . $e->getMessage()
         ];
      }
   }

   public function getPostCategoryToDo(string $tax_slut, string $tax_name, int $tax_id, int $limit): array {

      try {

         $startTime = microtime(true); // ⏱ Marca de inicio
         $conn = $this->conn;

         $limit = (is_numeric($limit) && $limit > 0 && $limit <= 100) ? (int)$limit : 6;

         // Query con placeholders
         $query = "
            SELECT 
               p.ID, 
               p.post_name,
               p.post_title, 
               p.post_date,
               p.post_content,
               t.slug,
               t.name,
               t.term_id,
               imguid.meta_value AS thumbnail_id,
               imgguid.guid AS thumbnail_url
            FROM wp_posts p
            INNER JOIN wp_term_relationships tr ON p.ID = tr.object_id
            INNER JOIN wp_term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
            INNER JOIN wp_terms t ON tt.term_id = t.term_id
            LEFT JOIN wp_postmeta imguid ON imguid.post_id = p.ID AND imguid.meta_key = '_thumbnail_id'
            LEFT JOIN wp_posts imgguid ON imgguid.ID = imguid.meta_value AND imgguid.post_type = 'attachment'
            WHERE p.post_status = 'publish'
               AND p.post_type = 'post'
               AND tt.taxonomy = 'category'
               AND (
                  t.slug = :slug
                  OR t.name = :name
                  OR t.term_id = :term_id
               )
            ORDER BY p.post_date DESC
            LIMIT $limit
            ";

         $stmt = $conn->prepare($query);
         $stmt->execute([
            ':slug' => $tax_slut,
            ':name' => $tax_name,
            ':term_id' => $tax_id
         ]);

         $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
         $rowCount = count($posts); // Cantidad de resultados
         $duration = round(microtime(true) - $startTime, 4);

         return[
            'success' => true,
            'counter' => $rowCount,
            'timer' => $duration,
            'post' => $posts
         ];

      } catch (PDOException $e) {
         return [
            'success' => false,
            'counter' => 0,
            'timer' => 0,
            'data' => [],
            'message' => 'Error en la consulta getPostCategoryMex | ' . $e->getMessage()
         ];
      }
   }

   public function getNextPostByCategory(string $tax_slut, string $tax_name, int $tax_id, int $limit, int $offset): array {

      try {

         $startTime = microtime(true); // ⏱ Marca de inicio
         $conn = $this->conn;

         $limit = (is_numeric($limit) && $limit > 0 && $limit <= 100) ? (int)$limit : 6;
         $offset = (is_numeric($offset) && $offset >= 0) ? (int)$offset : 6;

         // Query con placeholders
         $query = "
            SELECT 
               p.ID, 
               p.post_name,
               p.post_title, 
               p.post_date,
               p.post_content,
               t.slug,
               t.name,
               t.term_id,
               imguid.meta_value AS thumbnail_id,
               imgguid.guid AS thumbnail_url
            FROM wp_posts p
            INNER JOIN wp_term_relationships tr ON p.ID = tr.object_id
            INNER JOIN wp_term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
            INNER JOIN wp_terms t ON tt.term_id = t.term_id
            LEFT JOIN wp_postmeta imguid ON imguid.post_id = p.ID AND imguid.meta_key = '_thumbnail_id'
            LEFT JOIN wp_posts imgguid ON imgguid.ID = imguid.meta_value AND imgguid.post_type = 'attachment'
            WHERE p.post_status = 'publish'
               AND p.post_type = 'post'
               AND tt.taxonomy = 'category'
               AND (
                  t.slug = :slug
                  OR t.name = :name
                  OR t.term_id = :term_id
               )
            ORDER BY p.post_date DESC
            LIMIT $limit
            OFFSET $offset
            ";

         $stmt = $conn->prepare($query);
         $stmt->execute([
            ':slug' => $tax_slut,
            ':name' => $tax_name,
            ':term_id' => $tax_id
         ]);

         $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
         $rowCount = count($posts); // Cantidad de resultados
         $duration = round(microtime(true) - $startTime, 4);

         return[
            'success' => true,
            'counter' => $rowCount,
            'timer' => $duration,
            'post' => $posts
         ];

      } catch (PDOException $e) {
         return [
            'success' => false,
            'counter' => 0,
            'timer' => 0,
            'data' => [],
            'message' => 'Error en la consulta getNextPostByCategory | ' . $e->getMessage()
         ];
      }
   }

   public function getPublicidad(): array {

      try {

         $conn = $this->conn;

         $limit = 6;

         // Query con placeholders
         $query = "
            SELECT 
               *
            FROM publicidad p        
            WHERE p.estatus = 1
            LIMIT $limit
            ";

         $stmt = $conn->prepare($query);
         $stmt->execute();
         $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
         $rowCount = count($posts); // Cantidad de resultados
          if ($rowCount == 0){
              $rowCount = 1;
              $posts = [
                  [
                      'id' => 0,
                      'url_externa' => 'Sin publicidad',
                      'path_img' => 'https://admintr.marianay.sg-host.com/api/uploads/images/banners/PRINCIPAL.jpg',
                      'text_important' => 'Aunciate con nostros',
                  ]
                  ]
              ;
          }
         return[
            'success' => true,
            'counter' => $rowCount,
            'post' => $posts
         ];

      } catch (PDOException $e) {
         return [
            'success' => false,
            'counter' => 0,
            'timer' => 0,
            'data' => [],
            'message' => 'Error en la consulta getNextPostByCategory | ' . $e->getMessage()
         ];
      }
   }

   public function getPublicidad(): array {

      try {

         $conn = $this->conn;

         $limit = 6;

         // Query con placeholders
         $query = "
            SELECT 
               *
            FROM publicidad p        
            WHERE p.estatus = 1
            LIMIT $limit
            ";

         $stmt = $conn->prepare($query);
         $stmt->execute();
         $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
         $rowCount = count($posts); // Cantidad de resultados
          if ($rowCount == 0){
              $rowCount = 1;
              $posts =$posts = [
                  [
                      'id' => 0,
                      'url_externa' => 'Sin publicidad',
                      'path_img' => 'https://admintr.marianay.sg-host.com/api/uploads/images/banners/PRINCIPAL.jpg',
                      'text_important' => 'Aunciate con nostros',
                  ]
                  ]
              ;
          }
         return[
            'success' => true,
            'counter' => $rowCount,
            'post' => $posts
         ];

      } catch (PDOException $e) {
         return [
            'success' => false,
            'counter' => 0,
            'timer' => 0,
            'data' => [],
            'message' => 'Error en la consulta getNextPostByCategory | ' . $e->getMessage()
         ];
      }
   }

   public function registrarClickPublicidad($idPublicidad){
    try {
        $sql = "UPDATE publicidad SET clicks_publicidad = clicks_publicidad + 1 WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $idPublicidad, PDO::PARAM_INT);
        return $stmt->execute(); // true si se ejecuta correctamente
    } catch (PDOException $e) {
        error_log("Error al registrar clic: " . $e->getMessage());
        return false;
    }
   }
}

#Usuario usmtcjiraflcy ha sido creado con contraseña osjg7wrdgskp PWD:: Su1t3Scr1pt@2025%
#private $host; //= "35.209.159.244:3306";
#private $db_name; //= "dbonxlzrrzzd3g";
#private $username; //= "usmtcjiraflcy";
#private $password; //= "Su1t3Scr1pt@2025%";
#private $charset; //= "utf8mb4";
