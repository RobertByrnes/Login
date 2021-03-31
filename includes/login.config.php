<?php // $smarty->testInstall();
/**
 * @author Robert Byrnes
 * @created 01/01/2021
**/

if (preg_match('/wamp64|repositories/i', $_SERVER['DOCUMENT_ROOT'])){
    define('SMARTY_DIR', '../shared/smarty/libs/');
    require('../shared/smarty/libs/Smarty.class.php');
    $smarty = new Smarty();
    $smarty->setTemplateDir('templates/');
    $smarty->setCompileDir('SMARTY_DIR','templates_c/');
    $smarty->setConfigDir('SMARTY_DIR','configs/');
    $smarty->setCacheDir('SMARTY_DIR','cache/');
} else {
    require('/home/u610815376/public_html/smarty/libs/Smarty.class.php');
    $smarty = new Smarty();
    $smarty->setTemplateDir('/home/u610815376/public_html/templates');
    $smarty->setCompileDir('/home/u610815376/public_html/templates_c');
    $smarty->setCacheDir('/home/u610815376/public_html/cache');
    $smarty->setConfigDir('/home/u610815376/public_html/configs');
}

// Autoloader
if (preg_match('/wamp64|repositories/i', $_SERVER['DOCUMENT_ROOT'])) {
    spl_autoload_register(function($className) {
        $className = strtolower($className);
        $file = dirname(__DIR__) . '\\classes\\' . $className . '.php';
        if ($className == 'mypdo') {
            $file = dirname(__DIR__) . $className . '.php';
        }
        $file = str_replace('\\', DIRECTORY_SEPARATOR, $file);
        
        (file_exists($file)) ? include $file : print($file);
    });
} else {
    spl_autoload_register(function($className) { 
        $className = strtolower($className);
        $file = '/home/u610815376/public_html/classes/' . $className . '.php';
        if ($className == 'mypdo') {
            $file = dirname(__DIR__) . $className . '.php';
        }
        $file = str_replace('\\', DIRECTORY_SEPARATOR, $file);
        
        if (file_exists($file)) {
            include $file;
        } else {
            echo $file;
        }
    });
}