<?php

 /**
 * @author Robert Byrnes
 * @created 02/01/2021
 **/
 
// Database config
if (preg_match('/wamp64|repositories/i', $_SERVER['DOCUMENT_ROOT'])){

    define('dsn', 'mysql:dbname=test_enviro;host=localhost');
    define('username', 'root');
    define('password', '');

} else {
    $database = 'u610815376_derriston';
    $host = 'localhost';
    $username = 'u610815376_derristonBoss';
    $password = '|n1ED~Gcj';
    $dsn = 'mysql:host=localhost;dbname=u610815376_derriston';
}