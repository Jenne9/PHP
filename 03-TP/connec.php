<?php

define('HOST', 'localhost');
define('USER', 'root');
define('PASSWORD', 'root');
define('DBNAME', 'discussion');


$pdo = new PDO(
    'mysql: host=' . HOST . ';charset=utf8;dbname=' . DBNAME,
    USER,
    PASSWORD,
    array(
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    )
);
