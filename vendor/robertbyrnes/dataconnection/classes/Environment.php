<?php

Abstract Class Environment
{
    protected function checkLocation() : array
    {
        if (preg_match('/wamp64|repositories/i', $_SERVER['DOCUMENT_ROOT']))
        {
            $env = parse_ini_file('../../../private/local.ini');
            if ($env['name'] === 'local')
            {
                $GLOBALS['environment'] = 'TRUE';
                $GLOBALS['dsn'] = 'mysql:dbname='.$env['dbname'].';host='.$env['host'];
                $GLOBALS['username'] = $env['username'];
                $GLOBALS['password'] = $env['password'];
                return $GLOBALS;
            }
        }

        else
        {
            $env = parse_ini_file('../../../private/envirosample.ini');
            if ($env['name'] === 'envirosample')
            {
                $GLOBALS['environment'] = 'FALSE';
                $GLOBALS['database'] = $env['dbname'];
                $GLOBALS['host'] = $env['host'];
                $GLOBALS['username'] = $env['username'];
                $GLOBALS['password'] = $env['password'];
                $GLOBALS['dsn'] = 'mysql:host='.$env['host'].';dbname='.$env['dbname'];
                return $GLOBALS;
            }
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
