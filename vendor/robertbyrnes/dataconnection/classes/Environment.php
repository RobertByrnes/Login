<?php
Abstract Class Environment
{
    protected function checkLocation() : array
    {
        if (preg_match('/wamp64|repositories/i', $_SERVER['DOCUMENT_ROOT']))
        {
            $env = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/../../../private/local.ini');
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
            $env = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/../../../private/envirosample.ini');
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
}
