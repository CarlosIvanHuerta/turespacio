<?php
#declare(strict_types=1);
#header('Content-Type: application/json');
ini_set('display_errors', 0); // No mostrar errores PHP al usuario 
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
require_once 'data-env.php';

class ConexionSQL
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

}