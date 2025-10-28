<?php
/**
 * Database Connection Manager
 * Gestisce la connessione PDO al database con pattern Singleton
 */

class Database
{
    private static $instance = null;
    private $connection;
    
    private function __construct()
    {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET,
                PDO::ATTR_PERSISTENT => false
            ];
            
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
            
        } catch (PDOException $e) {
            if (APP_DEBUG) {
                die("Database connection failed: " . $e->getMessage());
            } else {
                error_log("Database connection failed: " . $e->getMessage());
                die("Database connection failed. Please try again later.");
            }
        }
    }
    
    /**
     * Ottiene l'istanza singleton del database
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Ottiene la connessione PDO
     */
    public function getConnection()
    {
        return $this->connection;
    }
    
    /**
     * Esegue una query preparata
     */
    public function query($sql, $params = [])
    {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            if (APP_DEBUG) {
                throw new Exception("Query failed: " . $e->getMessage() . " SQL: " . $sql);
            } else {
                error_log("Query failed: " . $e->getMessage() . " SQL: " . $sql);
                throw new Exception("Database query failed");
            }
        }
    }
    
    /**
     * Esegue una SELECT e ritorna tutti i risultati
     */
    public function fetchAll($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    /**
     * Esegue una SELECT e ritorna un singolo risultato
     */
    public function fetch($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }
    
    /**
     * Esegue una INSERT e ritorna l'ID inserito
     */
    public function insert($sql, $params = [])
    {
        $this->query($sql, $params);
        return $this->connection->lastInsertId();
    }
    
    /**
     * Esegue una UPDATE/DELETE e ritorna il numero di righe affette
     */
    public function execute($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }
    
    /**
     * Inizia una transazione
     */
    public function beginTransaction()
    {
        return $this->connection->beginTransaction();
    }
    
    /**
     * Conferma una transazione
     */
    public function commit()
    {
        return $this->connection->commit();
    }
    
    
    /**
     * Annulla una transazione
     */
    public function rollback()
    {
        return $this->connection->rollback();
    }
    
    /**
     * Ottiene l'ID dell'ultimo inserimento
     */
    public function getLastInsertId()
    {
        return $this->connection->lastInsertId();
    }
    
    /**
     * Impedisce la clonazione dell'oggetto
     */
    private function __clone() {}
    
    /**
     * Impedisce l'unserializzazione dell'oggetto
     */
    public function __wakeup() {}
}