<?php
class DB {
    private static $conn;
    private static $table;

    // 🔹 Conectar ao banco
    public static function connect($host, $user, $pass, $db) {
        try {
            self::$conn = new mysqli($host, $user, $pass, $db);
            if (self::$conn->connect_error) {
                throw new Exception("Erro de conexão: " . self::$conn->connect_error);
            }
            return self::$conn;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public static function getConexao() {
        return self::$conn;
    }

    public static function setTable($table) {
        self::$table = $table;
    }

    // 🔹 Criar tabela dinamicamente
    public static function createTable($table, $fields) {
        try {
            $sql = "CREATE TABLE IF NOT EXISTS `$table` (
                id INT AUTO_INCREMENT PRIMARY KEY,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,";

            foreach ($fields as $name => $type) {
                $sql .= "`$name` $type,";
            }

            $sql = rtrim($sql, ",") . ") ENGINE=InnoDB;";

            if (!self::$conn->query($sql)) {
                throw new Exception("Erro ao criar tabela: " . self::$conn->error);
            }

            return true;
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public static function alterTable($table, $action, $column, $definition = "") {
        try {
            $action = strtoupper($action);
            $sql = "ALTER TABLE `$table` ";

            switch ($action) {
                case 'ADD':
                    // Adiciona uma nova coluna: ADD column_name type_definition
                    $sql .= "ADD `$column` $definition";
                    break;

                case 'DROP':
                    // Remove uma coluna: DROP COLUMN column_name
                    $sql .= "DROP COLUMN `$column` text";
                    break;

                case 'RENAME':
                    // Altera o nome da coluna: RENAME COLUMN old to new
                    // Nota: Requer MySQL 8.0+ ou MariaDB 10.5.2+
                    $sql .= "RENAME COLUMN `$column` TO `$definition`";
                    break;

                case 'MODIFY':
                    // Altera o tipo/definição: MODIFY COLUMN name new_definition
                    $sql .= "MODIFY COLUMN `$column` $definition";
                    break;

                default:
                    throw new Exception("Ação '$action' não suportada para ALTER TABLE.");
            }

            if (!self::$conn->query($sql)) {
                throw new Exception("Erro ao alterar tabela '$table': " . self::$conn->error);
            }

            return true;
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }

    // 🔹 Helper para montar WHERE (aceita string ou array)
    private static function buildWhere($where) {
        if (is_array($where)) {
            $parts = [];
            foreach ($where as $key => $value) {
                $key = preg_replace('/[^a-zA-Z0-9_]/', '', $key); 
                if (is_null($value)) {
                    $parts[] = "$key IS NULL";
                } elseif (is_int($value) || ctype_digit((string)$value)) {
                    $parts[] = "$key = " . intval($value);
                } else {
                    $parts[] = "$key = '" . self::$conn->real_escape_string($value) . "'";
                }
            }
            return implode(" AND ", $parts);
        }
        return $where ?: "1";
    }

    // 🔹 CREATE
    public static function create($data) {
        try {
            unset($data['id'], $data['created_at'], $data['updated_at']);
            $fields = implode(",", array_keys($data));
            $values = "'" . implode("','", array_map([self::$conn, 'real_escape_string'], $data)) . "'";
            $sql = "INSERT INTO " . self::$table . " ($fields) VALUES ($values)";
            if (!self::$conn->query($sql)) {
                throw new Exception("Erro ao inserir: " . self::$conn->error);
            }
            return self::$conn->insert_id;
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }

    // 🔹 READ
    public static function read($where = "1", $fields = "*") {
        try {
            $where = self::buildWhere($where);
            $sql = "SELECT $fields FROM " . self::$table . " WHERE $where";
            $result = self::$conn->query($sql);
            if (!$result) {
                throw new Exception("Erro ao buscar: " . self::$conn->error);
            }
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    // 🔹 UPDATE
    public static function update($data, $where) {
        try {
            unset($data['id'], $data['created_at']);
            $set = "";
            foreach ($data as $key => $value) {
                $set .= "$key='" . self::$conn->real_escape_string($value) . "',";
            }
            $set = rtrim($set, ",");

            $where = self::buildWhere($where);
            $sql = "UPDATE " . self::$table . " SET $set WHERE $where";
            if (!self::$conn->query($sql)) {
                throw new Exception("Erro ao atualizar: " . self::$conn->error);
            }
            return true;
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }

    // 🔹 DELETE
    public static function delete($where) {
        try {
            $where = self::buildWhere($where);
            $sql = "DELETE FROM " . self::$table . " WHERE $where";
            if (!self::$conn->query($sql)) {
                throw new Exception("Erro ao deletar: " . self::$conn->error);
            }
            return true;
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }

    // 🔹 COUNT
    public static function count($where = "1") {
        try {
            $where = self::buildWhere($where);
            $sql = "SELECT COUNT(*) as total FROM " . self::$table . " WHERE $where";
            $result = self::$conn->query($sql);
            if (!$result) {
                throw new Exception("Erro ao contar: " . self::$conn->error);
            }
            return $result->fetch_assoc()['total'];
        } catch (Exception $e) {
            echo $e->getMessage();
            return 0;
        }
    }

    // 🔹 FIND por ID
    public static function find($id) {
        $result = self::read(["id" => intval($id)], "*");
        return $result[0] ?? null;
    }

    public static function getBy(string $field, mixed $value) {
        return self::read([$field => $value]);
    }

    public static function getOneBy(string $field, mixed $value) {
        $results = self::read([$field => $value], "*");
        return $results[0] ?? null;
    }

    // 🔹 Filtrar com ORDER BY
    public static function orderBy($field, $direction = "ASC", $where = "1", $fields = "*") {
        try {
            $where = self::buildWhere($where);
            $direction = strtoupper($direction) === "DESC" ? "DESC" : "ASC";
            $sql = "SELECT $fields FROM " . self::$table . " WHERE $where ORDER BY $field $direction";
            $result = self::$conn->query($sql);
            if (!$result) {
                throw new Exception("Erro no orderBy: " . self::$conn->error);
            }
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    // 🔹 Filtrar com    
    public static function limit($limit, $offset = 0, $where = "1", $fields = "*", $direction = "DESC") {
        try {
            $where = self::buildWhere($where);
            $sql = "SELECT $fields FROM " . self::$table . " WHERE $where ORDER BY id $direction LIMIT " . intval($offset) . "," . intval($limit);
            $result = self::$conn->query($sql);
            if (!$result) {
                throw new Exception("Erro no limit: " . self::$conn->error);
            }
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    // 🔹 Filtrar com LIKE (busca aproximada)
    public static function like($field, $value, $fields = "*") {
        try {
            $field = preg_replace('/[^a-zA-Z0-9_]/', '', $field);
            $value = self::$conn->real_escape_string($value);
            $sql = "SELECT $fields FROM " . self::$table . " WHERE $field LIKE '%$value%'";
            $result = self::$conn->query($sql);
            if (!$result) {
                throw new Exception("Erro no like: " . self::$conn->error);
            }
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    // 🔹 Paginação
    public static function paginate($page = 1, $perPage = 10, $where = "1", $fields = "*") {
        $offset = ($page - 1) * $perPage;
        return self::limit($perPage, $offset, $where, $fields);
    }

        // 🔹 Retorna todos registros
    public static function all($fields = "*") {
        return self::read("1", $fields);
    }

    // 🔹 Retorna os N últimos registros
    public static function latest($limit = 10, $fields = "*") {
        $sql = "SELECT $fields FROM " . self::$table . " ORDER BY id DESC LIMIT " . intval($limit);
        $result = self::$conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    // 🔹 Retorna o primeiro registro (apenas 1 linha)
    public static function first($fields = "*") {
        $sql = "SELECT $fields FROM " . self::$table . " ORDER BY id ASC LIMIT 1";
        $result = self::$conn->query($sql);
        return $result ? $result->fetch_assoc() : null;
    }


    // 🔹 Retorna apenas 1 registro (primeiro que encontrar)
    public static function firstOne($where = "1", $fields = "*") {
        $where = self::buildWhere($where);
        $sql = "SELECT $fields FROM " . self::$table . " WHERE $where LIMIT 1";
        $result = self::$conn->query($sql);
        return $result ? $result->fetch_assoc() : null;
    }

    // 🔹 Busca por LIKE (aproximado)
    public static function search($field, $value, $fields = "*") {
        $field = preg_replace('/[^a-zA-Z0-9_]/', '', $field);
        $value = self::$conn->real_escape_string($value);
        $sql = "SELECT $fields FROM " . self::$table . " WHERE $field LIKE '%$value%'";
        $result = self::$conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    // 🔹 Retorna registros entre dois valores (ex: datas, preços)
    public static function between($field, $min, $max, $fields = "*") {
        $field = preg_replace('/[^a-zA-Z0-9_]/', '', $field);
        $sql = "SELECT $fields FROM " . self::$table . " 
                WHERE $field BETWEEN '" . self::$conn->real_escape_string($min) . "' 
                AND '" . self::$conn->real_escape_string($max) . "'";
        $result = self::$conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
     // 🔹 Filtro flexível: ORDER BY + LIMIT + WHERE
    public static function filter($where = "1", $orderBy = "id", $direction = "DESC", $limit = 10, $fields = "*") {
    try {
            $where = self::buildWhere($where);
            $direction = strtoupper($direction) === "DESC" ? "DESC" : "ASC";
            $sql = "SELECT $fields FROM " . self::$table . " 
                    WHERE $where 
                    ORDER BY $orderBy $direction 
                    LIMIT " . intval($limit);

            $result = self::$conn->query($sql);
            if (!$result) {
                throw new Exception("Erro no filter: " . self::$conn->error);
            }
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }
    // 🔹 ALTER TABLE


     // 🔹 RESETAR UMA TABELA (apaga todos os registros, mas mantém a estrutura)
    public static function resetTable($table) {
        try {
            $sql = "TRUNCATE TABLE `$table`";
            if (!self::$conn->query($sql)) {
                throw new Exception("Erro ao resetar tabela $table: " . self::$conn->error);
            }
            return true;
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }

    // 🔹 RECRIAR UMA TABELA (apaga tabela e cria novamente com os campos fornecidos)
    public static function recreateTable($table, $fields) {
        try {
            // Deleta tabela se existir
            $dropSql = "DROP TABLE IF EXISTS `$table`";
            if (!self::$conn->query($dropSql)) {
                throw new Exception("Erro ao deletar tabela $table: " . self::$conn->error);
            }

            // Cria novamente
            return self::createTable($table, $fields);
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }

    // 🔹 RESETAR TODAS AS TABELAS (trunca todas tabelas existentes)
    public static function resetAllTables() {
        try {
            $result = self::$conn->query("SHOW TABLES");
            if (!$result) {
                throw new Exception("Erro ao listar tabelas: " . self::$conn->error);
            }

            while ($row = $result->fetch_array()) {
                $table = $row[0];
                self::resetTable($table);
            }
            return true;
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }

    // 🔹 DELETAR E RECRIAR TODAS AS TABELAS (necessita array de tabelas e campos)
    public static function recreateAllTables($tables) {
        /*
        $tables = [
            "usuarios" => ["nome VARCHAR(255)", "email VARCHAR(255)"],
            "produtos" => ["nome VARCHAR(255)", "preco DECIMAL(10,2)"]
        ];
        */
        try {
            foreach ($tables as $table => $fields) {
                self::recreateTable($table, $fields);
            }
            return true;
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }

    // 🔹 DELETAR UMA TABELA GENÉRICA
    public static function dropTable(string $table) {
        try {
            // segurança: permite apenas letras, números e underline
            $table = preg_replace('/[^a-zA-Z0-9_]/', '', $table); 
            $sql = "DROP TABLE IF EXISTS `$table`";
            if (!self::$conn->query($sql)) {
                throw new Exception("Erro ao deletar tabela $table: " . self::$conn->error);
            }
            return true;
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }

    // 🔹 DELETAR TODAS AS TABELAS GENÉRICAMENTE
    public static function dropAllTables() {
        try {
            // desativa foreign keys
            self::$conn->query("SET FOREIGN_KEY_CHECKS = 0");

            // pega todas as tabelas do DB
            $result = self::$conn->query("SHOW TABLES");
            if (!$result) {
                throw new Exception("Erro ao listar tabelas: " . self::$conn->error);
            }

            $tables = [];
            while ($row = $result->fetch_array()) {
                $tables[] = $row[0];
            }

            // drop de todas as tabelas
            foreach ($tables as $table) {
                self::dropTable($table);
            }

            // reativa foreign keys
            self::$conn->query("SET FOREIGN_KEY_CHECKS = 1");

            return true;
        } catch (Exception $e) {
            echo "Erro ao deletar todas as tabelas: " . $e->getMessage();
            return false;
        }
    }


}

DB::connect(
    $_ENV['DB_HOST'],
    $_ENV['DB_USERNAME'],
    $_ENV['DB_PASSWORD'],
    $_ENV['DB_DATABASE']
);