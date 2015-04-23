# DiffDB-php
This gets diffs of database structure and updates it.  
====
###Description
###Requirement
[EasySql](https://github.com/ryo33/ryo33PHPlib)
###Installation
`git clone https://github.com/ryo33/DiffDB-php`  
or  
`git submodule add https://github.com/ryo33/DiffDB-php diffdb`
###Usage
#Example
```
$ddb = new DiffDB($con); //$con = new EasySql("dsn", "username", "password");
$ddb->addTable('test_table', [
'id' => 'INT PRIMARY KEY AUTO_INCREMENT',
'text' => 'VARCHAR(64) NOT NULL'
]); //register tables
$ddb->addTable('test_table2', [
'id' => 'INT PRIMARY KEY AUTO_INCREMENT',
'number' => 'INT NOT NULL'
]);
$ddb->updateDB(); //update table structures
```
Comment out the `$ddb->updateDB()` When you don't need updating.  
#Options
You can specify some options like this when calling `$ddb->updateDB()`.  
```
$ddb->updateDB([
    'drop' => false, //If it's true and `$dbb->addTable(...)` is removed, DiffDB drops the table.
    'truncate' => false //If it's true, DiffDB truncates tables updated columns
]);
```
###License
  [License](LICENSE)
###Author
  [ryo33](https://github.com/ryo33/ "ryo33's github page")
