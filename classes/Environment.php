<?php

abstract class Environment
{
    protected function checkLocation()
    {
        if (preg_match('/wamp64|repositories/i', $_SERVER['DOCUMENT_ROOT']))
        {
            $GLOBALS['environment'] = 'TRUE';
            $GLOBALS['dsn'] = 'mysql:dbname=test_enviro;host=localhost';
            $GLOBALS['username'] = 'root';
            $GLOBALS['password'] = ''; 
            return $GLOBALS;
        }

        else
        {
            $GLOBALS['environment'] = 'FALSE';
            $GLOBALS['database'] = 'u610815376_derriston';
            $GLOBALS['host'] = 'localhost';
            $GLOBALS['username'] = 'u610815376_derristonBoss';
            $GLOBALS['password'] = '|n1ED~Gcj';
            $GLOBALS['dsn'] = 'mysql:host=localhost;dbname=u610815376_derriston';
            return $GLOBALS;
        }
    }

    protected function initTemplateEngine()
    {
        if (isset($GLOBALS['environment']))
        {
            if ($GLOBALS['environment'] == 'TRUE')
            {
                define('SMARTY_DIR', '../shared/smarty/libs/');
                require('../shared/smarty/libs/Smarty.class.php');
                $smarty = new Smarty();
                $smarty->setTemplateDir('templates/');
                $smarty->setCompileDir('SMARTY_DIR','templates_c/');
                $smarty->setConfigDir('SMARTY_DIR','configs/');
                $smarty->setCacheDir('SMARTY_DIR','cache/');
            }
            
            else
            {
                require('/home/u610815376/public_html/smarty/libs/Smarty.class.php');
                $smarty = new Smarty();
                $smarty->setTemplateDir('/home/u610815376/public_html/templates');
                $smarty->setCompileDir('/home/u610815376/public_html/templates_c');
                $smarty->setCacheDir('/home/u610815376/public_html/cache');
                $smarty->setConfigDir('/home/u610815376/public_html/configs');
            }
        }   
    }
}