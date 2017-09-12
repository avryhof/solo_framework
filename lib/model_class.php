<?php

class BaseModel {
    var $objects = null;
    var $table  = null;

    function __construct($table) {
        $this->table = $table;

        $orm_db = new DB();
        $this->objects = new DatabaseObject($orm_db, $this->table);

        foreach ($this as $key => $value) {
            print "$key => $value\n";
        }
    }
}