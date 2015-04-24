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
Just do:  
1. Creating DiffDB object
2. Registering tables
3. Calling `updateDB()`
#Example
```php
//$con = new EasySql("DSN", "USERNAME", "PASSWORD");
$ddb = new DiffDB($con);
//register tables
$ddb->addTable('test_table', [
'id' => 'INT PRIMARY KEY AUTO_INCREMENT',
'text' => 'VARCHAR(64) NOT NULL'
]);
$ddb->addTable('test_table2', [
'id' => 'INT PRIMARY KEY AUTO_INCREMENT',
'number' => 'INT NOT NULL'
]);
//update table structures
$ddb->updateDB();
```
Comment out the `$ddb->updateDB()` When you don't need updating.  
#Options
You can specify some options like this when calling `$ddb->updateDB()`.  
```php
$ddb->updateDB([
    'drop' => false, //If it's true and remove `$dbb->addTable(...)`, DiffDB drops the table.
    'truncate' => false //If it's true, DiffDB truncates tables updated columns
]);
```
###License
  [MIT](LICENSE)
###Author
  [ryo33](https://github.com/ryo33/ "ryo33's github page")
