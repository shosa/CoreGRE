<?php
/**
 * Environment Configuration Loader
 * Gestisce il caricamento delle variabili di ambiente dal file .env
 */

class Env
{
    private static $loaded = false;
    private static $variables = [];

    /**
     * Carica il file .env
     */
    public static function load($path = null)
    {
        if (self::$loaded) {
            return;
        }

        $path = $path ?? APP_ROOT . '/.env';

        if (!file_exists($path)) {
            // Se non esiste .env, prova con .env.example
            $examplePath = APP_ROOT . '/.env.example';
            if (file_exists($examplePath)) {
                error_log("COREGRE: File .env non trovato, usa .env.example come template");
            }
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            // Ignora commenti e righe vuote
            if (strpos(trim($line), '#') === 0 || empty(trim($line))) {
                continue;
            }

            // Parse KEY=VALUE
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);

                // Rimuovi le virgolette se presenti
                $value = trim($value, '"\'');

                // Salva la variabile
                self::$variables[$key] = $value;

                // Imposta anche come variabile di ambiente di sistema
                $_ENV[$key] = $value;
                putenv("$key=$value");
            }
        }

        self::$loaded = true;
    }

    /**
     * Ottiene una variabile di ambiente
     */
    public static function get($key, $default = null)
    {
        // Prova prima dalle variabili caricate
        if (isset(self::$variables[$key])) {
            return self::$variables[$key];
        }

        // Poi da $_ENV
        if (isset($_ENV[$key])) {
            return $_ENV[$key];
        }

        // Infine da getenv()
        $value = getenv($key);
        if ($value !== false) {
            return $value;
        }

        return $default;
    }

    /**
     * Controlla se una variabile esiste
     */
    public static function has($key)
    {
        return self::get($key) !== null;
    }

    /**
     * Ottiene una variabile come booleano
     */
    public static function getBool($key, $default = false)
    {
        $value = self::get($key);
        
        if ($value === null) {
            return $default;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Ottiene una variabile come intero
     */
    public static function getInt($key, $default = 0)
    {
        $value = self::get($key);
        
        if ($value === null) {
            return $default;
        }

        return (int) $value;
    }

    /**
     * Ottiene tutte le variabili caricate
     */
    public static function all()
    {
        return self::$variables;
    }
}