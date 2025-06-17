<?php
declare(strict_types=1);
header('Content-Type: application/json');

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

         $postSliderArr = implode(',', array(47175, 47171, 47168, 47165, 47162)); // Convertir el array a una cadena separada por comas

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
               AND p.ID IN ($postSliderArr)
               AND t.term_id = 173 
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
}

#Usuario usmtcjiraflcy ha sido creado con contraseña osjg7wrdgskp PWD:: Su1t3Scr1pt@2025%
#private $host; //= "35.209.159.244:3306";
#private $db_name; //= "dbonxlzrrzzd3g";
#private $username; //= "usmtcjiraflcy";
#private $password; //= "Su1t3Scr1pt@2025%";
#private $charset; //= "utf8mb4";
