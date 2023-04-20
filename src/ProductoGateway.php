<?php

class ProductoGateway
{
  private PDO $connection;
  public function __construct(Database $database)
  {
    $this->connection = $database->getConnection();
  }

  public function getAll(): array
  {
    $sql = "SELECT * FROM products";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute();
    $data = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $row['is_available'] = (bool) $row['is_available'];
      $data[] = $row;
    }
    return $data;
  }

  public function create(array $data): array | false
  {
    $sql = "INSERT INTO  products (name, size, is_available)
            VALUES (:name, :size, :is_available)";

    // Preparar la consulta SQL y asignar los valores
    $stmt = $this->connection->prepare($sql);
    $stmt->bindParam(':name', $data['name'], PDO::PARAM_STR);
    $stmt->bindParam(':size', $data['size'], PDO::PARAM_INT);
    $stmt->bindParam(':is_available', $data['is_available'], PDO::PARAM_BOOL);
    $stmt->execute();

    // Devolver el ID de la última inserción
    return $this->connection->lastInsertId();
    //return $data;
  }

  public function getById(string $id): array | false
  {
    $sql = "SELECT * FROM products WHERE id = :id";
    $stmt = $this->connection->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($data !== false) {
      $data['is_available'] = (bool)$data['is_available'];
    }
    return $data;
  }

  public function update(array $current, array $new): int | false
  {
    $sql = "UPDATE products SET name = :name, size = :size, is_available = :is_available WHERE id = :id";

    //Al crear la clase modelo Product seria mas facil la validacion
    $stmt = $this->connection->prepare($sql);

    $name = $new['name'] ?? $current['name'];
    $size = $new['size'] ?? $current['size'];
    $is_available = $new['is_available'] ?? $current['is_available'];
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':size', $size, PDO::PARAM_INT);
    $stmt->bindParam(':is_available', $is_available, PDO::PARAM_BOOL);
    $stmt->bindParam(':id', $current['id'], PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->rowCount();
  }

  public function delete(string $id): int
  {
    $sql = "DELETE FROM products WHERE id = :id";
    $stmt = $this->connection->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->rowCount();
  }
}
