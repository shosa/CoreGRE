<?php
/**
 * Eloquent ORM Bootstrap
 * Inizializza e configura Eloquent ORM standalone
 */

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;

class EloquentBootstrap
{
    private static $capsule = null;

    /**
     * Inizializza Eloquent ORM
     */
    public static function init()
    {
        if (self::$capsule !== null) {
            return self::$capsule;
        }

        // Crea il Capsule manager
        $capsule = new Capsule();

        // Configura la connessione al database
        $capsule->addConnection([
            'driver'    => 'mysql',
            'host'      => DB_HOST,
            'port'      => DB_PORT,
            'database'  => DB_NAME,
            'username'  => DB_USER,
            'password'  => DB_PASS,
            'charset'   => DB_CHARSET,
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
            'options'   => [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ],
        ], 'default');

        // Setup the Event Dispatcher per Eloquent events
        $capsule->setEventDispatcher(new Dispatcher(new Container()));

        // Make Capsule globally available
        $capsule->setAsGlobal();

        // Bootstrap Eloquent
        $capsule->bootEloquent();

        self::$capsule = $capsule;

        return $capsule;
    }

    /**
     * Ottiene l'istanza del Capsule
     */
    public static function getCapsule()
    {
        if (self::$capsule === null) {
            self::init();
        }

        return self::$capsule;
    }

    /**
     * Ottiene la connessione al database
     */
    public static function getConnection()
    {
        return self::getCapsule()->getConnection();
    }

    /**
     * Ottiene il schema builder
     */
    public static function getSchema()
    {
        return self::getConnection()->getSchemaBuilder();
    }
}