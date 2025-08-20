<?php
ini_set('display_errors', 0); // No mostrar errores PHP al usuario
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
error_reporting(E_ALL); // Registrar todos los errores
date_default_timezone_set('America/Mexico_City');
header("Content-Type: application/json");

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

// Incluir los archivos necesarios
require '../libs/PHPMailer/src/Exception.php';
require '../libs/PHPMailer/src/PHPMailer.php';
require '../libs/PHPMailer/src/SMTP.php';
require_once 'data-env.php';

$mailHandler = new EmailHandler();

// Clase con método de envío de correo
class EmailHandler {
    private $mail_user;
    private $mail_pass;

    public function __construct()
    {
        // Cargar las variables de entorno desde el archivo .env
        cargarEntornoDesdeEnv(__DIR__ . '/.env');

        $this->mail_user = getenv('MAIL_USER');
        $this->mail_pass = getenv('MAIL_PASS');

        // Validación explícita
        if (! $this->mail_user || ! $this->mail_pass) {
            throw new Exception("Error: variables de entorno faltantes o mal definidas | Mail.");
        }
    }

    public function sendEmailContact(string $name, string $email, string $message): array
    {

        try {
            $startTime = microtime(true); // ⏱ Marca de inicio

            $mail = new PHPMailer(true);

            //$mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host     = 'smtp.gmail.com'; // o mail.tudominio.com si ya está apuntando
            $mail->SMTPAuth = true;
            $mail->Username   = $this->mail_user;
            $mail->Password   = $this->mail_pass;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // STARTTLS es el más común
                                                                //$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // STARTTLS es el más común
            $mail->Port    = 587;                               // SiteGround recomienda 587 con STARTTLS o 465 con SSL
            $mail->Timeout = 20;
            $mail->CharSet = 'UTF-8';

            // Datos del remitente y destinatario
            $mail->setFrom('info@turespacio.com', 'Turespacio');
            
            $asunto = 'Nueva Solicitud de Contacto 🌎';
            $mail->addAddress('info@turespacio.com', 'InfoTurespacio');

            // Contenido
            $mail->isHTML(true);
            $mail->Subject = $asunto;
            $mail->Body    = '
      <!doctype html>
      <html>

      <head>
      <meta name="viewport" content="width=device-width" />
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
      <title>Turespacio | Contacto</title>
      <style>
         img {
            border: none;
            -ms-interpolation-mode: bicubic;
            max-width: 100%;
         }

         body {
            background-color: #f6f6f6;
            font-family: sans-serif;
            -webkit-font-smoothing: antialiased;
            font-size: 14px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            -ms-text-size-adjust: 100%;
            -webkit-text-size-adjust: 100%;
         }

         table {
            border-collapse: separate;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
            width: 100%;
         }

         table td {
            font-family: sans-serif;
            font-size: 14px;
            vertical-align: top;
         }

         .body {
            background-color: #f6f6f6;
            width: 100%;
         }

         /* Set a max-width, and make it display as block so it will automatically stretch to that width, but will also shrink down on a phone or something */
         .container {
            display: block;
            margin: 0 auto !important;
            /* makes it centered */
            max-width: 580px;
            padding: 10px;
            width: 580px;
         }

         /* This should also be a block element, so that it will fill 100% of the .container */
         .content {
            box-sizing: border-box;
            display: block;
            margin: 0 auto;
            max-width: 580px;
            padding: 10px;
         }

         /* -------------------------------------
               HEADER, FOOTER, MAIN
            ------------------------------------- */
         .main {
            background: #ffffff;
            border-radius: 3px;
            width: 100%;
         }

         .wrapper {
            box-sizing: border-box;
            padding: 20px;
         }

         .content-block {
            padding-bottom: 10px;
            padding-top: 10px;
         }

         .footer {
            clear: both;
            margin-top: 10px;
            text-align: center;
            width: 100%;
         }

         .footer td,
         .footer p,
         .footer span,
         .footer a {
            color: #999999;
            font-size: 12px;
            text-align: center;
         }

         /* -------------------------------------
               TYPOGRAPHY
            ------------------------------------- */
         h1,
         h2,
         h3,
         h4 {
            color: #000000;
            font-family: sans-serif;
            font-weight: 400;
            line-height: 1.4;
            margin: 0;
            margin-bottom: 30px;
         }

         h1 {
            font-size: 35px;
            font-weight: 300;
            text-align: center;
            text-transform: capitalize;
         }

         p,
         ul,
         ol {
            font-family: sans-serif;
            font-size: 14px;
            font-weight: normal;
            margin: 0;
            margin-bottom: 15px;
         }

         p li,
         ul li,
         ol li {
            list-style-position: inside;
            margin-left: 5px;
         }

         a {
            color: #3498db;
            text-decoration: underline;
         }

         /* -------------------------------------
               BUTTONS
            ------------------------------------- */
         .btn {
            box-sizing: border-box;
            width: 100%;
         }

         .btn>tbody>tr>td {
            padding-bottom: 15px;
         }

         .btn table {
            width: auto;
         }

         .btn table td {
            background-color: #ffffff;
            border-radius: 5px;
            text-align: center;
         }

         .btn a {
            background-color: #ffffff;
            border: solid 1px #3498db;
            border-radius: 5px;
            box-sizing: border-box;
            color: #3498db;
            cursor: pointer;
            display: inline-block;
            font-size: 14px;
            font-weight: bold;
            margin: 0;
            padding: 12px 25px;
            text-decoration: none;
            text-transform: capitalize;
         }

         .btn-primary table td {
            background-color: #3498db;
         }

         .btn-primary a {
            background-color: #3498db;
            border-color: #3498db;
            color: #ffffff;
         }

         /* -------------------------------------
               OTHER STYLES THAT MIGHT BE USEFUL
            ------------------------------------- */
         .last {
            margin-bottom: 0;
         }

         .first {
            margin-top: 0;
         }

         .align-center {
            text-align: center;
         }

         .align-right {
            text-align: right;
         }

         .align-left {
            text-align: left;
         }

         .clear {
            clear: both;
         }

         .mt0 {
            margin-top: 0;
         }

         .mb0 {
            margin-bottom: 0;
         }

         .preheader {
            color: transparent;
            display: none;
            height: 0;
            max-height: 0;
            max-width: 0;
            opacity: 0;
            overflow: hidden;
            mso-hide: all;
            visibility: hidden;
            width: 0;
         }

         .powered-by a {
            text-decoration: none;
         }

         hr {
            border: 0;
            border-bottom: 1px solid #f6f6f6;
            margin: 20px 0;
         }

         /* -------------------------------------
               RESPONSIVE AND MOBILE FRIENDLY STYLES
            ------------------------------------- */
         @media only screen and (max-width: 620px) {
            table[class=body] h1 {
            font-size: 28px !important;
            margin-bottom: 10px !important;
            }

            table[class=body] p,
            table[class=body] ul,
            table[class=body] ol,
            table[class=body] td,
            table[class=body] span,
            table[class=body] a {
            font-size: 16px !important;
            }

            table[class=body] .wrapper,
            table[class=body] .article {
            padding: 10px !important;
            }

            table[class=body] .content {
            padding: 0 !important;
            }

            table[class=body] .container {
            padding: 0 !important;
            width: 100% !important;
            }

            table[class=body] .main {
            border-left-width: 0 !important;
            border-radius: 0 !important;
            border-right-width: 0 !important;
            }

            table[class=body] .btn table {
            width: 100% !important;
            }

            table[class=body] .btn a {
            width: 100% !important;
            }

            table[class=body] .img-responsive {
            height: auto !important;
            max-width: 100% !important;
            width: auto !important;
            }
         }

         /* -------------------------------------
               PRESERVE THESE STYLES IN THE HEAD
            ------------------------------------- */
         @media all {
            .ExternalClass {
            width: 100%;
            }

            .ExternalClass,
            .ExternalClass p,
            .ExternalClass span,
            .ExternalClass font,
            .ExternalClass td,
            .ExternalClass div {
            line-height: 100%;
            }

            .apple-link a {
            color: inherit !important;
            font-family: inherit !important;
            font-size: inherit !important;
            font-weight: inherit !important;
            line-height: inherit !important;
            text-decoration: none !important;
            }

            #MessageViewBody a {
            color: inherit;
            text-decoration: none;
            font-size: inherit;
            font-family: inherit;
            font-weight: inherit;
            line-height: inherit;
            }

            .btn-primary table td:hover {
            background-color: #34495e !important;
            }

            .btn-primary a:hover {
            background-color: #34495e !important;
            border-color: #34495e !important;
            }
         }
      </style>
      </head>

      <body class="">
      <span class="preheader">Tienes un mensaje nuevo desde Turespacio</span>
      <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
         <tr>
            <td>&nbsp;</td>
            <td class="container">
            <div class="content">

               <!-- START CENTERED WHITE CONTAINER -->
               <table role="presentation" class="main">

                  <!-- START MAIN CONTENT AREA -->
                  <tr>
                  <td class="wrapper">
                     <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                        <tr>
                        <td>
                           <p>Te dejamos los datos del mensaje registrado en el apartado de Contáctanos en Turespacio.</p>

                           <ul>
                              <li><b>Nombre</b>: <span style="color: #bbdee3">' . $name . '</span></li>
                              <li><b>Correo</b>: <a href="mailto:' . $email . '" style="color: #bbdee3">' . $email . '</a></li>
                              <li><b>Mensaje</b>: <span style="color: #bbdee3">' . $message . '</span></li>
                           </ul>

                        </td>
                        </tr>
                     </table>
                  </td>
                  </tr>

                  <!-- END MAIN CONTENT AREA -->
               </table>
               <!-- END CENTERED WHITE CONTAINER -->

               <!-- START FOOTER -->
               <div class="footer">
                  <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                  <tr>
                     <td class="content-block">
                        <span class="apple-link">&copy; Turespacio ' . date('Y') . '</span>
                     </td>
                  </table>
               </div>
               <!-- END FOOTER -->

            </div>
            </td>
            <td>&nbsp;</td>
         </tr>
      </table>
      </body>

      </html>
      ';

            $mail->send();
            $duration = round(microtime(true) - $startTime, 4);

            return [
                'success' => true,
                'timer'   => $duration
            ];

        } catch (PDOException $e) {
            return [
                'success' => false,
                'timer'   => 0,
                'message' => 'Error en la consulta sendEmailContact | ' . $e->getMessage(),
            ];
        }
    }
}

// Saber cuál metodo se va a llamar segun el stage recibido
if (isset($_GET["stage"])) {
   $stage = $_GET["stage"];

   try {

      switch ($stage) {
         case "sendEmailData":
            $name = isset($_GET["name"]) ? $_GET["name"] : null;
            $email = isset($_GET["email"]) ? $_GET["email"] : null;
            $message   = isset($_GET["message"]) ? $_GET["message"] : 1;
            $result = $mailHandler->sendEmailContact($name, $email, $message);
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
