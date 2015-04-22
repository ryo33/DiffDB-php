<?php

class DiffDB{

    private $tables;

    function __construct($con){
        $this->tables = [];
    }

    function addTable($name, $columns){
        $this->tables[$name] == $columns;
    }

    private function diffTable($old, $new){
        $add = [];
        $change = [];
        foreach($new as $key => $column){
            if(array_key_exists($key, $old)){
                if($old[$key] !== $column){
                    $change[$key] = $column;
                }
                unset($old[$key]);
            }else{
                $add[$key] = $column;
            }
        }
        return [$add, $old, $change]; //added, deleted, changed columns
    }

    function updateDB($truncate=true){
    }

}
