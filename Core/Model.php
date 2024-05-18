<?php

namespace Domnyus;

use Domnyus\Constants;
use Exception;
use PDO;

class Model
{
    protected string $__table_name__ = "";
    protected ?int $___total_count;
    protected PDO $___pdo___;
    protected ?array $___not_columns___ = [
        "__table_name__",
        "___pdo___",
        "___total_count",
        "___not_columns___"
    ];

    public function __construct(?string $__table_name__ = null)
    {
        $this->__table_name__ = $__table_name__;
        $this->___pdo___ = new PDO("mysql:host=" . Constants::DBHOST . ";dbname=" . Constants::DBNAME . "", Constants::DBUSER, Constants::DBPASS);
        $this->___pdo___->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function ___get_total_count(): ?int
    {
        return $this->___total_count;
    }

    public function model_to_array(): array
    {
        $array = [];
        foreach (get_object_vars($this) as $key => $value) {
            if (in_array($key, $this->___not_columns___)) {
                continue;
            }
            $array[$key] = $value;
        }

        return $array;
    }

    public function parser(?bool $light_version = false): array
    {
        return [];
    }

    public function array_to_model(array $data): Model
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
        return $this;
    }

    public function insert()
    {
        $columns = "";
        $params = "";
        $array_model = $this->model_to_array();
        $values = [];

        foreach ($array_model as $key => $value) {
            if (strtolower($key) === "id") {
                continue;
            }

            if (!empty($columns)) {
                $columns .= ", ";
                $params .= ", ";
            }

            $columns .= "`$key`";
            $params .= ":$key";
            $values[":$key"] = $value;
        }

        $sql = "INSERT INTO `$this->__table_name__` ($columns) VALUES ($params)";
        $statement = $this->___pdo___->prepare($sql);

        foreach ($values as $param => &$val) {
            $statement->bindParam($param, $val);
        }

        $statement->execute();

        $this->find_by_id($this->___pdo___->lastInsertId());
        return $this;
    }

    public function select(
        array $where = null,
        $order_by = null,
        $limit = null,
        $offset = null,
        $group_by = null,
        $join = null,
        array $columns = null,
        bool $use_parser = false
    ): array {
        $sql = "SELECT";

        if (isset($columns)) {
            $sql .= " " . implode(", ", $columns);
        } else {
            $sql .= " *";
        }

        $sql .= " FROM $this->__table_name__";

        if (isset($where)) {
            $sql .= " WHERE";
            $conditions = [];

            foreach ($where as $key => $value) {
                $conditions[] = "$key = :$key";
            }

            $sql .= " " . implode(" AND ", $conditions);
        }

        if (isset($order_by)) {
            $sql .= " ORDER BY $order_by";
        }

        if (isset($group_by)) {
            $sql .= " GROUP BY $group_by";
        }

        if (isset($join)) {
            $sql .= " $join";
        }

        if (isset($offset) && isset($limit)) {
            $sql_count = $sql;
            $sql .= " LIMIT $limit OFFSET $offset";
        }

        $statement = $this->___pdo___->prepare($sql);

        if (isset($where)) {
            foreach ($where as $key => $value) {
                $statement->bindParam(":$key", $value);
            }
        }

        $statement->execute();

        $data = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (isset($sql_count)) {
            $statement = $this->___pdo___->prepare($sql_count);
            $statement->execute();
            $this->___total_count = $statement->rowCount();
        }

        $parsed_data = [];
        if ($use_parser) {
            foreach ($data as $value) {
                $this->array_to_model($value);
                $parsed_data[] = $this->parser($use_parser);
            }
        } else {
            foreach ($data as $value) {
                $this->array_to_model($value);
                $parsed_data[] = $this;
            }
        }
        $data = $parsed_data;

        return $data;
    }


    public function update(string $where = null): bool
    {
        $update_columns = $this->model_to_array();

        $sql = "UPDATE $this->__table_name__ SET ";
        $update_values = [];

        foreach ($update_columns as $column => $value) {
            if (strtolower($column) === "id") {
                continue;
            }
            $sql .= "$column = :$column, ";
            $update_values[":$column"] = $value;
        }

        $sql = rtrim($sql, ", ");

        if (isset($where)) {
            $sql .= " WHERE $where";
        } elseif (isset($this->model_to_array()["id"])) {
            $sql .= " WHERE id = :id";
            $update_values[":id"] = $this->model_to_array()["id"];
        } else {
            return false;
        }

        $statement = $this->___pdo___->prepare($sql);

        foreach ($update_values as $param => &$value) {
            $statement->bindValue($param, $value);
        }

        $success = $statement->execute();

        if ($success) {
            $this->find_by_id($this->model_to_array()["id"]);
        }

        return $success;
    }

    public function delete($where = null): bool
    {
        $update_columns = $this->model_to_array();

        $sql = "DELETE FROM $this->__table_name__ ";
        $update_values = [];

        if (isset($where)) {
            $sql .= " WHERE $where";
        } elseif (isset($this->model_to_array()["id"])) {
            $sql .= " WHERE id = :id";
            $update_values[":id"] = $this->model_to_array()["id"];
        } else {
            return false;
        }

        $statement = $this->___pdo___->prepare($sql);

        foreach ($update_values as $param => &$value) {
            $statement->bindValue($param, $value);
        }

        $success = $statement->execute();

        if ($success) {
            $this->find_by_id($this->model_to_array()["id"]);
        }

        return $success;
    }

    public function find_by_id($id = null)
    {
        $data = $this->select(["id" => $id]);

        if (!empty($data)) {
            $this->array_to_model(get_object_vars($data[0]));
        }
        return $this;
    }
}
