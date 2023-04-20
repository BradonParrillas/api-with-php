<?php

declare(strict_types=1);

// Cargamos automaticamente las clases
spl_autoload_register(function ($clase) {
  require __DIR__ . "/src/$clase.php";
});
//Definimos el manejador de errores
set_error_handler("ErrorHandler::handleError");
// Definimos que el controlador de error sera la clase ErrorHandler
set_exception_handler("ErrorHandler::handleException");

// Hacemos que la cabecera de respuesta indique que eniamos un J|sON
header("Content-type: application/; charset=UTF-8");

$uri = explode("/", $_SERVER["REQUEST_URI"]);

if ($uri[2] != "productos") {
  http_response_code(404);
  exit;
}

$id = $uri[3] ?? null;

$database = new Database("localhost", "php_rest_api", "php_user", "12345678");
$database->getConnection();

$gateway = new ProductoGateway($database);
$productoController = new ProductoController($gateway);

$productoController->procesarSolicitud($_SERVER["REQUEST_METHOD"], $id);
