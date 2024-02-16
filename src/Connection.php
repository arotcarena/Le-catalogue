<?php 
namespace Vico;

use \PDO;

class Connection
{
    private static $pdo;

    public static function getPdo()
    {
        if(self::$pdo === null)
        {
            self::$pdo = new PDO(Config::DATA_BASE.':dbname='.Config::DBNAME.';host='.Config::HOST, Config::DB_ID, Config::DB_PASSWORD, [
                PDO::ATTR_ERRMODE => Config::PDO_ATTR_ERRMODE
            ]);
        }
        return self::$pdo;
    }
}