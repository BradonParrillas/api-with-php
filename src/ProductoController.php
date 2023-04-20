<?php

class ProductoController
{
  public function __construct(private ProductoGateway $gateway)
  {
  }

  public function procesarSolicitud(string $method, ?string $id): void
  {
    if ($id) $this->atenderSolicitudPorId($method, $id);
    else  $this->atenderSolicitud($method);
  }

  private function atenderSolicitud($method): void
  {
    switch ($method) {
      case 'GET':
        echo json_encode($this->gateway->getAll());
        break;

      case 'POST':
        // $data = json_decode(file_get_contents("php://input"), true);
        $data = (array) $_POST;

        $errors = $this->getValidationErrors($data);

        if (!empty($errors)) {
          //Codigo para entidad no procesada
          http_response_code(422);
          echo json_encode($errors);
          break;
        }

        $id = $this->gateway->create($data);
        http_response_code(201);
        echo json_encode([
          "message" => "Product created successfully",
          "id" => $id
        ]);
        break;

      default:
        //Codigo para metodos no permitidos
        http_response_code(405);
        header("Allow: GET, POST");
        break;
    }
  }

  private function atenderSolicitudPorId(string $method, string $id)
  {
    $product = $this->gateway->getById($id);

    if (!$product) {
      http_response_code(404);
      echo json_encode([
        "message" => "Product not found",
        "id" => $id
      ]);
      return;
    }

    switch ($method) {
      case 'GET':

        echo json_encode($product);
        break;

      case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);

        $errors = $this->getValidationErrors($data, false);

        if (!empty($errors)) {
          //Codigo para entidad no procesada
          http_response_code(422);
          echo json_encode($errors);
          break;
        }

        $rows = $this->gateway->update($product, $data);
        http_response_code(200);
        echo json_encode([
          "message" => "Product $id updated successfully",
          "rows" => $rows
        ]);

        break;

      case 'DELETE':
        $rows = $this->gateway->delete($id);
        echo json_encode([
          "message" => "Product $id deleted successfully",
          "rows" => $rows
        ]);
        break;

      default:
        //Codigo para metodos no permitidos
        http_response_code(405);
        header("Allow: GET, PUT, DELETE");
        break;
    }
  }


  public function getValidationErrors(array $data, bool $is_new = true): array
  {
    $errors = [];

    if ($is_new && empty($data["name"])) {
      $errors["name"] = "name is required";
    }

    if (array_key_exists("size", $data)) {
      if (filter_var($data["size"], FILTER_VALIDATE_INT) === false) {
        $errors["size"] = "size must be a integer";
      }
    }

    return $errors;
  }
}
