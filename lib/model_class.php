<?php

class Model extends BaseModel {
    function __toString()
    {
        return 'Model';
    }
}

class BaseModel {
    var $objects = null;
    var $table  = null;

    function __construct($table) {
        $this->table = $table;

        $this->objects = new DatabaseObject($this->table);

        foreach ($this as $key => $value) {
            print "$key => $value\n";
        }
    }

    function __toString()
    {
        return 'BaseModel';
    }
}