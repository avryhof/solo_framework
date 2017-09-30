<?php

class DB {
    var $conn = None;
    var $statement = '';
    var $prepared_statement = None;
    var $executed_statement = None;

    function __construct($db_key = 'default') {
        $dsn  = DATABASES[$db_key]['ENGINE'].":";
        $dsn .= "host=".DATABASES[$db_key]['HOST'].";";
        $dsn .= "port=".DATABASES[$db_key]['PORT'].";";
        $dsn .= "dbname=".DATABASES[$db_key]['NAME'].";";
        $dsn .= "charset=".DATABASES[$db_key]['ENGINE'];
        $this->conn = new PDO($dsn, DATABASES[$db_key]['USER'], DATABASES[$db_key]['PASSWORD'], DATABASES[$db_key]['OPTS']);
    }

    function escape($string) {
        if (is_numeric($string)) {
            return $string;
        } else {
            return $this->conn->quote($string);
        }
    }

    function prepare($statement) {
        $this->statement = $statement;
        $this->prepared_statement = $this->conn->prepare($statement);
        return $this->prepared_statement;
    }

    function query($sql) {
        return $this->conn->query($sql);
    }

    function query_to_set($sql) {
        $retn = [];
        $rows = $this->query($sql);

        while ($row = $rows->fetch(PDO::FETCH_ASSOC)) {
            $retn[] = $row;
        }
        return $retn;
    }
}

class DatabaseObject {
    var $table = false;
    var $database;
    var $last_query = None;
    var $dataset = [];
    var $item = '';
    var $count = 0;

    function __construct($table = false, $database = false) {
        $this->database = (!$database ? new DB() : $database);
        if (!$this->table && $table) {
            $this->table = $table;
        }
    }

    function buildquery($where) {
        if (in_array($where, ['self', 'this'])) {
            $where = "`id` = " . $this->item['id'];
        } elseif (is_numeric($where)) {
            $where = "`id` = ".$where;
        } elseif (is_array($where)) {
            $set = [];
            foreach($where as $key => $value) {
                $set[] = "`$key` = " . $this->database->escape($value);
            }
            $where = implode(' AND ', $set);
        }
        return $where;
    }

    function statement_array($data) {
        $statement_data = [];
        foreach ($data as $key => $value) {
            $statement_data[':'.$key] = $value;
        }
        return $statement_data;
    }

    function filter($where) {
        $where = $this->buildquery($where);
        $table = $this->table;
        $this->last_query = "SELECT * FROM $table WHERE $where";
        $rows = $this->database->query_to_set($this->last_query);
        $this->dataset = $rows;
        $this->count = count($this->dataset);

        return $this->dataset;
    }

    function all() {
        $table = $this->table;
        $this->last_query = "SELECT * FROM $table";
        $rows = $this->database->query_to_set($this->last_query);
        $this->dataset = $rows;
        $this->count = count($this->dataset);

        return $this->dataset;
    }

    function get($where) {
        $where = $this->buildquery($where);
        $table = $this->table;
        $this->last_query = "SELECT * FROM $table WHERE $where LIMIT 1";
        $rows = $this->database->query_to_set($this->last_query);
        $this->dataset = $rows;
        $this->count = count($this->dataset);
        $this->item = $this->dataset[0];

        return $this->item;
    }

    function add($data) {
        $to_prepare  = "INSERT INTO ".$this->table;
        $to_prepare .= "(" . implode(',', array_keys($data)) . ") VALUES ";
        $to_prepare .= "(:" . implode(',:', array_keys($data)) . ");";

        $this->database->prepare($to_prepare)->execute($this->statement_data($data));
        $this->dataset = $this->filter($data);
        $this->item = $this->dataset[0];

        return $this->item;
    }

    function create($data) {
        return $this->add($data);
    }

    function update($data, $where = false) {
        if ($where !== false && !empty($where)) {
            $where = $this->buildquery($where);
        } else {
            if ($this->count == 1) {
                $where = $this->buildquery('self');
            }
        }
        $to_prepare = "UPDATE " . $this->table ." SET ";
        foreach($data as $key => $val) {
            $to_prepare .= "$key = :$key";
        }
        $to_prepare .= " WHERE $where";

        $this->database->prepare($to_prepare)->execute($this->statement_data($data));
        $this->dataset = $this->filter($data);
        $this->item = $this->dataset[0];

        return $this->item;
    }

    function delete($where = false) {
        if ($where !== false && !empty($where)) {
            $where = $this->buildquery($where);
        } else {
            if ($this->count == 1) {
                $where = $this->buildquery('self');
            }
        }
        $sql = "DELETE FROM " . $this->table . " WHERE $where";
        $this->database->query($sql);

        return $this->filter($where);
    }

    function exists($where) {
        if ($where !== false && !empty($where)) {
            $where = $this->buildquery($where);
        } else {
            if ($this->count == 1) {
                $where = $this->buildquery('self');
            }
        }
        $items = $this->filter($where);

        return $this->count > 0;
    }
}