<?php

require_once __DIR__ .'/../helper/errors.php';
require_once __DIR__ . "/../utils.php";
require_once __DIR__ . "/../model/connector.php";

// use PDO;
// use Exception;

class CRUD
{
  public function createTable($tableName, $columns)
  {
    try {
      $conn = SQLConnector::connectDatabase();
      if (!$conn) {
        throw new Exception("Failed to connect to the database.");
      }

      $columnsDefinition = [];
      $constraints = [];

      foreach ($columns as $columnName => $dataType) {
        if ($columnName === '') {
          $constraints[] = $dataType;
        } elseif (
          str_starts_with($columnName, 'CONSTRAINT') ||
          str_starts_with($dataType, 'FOREIGN KEY')
        ) {
          $constraints[] = $dataType;
        }
        // Regular columns
        else {
          $columnsDefinition[] = "`$columnName` $dataType";
        }
      }

      $createQuery = "CREATE TABLE IF NOT EXISTS `$tableName` (" .
      implode(", ", $columnsDefinition);

      if (!empty($constraints)) {
        $createQuery .= ", " . implode(", ", $constraints);
      }

      $createQuery .= ");";

      $stmt = $conn->prepare($createQuery);
      $stmt->execute();

      echo "Table `$tableName` created successfully.\n";
      $conn = null;
      return true;
    } catch (Exception $e) {
      echo "Error creating table: " . $e->getMessage() . "\n";
      return false;
    }
  }
  public function insert($tableName, $data)
  {
    try {
      $conn = SQLConnector::connectDatabase();
      if ($conn) {
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');

        $insert_query = "INSERT INTO `$tableName` (" . implode(", ", $columns) . ")
                             VALUES (" . implode(", ", $placeholders) . ");";

        $stmt = $conn->prepare($insert_query);
        $res = $stmt->execute(array_values($data));

        if ($res) {
          $inserted_id = $conn->lastInsertId();
          echo ("Inserted successfully, ID: {$inserted_id}");
          return $inserted_id;
        }

        $conn = null;
      }
      return true;
    } catch (Exception $e) {
      echo $e->getMessage();
      return false;
    }
  }

  function delete($tableName, $condition)
  {
    try {
      $conn = SQLConnector::connectDatabase();
      if (!$conn) {
        throw new Exception("Failed to connect to the database.");
      }

      if (empty($condition)) {
        throw new Exception("Condition is required for DELETE operation.");
      }

      $whereConditions = [];
      foreach ($condition as $column => $value) {
        $whereConditions[] = "`$column` = :where_$column";
      }
      $whereClause = "WHERE " . implode(" AND ", $whereConditions);

      $delete_query = "DELETE FROM `$tableName` $whereClause;";
      $stmt = $conn->prepare($delete_query);

      foreach ($condition as $column => $value) {
        $stmt->bindValue(":where_$column", $value);
      }

      $stmt->execute();
      $affected_rows = $stmt->rowCount();

      $conn = null;
      return $affected_rows;
    } catch (Exception $e) {
      throw $e;
    }
  }

  function update($tableName, $data, $condition)
  {
    try {
      $conn = SQLConnector::connectDatabase();
      if (!$conn) {
        throw new Exception("Failed to connect to the database.");
      }

      $setClause = [];
      foreach ($data as $column => $value) {
        $setClause[] = "`$column` = :$column";
      }
      $setClause = implode(", ", $setClause);

      $whereClause = [];
      foreach ($condition as $column => $value) {
        $whereClause[] = "`$column` = :where_$column";
      }
      $whereClause = implode(" AND ", $whereClause);

      $update_query = "UPDATE `$tableName` SET $setClause WHERE $whereClause;";
      $stmt = $conn->prepare($update_query);

      foreach ($data as $column => $value) {
        $stmt->bindValue(":$column", $value);
      }

      foreach ($condition as $column => $value) {
        $stmt->bindValue(":where_$column", $value);
      }

      $stmt->execute();
      $affected_rows = $stmt->rowCount();

      return $affected_rows;
    } catch (Exception $e) {
      throw $e;
    }
  }

  function select($tableName, $columns = "*", $condition = [], $orderBy = "", $limit = null, $offset = null)
  {
    try {
      $conn = SQLConnector::connectDatabase();
      if (!$conn) {
        throw new Exception("Failed to connect to the database.");
      }

      $selectClause = is_array($columns) ? implode(", ", $columns) : $columns;

      $whereClause = "";
      if (!empty($condition)) {
        $whereConditions = [];
        foreach ($condition as $column => $value) {
          $whereConditions[] = "`$column` = :where_$column";
        }
        $whereClause = "WHERE " . implode(" AND ", $whereConditions);
      }

      $orderByClause = $orderBy ? "ORDER BY $orderBy" : "";

      $limitClause = $limit !== null ? "LIMIT $limit" : "";
      $offsetClause = $offset !== null ? "OFFSET $offset" : "";

      $select_query = "SELECT $selectClause FROM `$tableName` $whereClause $orderByClause $limitClause $offsetClause;";

      $stmt = $conn->prepare($select_query);

      if (!empty($condition)) {
        foreach ($condition as $column => $value) {
          $stmt->bindValue(":where_$column", $value);
        }
      }

      $stmt->execute();

      $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

      $conn = null;

      return $results;
    } catch (Exception $e) {
      error_log($e->getMessage());
      return false;
    }
  }
}
