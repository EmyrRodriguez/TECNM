<?php

include_once 'configDB.php';

class ConnectionDB {
    //put your code here
    private $driver;
    private $host;
    private $user;
    private $pass;
    private $database;
    private $charset;
    
    private static $instancia;
    
    function __construct() {
        $this->driver = DB_DRIVER;
        $this->host = HOST;
        $this->user = DB_USER;
        $this->pass = DB_PASSWORD;
        $this->database = DB_NAME;
        $this->charset = CHARSET;
    }    
    // SINGLETON
    public static function connectSngtn()
    {
        if (!isset(self::$instancia)) {
            $ConnectionDB = __CLASS__;
            self::$instancia = new $ConnectionDB;
        } 
        return self::$instancia;
    }  
    // Evita que el objeto se pueda clonar
    public function __clone()
    {
        trigger_error('La clonación de este objeto no está permitida', E_USER_ERROR);
    }
    
    function DBConnect() {
        try {
            //$db = new PDO("mysql:host=".HOST.";dbname=".DB_NAME, DB_USER, DB_PASSWORD);
            $db = new PDO($this->driver . ":host=" . $this->host . ";dbname=" . $this->database, $this->user, $this->pass);
            $db->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//            $db->setAttribute(PDO::ATTR_PERSISTENT, true);
            $db->query("SET NAMES 'utf8';");
            return ($db);
        } catch (PDOException $exc) {
            echo $exc->getTraceAsString();
            print "Error: " . $exc->getMessage();
        }
    }
}
