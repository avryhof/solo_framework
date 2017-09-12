<?php

class path {

    function exists($path) {
        return is_dir($path) || file_exists($path);
    }

    function join($parts) {
        return implode(DIRECTORY_SEPARATOR, $parts);
    }
}

class os {
    var $path;

    function __construct() {
        $this->path = new path();
    }
}

$os = new os();