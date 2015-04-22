<?php

class DiffDB{

    private $tables;
    private $prefix;
    private $con;

    function __construct($con, $prefix='diffdb_'){
        $this->con = $con;
        $this->prefix = $prefix;
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
        $this->con->execute(<<<SQL
CREATE TABLE IF NOT EXISTS `{$this->prefix}table`(
`name` VARCHAR(64) PRIMARY KEY,
);
CREATE TABLE IF NOT EXISTS `{$this->prefix}column`(
`id` INTEGER PRIMARY KEY,
`name` VARCHAR(64),
`structure` VARCHAR(128),
`table_name` VARCHAR(64)
);
SQL
        );
        $old_table_names = $this->con->fetchColumnAll("SELECT `name` FROM `{$this->prefix}table`;");
        foreach($old_table_names as $old_table_name){
            $columns = $this->con->fetchAll("SELECT `name`, `structure` FROM `{$this->prefix}column` WHERE `table_name` = ?", $old_table_name);
            foreach($columns as $column){
                $old_tables[$old_table_name][$column['name']] = $column['structure'];
            }
        }
    }

}
