<?php

class ErrorHandler
{
  public static function handleException(Throwable $exception)
  {
    //Establecemos el estado http a 500 (error)
    http_response_code(500);
    echo json_encode([
      'error' => [
        'message' => $exception->getMessage(),
        'code' => $exception->getCode(),
        'file' => $exception->getFile(),
        'line' => $exception->getLine(),
        'trace' => $exception->getTraceAsString()
      ]
    ]);
  }

  //Convertimos los error que no se pueden manejar a excepciones (para que se pueda mandar en formato JSON)
  public static function handleError(
    int $errno,
    string $errstr,
    string $errfile,
    int $errline,
  ): bool {
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
  }
}
