<?php
require __DIR__ . '\..\vendor\autoload.php';

class DataConnection extends Environment
{
    private $dB;
    private $message;

    public function __construct()
    {
        $GLOBALS = $this->checkLocation();
        $this->dbConnect();
    }
    
    /**
     * Uses the database credentials to establish and set a property containing a PDO connection.
     *
     * @return boolean
     */
    private function dbConnect() : boolean
    {
        try
        {
            $this->dB = new PDO($GLOBALS['dsn'], $GLOBALS['username'], $GLOBALS['password']);
            $this->message = 'Database connection a success';
            return true;
        }

        catch(PDOException $error)
        { 
            $this->message = 'Database connection error: '.$error;
            return false;
        }
    }

    /**
     * Safe way to fetch results from a single row using a PDO connection.
     * If return type is TRUE this functions returns an array, otherwise
     * it returns an object of stdClass.
     *
     * @param string $query
     * @param boolean $returnType
     * @return mixed
     */
    static protected function preparedQueryRow($query, bool $returnType=FALSE)
    {
        try
        {
            $stmt = self::$dB->prepare($query);
            if (!$returnType)
            {
                 return $stmt->execute()->fetch();
            }
            else
            {
                return $stmt->execute->fetch(FETCH_ASSOC);
            }
        }
        catch (PDOException $error)
        {
            self::$message = 'Database query failed: '.$error;
        }
    }

    /**
     * Safe way to fetch results from multiple rows using a PDO connection.
     * If return type is TRUE this functions returns an array, otherwise
     * it returns an object of stdClass.
     *
     * @param string $query
     * @param boolean $returnType
     * @return mixed
     */
    static protected function preparedQueryMany($query, bool $returnType=FALSE)
    {
        try
        {
            $stmt = self::$dB->prepare($query);
            if (!$returnType)
            {
                return $stmt->execute()->fetchAll();
            }
            else
            {
                return $stmt->execute->fetchAll(FETCH_ASSOC);
            }
        }
        catch (PDOException $error)
        {
            self::$message = 'Database query failed: '.$error;
        }
    }

    static protected function preparedInsert($query, array $values=NULL)
    {
        try
        {
            $stmt = self::$dB->prepare($query);
            if (!$returnType)
            {
                return $stmt->execute()->fetchAll();
            }
            else
            {
                return $stmt->execute->fetch(FETCH_ASSOC);
            }
        }
        catch (PDOException $error)
        {
            self::$message = 'Database query failed: '.$error;
        }
    }

    public function getMessage()
    {
        return $this->message;
    }
}

$db = new DataConnection;
$db->message();

