<?php

require_once __DIR__ .'/../helper/errors.php';
require_once __DIR__ . "/../utils.php";
require_once __DIR__ . "/../db/connector.php";

class CRUD
{
    private function jsonResponse($success, $data = null, $message = '', $count = 0) {
        return json_encode([
            'success' => $success,
            'data' => $data,
            'message' => $message,
            'count' => $count
        ]);
    }

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
                } else {
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

            $conn = null;
            return $this->jsonResponse(true, null, "Table `$tableName` created successfully.");
        } catch (Exception $e) {
            return $this->jsonResponse(false, null, "Error creating table: " . $e->getMessage());
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
                    // Fetch the inserted row
                    $inserted_row = $this->select($tableName, "*", ['id' => $inserted_id]);
                    $inserted_data = json_decode($inserted_row, true);
                    return $this->jsonResponse(true, $inserted_data['data'][0], "Record inserted successfully", 1);
                }
                return $this->jsonResponse(false, null, "Failed to insert record");
            }
            return $this->jsonResponse(false, null, "Database connection failed");
        } catch (Exception $e) {
            return $this->jsonResponse(false, null, $e->getMessage());
        }
    }

    function delete($tableName, $condition)
    {
        try {
            // Get the row before deletion
            $row_to_delete = $this->select($tableName, "*", $condition);
            $row_data = json_decode($row_to_delete, true);

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
            return $this->jsonResponse(
                true, 
                $row_data['data'][0], 
                "Record deleted successfully", 
                $affected_rows
            );
        } catch (Exception $e) {
            return $this->jsonResponse(false, null, $e->getMessage());
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

            // Fetch the updated row
            $updated_row = $this->select($tableName, "*", $condition);
            $updated_data = json_decode($updated_row, true);
            
            return $this->jsonResponse(
                true, 
                $updated_data['data'][0], 
                "Record updated successfully", 
                $affected_rows
            );
        } catch (Exception $e) {
            return $this->jsonResponse(false, null, $e->getMessage());
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
            return $this->jsonResponse(
                true, 
                $results, 
                "Records retrieved successfully", 
                count($results)
            );
        } catch (Exception $e) {
            return $this->jsonResponse(false, null, $e->getMessage());
        }
    }
}
/**
* 
*$crud = new CRUD();
*$response = json_decode($crud->insert('table_name', $data), true);
*if ($response['success']) {
*    // Access the inserted data through $response['data']
*}
 */