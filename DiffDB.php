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
        if(!(strpos($name, '`') or strpos($name, ';'))){
            $this->tables[$name] = $columns;
        }else{
            //TODO throwing exception or return error message
        }
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

    function updateDB($option=[]){
        $new_tables = $this->tables;
        $this->con->exec(<<<SQL
CREATE TABLE IF NOT EXISTS `{$this->prefix}table`(
`name` VARCHAR(64) PRIMARY KEY
);
CREATE TABLE IF NOT EXISTS `{$this->prefix}column`(
`id` INTEGER PRIMARY KEY AUTO_INCREMENT,
`name` VARCHAR(64),
`structure` VARCHAR(128),
`table_name` VARCHAR(64)
);
SQL
        );
        $old_table_names = $this->con->fetchColumnAll("SELECT `name` FROM `{$this->prefix}table`;");
        $old_tables = [];
        foreach($old_table_names as $old_table_name){
            $columns = $this->con->fetchAll("SELECT `name`, `structure` FROM `{$this->prefix}column` WHERE `table_name` = ?", $old_table_name);
            foreach($columns as $column){
                $old_tables[$old_table_name][$column['name']] = $column['structure'];
            }
        }
        foreach($old_tables as $key => $old_table){
            if(array_key_exists($key, $new_tables)){
                //update table
                $result = $this->diffTable($old_table, $new_tables[$key]);
                if(count($result[0]) or count($result[1]) or count($result[2])){
                    if(isset($option['truncate']) and $option['truncate']){
                        $this->con->execute("TRUNCATE TABLE `$key`");
                    }
                    foreach($result[1] as $deleted_name => $deleted){
                        $this->con->execute("ALTER TABLE `$key` DROP COLUMN `$deleted_name`;");
                        $this->con->execute("DELETE FROM `{$this->prefix}column` WHERE `table_name` = ? AND `name` = ?", [$key, $deleted_name]);
                    }
                    foreach($result[0] as $added_name => $added){
                        $this->con->execute("ALTER TABLE `$key` ADD `$added_name` $added;");
                        $this->con->insert($this->prefix . 'column', ['table_name' => $key, 'name' => $added_name, 'structure' => $added]);
                    }
                    foreach($result[2] as $added_name => $added){
                        $this->con->execute("ALTER TABLE `$key` MODIFY `$added_name` $added;");
                        $this->con->update($this->prefix . 'column', ['structure' => $added], '`table_name` = ? AND `name` = ?', [$key, $added_name]);
                    }
                }
                unset($new_tables[$key]);
            }else if(isset($option['drop']) and $option['drop']){
                //delete table
                $this->con->execute("DROP TABLE `$key`;");
                $this->con->execute("DELETE FROM `{$this->prefix}table` WHERE `name` = ?", $key);
            }
        }
        //create table
        foreach($new_tables as $key => $new_table){
            $columns = [];
            foreach($new_table as $key2 => $column){
                $columns[] = $key2 . ' ' . $column;
            }
            $this->con->execute("CREATE TABLE `$key`(" . implode(', ', $columns) . ');');
            $this->con->insert($this->prefix . 'table', ['name' => $key]);
            foreach($new_table as $column_name => $column){
                $this->con->insert($this->prefix . 'column', ['name' => $column_name, 'structure' => $column, 'table_name' => $key]);
            }
        }
    }

}
