<?php

namespace AgrandesR;

use AgrandesR\GlobalResponse;
use Error;
use Exception;
use PDOException;
use PDO;

/*
EXAMPLE ENV:
DB_FLAG_TYPE=           # mysql psql
DB_FLAG_HOST=
DB_FLAG_USER=
DB_FLAG_PASS=
DB_FLAG_DTBS=
DB_FLAG_PORT=
DB_FLAG_CHAR=           # DEFAULT UTF-8

Example:

$DB = new DatabaseTool();
*/

class DBtool {
    protected PDO $pdo;
    protected bool $operative;
    public string $lastError;

    function __construct(string $flag='') {
        try {
            $flag = (empty($flag))? 'DB_': 'DB_'.$flag.'_';

            if(!isset($_ENV[$flag . 'TYPE'])) GlobalResponse::addError('The ENV value '.$flag .'TYPE' .' not found');
            if(!isset($_ENV[$flag . 'HOST'])) GlobalResponse::addError('The ENV value '.$flag .'HOST' .' not found');
            if(!isset($_ENV[$flag . 'USER'])) GlobalResponse::addError('The ENV value '.$flag .'USER' .' not found');
            if(!isset($_ENV[$flag . 'PASS'])) GlobalResponse::addError('The ENV value '.$flag .'PASS' .' not found');
            if(!isset($_ENV[$flag . 'DTBS'])) GlobalResponse::addError('The ENV value '.$flag .'DTBS' .' not found');
            if(!isset($_ENV[$flag . 'PORT'])) GlobalResponse::addError('The ENV value '.$flag .'PORT' .' not found');

            if(GlobalResponse::hasErrors()) GlobalResponse::showAndDie();

            $type=$_ENV[$flag . 'TYPE'];
            $host=$_ENV[$flag . 'HOST'];
            $user=$_ENV[$flag . 'USER'];
            $pass=$_ENV[$flag . 'PASS'];
            $dtbs=$_ENV[$flag . 'DTBS'];
            $port=$_ENV[$flag . 'PORT'];
            $char=isset($_ENV[$flag . 'CHAR']) ? $_ENV[$flag . 'CHAR'] : 'UTF8';
            $dsn = "$type:host=$host;port=$port;dbname=$dtbs;charset=$char";

            $this->pdo = new PDO($dsn, $user, $pass);
        
            $this->operative=true;
        } catch (Error | Exception | PDOException $e) {
            GlobalResponse::addError($e->getMessage());
        }
    }

    public function isOperative() {
        return $this->operative;
    }

    public function query(string $sql, array $values=[]) : array | bool {
        try {
            $sql=trim($sql);
            if(strtoupper(substr($sql, 0, 6)) === "SELECT") $sqlQuery=true;
            else $sqlQuery=false;
            if(empty($values)) {
                $statement = $this->pdo->query($sql);
            } else {
                $statement = $this->pdo->prepare($sql);
                foreach ($values as $key=>&$value){
                    //echo "$key - $value " . $this->getPdoType($value) ."\n";
                    $statement->bindParam("$key", $value, $this->getPdoType($value));
                }
                $statement->execute();
            }
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            return empty($result)? true : $result;
        } catch (PDOException $e) {
            GlobalResponse::addError($e->getMessage());
            return false;
        }

    }

    private function getPdoType($value){
        switch(gettype($value)) {
            case ("boolean"):
                return PDO::PARAM_BOOL;
            case ("integer"):
                return PDO::PARAM_INT;
            case ("NULL"):
                return PDO::PARAM_NULL;
            default:
                return PDO::PARAM_STR;
        }
    }

    static function sql(string $flag='', string $sql='SHOW DATABASES;', array $values=[]) : array | bool {
        return (new self($flag))->query($sql, $values);
    }
        
}