<?php
ini_set('display_errors', 0); // No mostrar errores PHP al usuario
error_reporting(E_ALL); // Registrar todos los errores
header("Content-Type: application/json");
require_once "access-db.php";

$db = new Database();

// Saber cuál metodo se va a llamar segun el stage recibido
if (isset($_GET["stage"])) {
   $stage = $_GET["stage"];

   try {

      switch ($stage) {
         case "topPostTravelTips":
            $tax_slut = isset($_GET["tax_slut"]) ? $_GET["tax_slut"] : null;
            $tax_name = isset($_GET["tax_name"]) ? $_GET["tax_name"] : null;
            $tax_id   = isset($_GET["tax_id"]) ? $_GET["tax_id"] : 1;
            $limit    = isset($_GET["limit"]) ? (int)$_GET["limit"] : 3;
            $result = $db->getTopTravelTips($tax_slut, $tax_name, $tax_id, $limit);
            echo json_encode($result);
            break;

         case "topPostMexico":
            $tax_slut = isset($_GET["tax_slut"]) ? $_GET["tax_slut"] : null;
            $tax_name = isset($_GET["tax_name"]) ? $_GET["tax_name"] : null;
            $tax_id   = isset($_GET["tax_id"]) ? $_GET["tax_id"] : 1;
            $limit    = isset($_GET["limit"]) ? (int)$_GET["limit"] : 5;
            $result = $db->getTopMexico($tax_slut, $tax_name, $tax_id, $limit);
            echo json_encode($result);
            break;

         case "topPostSliderMain":
            $tax_slut = isset($_GET["tax_slut"]) ? $_GET["tax_slut"] : null;
            $tax_name = isset($_GET["tax_name"]) ? $_GET["tax_name"] : null;
            $tax_id   = isset($_GET["tax_id"]) ? $_GET["tax_id"] : 1;
            $limit    = isset($_GET["limit"]) ? (int)$_GET["limit"] : 5;
            $result = $db->getPostSlidesMain();
            echo json_encode($result);
            break;

         case "categoryMexico":
            $tax_slut = isset($_GET["tax_slut"]) ? $_GET["tax_slut"] : null;
            $tax_name = isset($_GET["tax_name"]) ? $_GET["tax_name"] : null;
            $tax_id   = isset($_GET["tax_id"]) ? $_GET["tax_id"] : 1;
            $limit    = isset($_GET["limit"]) ? (int)$_GET["limit"] : 3;
            $result = $db->getPostCategoryMex($tax_slut, $tax_name, $tax_id, $limit);
            echo json_encode($result);
            break;

         case "categoryTipsTravel":
            $tax_slut = isset($_GET["tax_slut"]) ? $_GET["tax_slut"] : null;
            $tax_name = isset($_GET["tax_name"]) ? $_GET["tax_name"] : null;
            $tax_id   = isset($_GET["tax_id"]) ? $_GET["tax_id"] : 1;
            $limit    = isset($_GET["limit"]) ? (int)$_GET["limit"] : 3;
            $result = $db->getPostCategoryTipsTravel($tax_slut, $tax_name, $tax_id, $limit);
            echo json_encode($result);
            break;

         case "categoryWorld":
            $tax_slut = isset($_GET["tax_slut"]) ? $_GET["tax_slut"] : null;
            $tax_name = isset($_GET["tax_name"]) ? $_GET["tax_name"] : null;
            $tax_id   = isset($_GET["tax_id"]) ? $_GET["tax_id"] : 1;
            $limit    = isset($_GET["limit"]) ? (int)$_GET["limit"] : 3;
            $result = $db->getPostCategoryWorld($tax_slut, $tax_name, $tax_id, $limit);
            echo json_encode($result);
            break;

         case "categoryToDo":
            $tax_slut = isset($_GET["tax_slut"]) ? $_GET["tax_slut"] : null;
            $tax_name = isset($_GET["tax_name"]) ? $_GET["tax_name"] : null;
            $tax_id   = isset($_GET["tax_id"]) ? $_GET["tax_id"] : 1;
            $limit    = isset($_GET["limit"]) ? (int)$_GET["limit"] : 3;
            $result = $db->getPostCategoryToDo($tax_slut, $tax_name, $tax_id, $limit);
            echo json_encode($result);
            break;

         case "getNextPostByCategory":
            $tax_slut = isset($_GET["tax_slut"]) ? $_GET["tax_slut"] : null;
            $tax_name = isset($_GET["tax_name"]) ? $_GET["tax_name"] : null;
            $tax_id   = isset($_GET["tax_id"]) ? $_GET["tax_id"] : 1;
            $limit    = isset($_GET["limit"]) ? (int)$_GET["limit"] : 3;
            $offset   = isset($_GET["offset"]) ? (int)$_GET["offset"] : 6;
            $result = $db->getNextPostByCategory($tax_slut, $tax_name, $tax_id, $limit, $offset);
            echo json_encode($result);
            break;

         default:
            echo json_encode(["error" => "Acción no válida"]);
            break;
      }
      
   } catch (Exception $e) {
      // Envío de error como JSON para el fetch del front
      echo json_encode([
         'success' => false,
         'message' => $e->getMessage()
      ]);
   }

} else {
   echo json_encode(["error" => "No stage specified"]);
}
