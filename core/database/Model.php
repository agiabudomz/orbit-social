<?php


abstract class Model {
    protected static $table;
    protected $attributes = [];

    public function __construct($attributes = []) {
        $this->attributes = $attributes;
    }

    // 🔹 Acessar propriedades do registro
    public function __get($key) {
        return $this->attributes[$key] ?? null;
    }

    public function __set($key, $value) {
        $this->attributes[$key] = $value;
    }

    // 🔹 CRUD instanciado
    public function save() {
        DB::setTable(static::$table);
        if (isset($this->attributes['id'])) {
            // UPDATE
            $id = $this->attributes['id'];
            $data = $this->attributes;
            unset($data['id']);
            return DB::update($data, ['id' => $id]);
        } else {
            // CREATE
            $id = DB::create($this->attributes);
            $this->attributes['id'] = $id;
            return $id;
        }
    }

    public function delete() {
        DB::setTable(static::$table);
        if (!isset($this->attributes['id'])) return false;
        return DB::delete(['id' => $this->attributes['id']]);
    }


    

    // 🔹 Métodos estáticos
    public static function all($where = "1", $fields = "*") {
        DB::setTable(static::$table);
        $rows = DB::read($where, $fields);
        return array_map(fn($r) => new static($r), $rows);
    }

    public static function count($where = "*") {
        DB::setTable(static::$table);
        $rows = DB::read($where);
        return count($rows);
    }

    public static function find($id) {
        DB::setTable(static::$table);
        $row = DB::find($id);
        return $row ? new static($row) : null;
    }

    public static function filter($where = "1", $orderBy = "id", $direction = "DESC", $limit = 10) {
        DB::setTable(static::$table);
        $rows = DB::filter($where, $orderBy, $direction, $limit);
        return array_map(fn($r) => new static($r), $rows);
    }

    public static function search($field, $value) {
        DB::setTable(static::$table);
        $rows = DB::search($field, $value);
        return array_map(fn($r) => new static($r), $rows);
    }

    public static function paginate($page = 1, $perPage = 10, $where = "1", $fields = "*") {
        DB::setTable(static::$table);
        $rows = DB::paginate($page, $perPage, $where, $fields);
        return array_map(fn($r) => new static($r), $rows);
    }

    public static function latest($limit = 10, $fields = "*") {
        DB::setTable(static::$table);
        $rows = DB::latest($limit, $fields);
        return array_map(fn($r) => new static($r), $rows);
    }

    public static function first($fields = "*") {
        DB::setTable(static::$table);
        $row = DB::first($fields);
        return $row ? new static($row) : null;
    }

    public static function firstOne($where = "1", $fields = "*") {
        DB::setTable(static::$table);
        $row = DB::firstOne($where, $fields);
        return $row ? new static($row) : null;
    }

    // 🔹 Relacionamentos Active Record

    // converte tabela plural para singular (remove 's' final)
    protected function singular($tableName) {
        if (substr($tableName, -1) === 's') {
            return substr($tableName, 0, -1);
        }
        return $tableName;
    }

    // One-to-Many
    protected function hasMany($relatedClass, $foreignKey = null) {
        if (!$foreignKey) {
            $foreignKey = $this->singular(static::$table) . "_id";
        }
        DB::setTable($relatedClass::$table);
        $rows = DB::read([$foreignKey => $this->id]);
        return array_map(fn($r) => new $relatedClass($r), $rows);
    }

    // One-to-One
    protected function hasOne($relatedClass, $foreignKey = null) {
        if (!$foreignKey) {
            $foreignKey = $this->singular($relatedClass::$table) . "_id";
        }
        DB::setTable($relatedClass::$table);
        $row = DB::firstOne([$foreignKey => $this->id]);
        return $row ? new $relatedClass($row) : null;
    }

    // Many-to-Many via pivot table
    protected function belongsToMany($relatedClass, $pivotTable, $foreignKey, $relatedKey) {
        DB::setTable($pivotTable);
        $pivotRows = DB::read([$foreignKey => $this->id]);
        $ids = array_column($pivotRows, $relatedKey);

        DB::setTable($relatedClass::$table);
        $results = [];
        foreach ($ids as $id) {
            $results[] = new $relatedClass(DB::find($id));
        }
        return $results;
    }
}