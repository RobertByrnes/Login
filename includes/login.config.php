<?php

/**
 * @author Robert Byrnes
 * @created 01/01/2021
**/

session_start();


define('userfile', 'user.php');
define('loginfile', 'login.php');
define('activatefile', 'activate.php');


//template files
define('indexHead', 'templates/header.tpl');
define('indexTop', 'inc/indextop.htm');
define('loginForm', 'templates/loginform.tpl');
define('activationForm', 'inc/activationform.php');
define('indexMiddle', 'inc/indexmiddle.htm');
define('registerForm', 'templates/registerform.tpl');
define('indexFooter', 'templates/footer.tpl');
define('userPage', 'templates/userpage.tpl');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

