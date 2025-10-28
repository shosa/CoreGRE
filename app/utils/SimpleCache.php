<?php
/**
 * Simple File-Based Cache
 * Gestisce cache su filesystem per query ripetute
 */

class SimpleCache
{
    private static $cacheDir = null;
    private static $enabled = true;
    private static $defaultTtl = 300; // 5 minuti default

    /**
     * Inizializza il sistema di cache
     */
    public static function init()
    {
        if (self::$cacheDir === null) {
            self::$cacheDir = APP_ROOT . '/storage/cache/';

            // Crea directory se non esiste
            if (!is_dir(self::$cacheDir)) {
                mkdir(self::$cacheDir, 0755, true);
            }

            // Disabilita cache in development se configurato
            if (defined('APP_ENV') && APP_ENV === 'development') {
                self::$enabled = defined('CACHE_ENABLED') ? CACHE_ENABLED : false;
            }
        }
    }

    /**
     * Ottiene un valore dalla cache
     *
     * @param string $key Chiave cache
     * @param callable|null $callback Funzione da eseguire se cache miss
     * @param int $ttl Tempo di vita in secondi
     * @return mixed
     */
    public static function remember($key, $callback = null, $ttl = null)
    {
        self::init();

        if (!self::$enabled) {
            return $callback ? $callback() : null;
        }

        $ttl = $ttl ?? self::$defaultTtl;
        $file = self::getCacheFile($key);

        // Controlla se cache valida
        if (file_exists($file)) {
            $data = unserialize(file_get_contents($file));
            if ($data['expires'] > time()) {
                return $data['value'];
            }
            // Cache scaduta, elimina
            @unlink($file);
        }

        // Cache miss, esegui callback
        if ($callback) {
            $value = $callback();
            self::put($key, $value, $ttl);
            return $value;
        }

        return null;
    }

    /**
     * Salva un valore in cache
     *
     * @param string $key Chiave cache
     * @param mixed $value Valore da cachare
     * @param int $ttl Tempo di vita in secondi
     */
    public static function put($key, $value, $ttl = null)
    {
        self::init();

        if (!self::$enabled) {
            return false;
        }

        $ttl = $ttl ?? self::$defaultTtl;
        $file = self::getCacheFile($key);

        $data = [
            'expires' => time() + $ttl,
            'value' => $value
        ];

        return file_put_contents($file, serialize($data), LOCK_EX) !== false;
    }

    /**
     * Elimina un valore dalla cache
     *
     * @param string $key Chiave cache
     */
    public static function forget($key)
    {
        self::init();
        $file = self::getCacheFile($key);
        if (file_exists($file)) {
            return @unlink($file);
        }
        return false;
    }

    /**
     * Pulisce tutta la cache
     */
    public static function flush()
    {
        self::init();
        $files = glob(self::$cacheDir . 'cache_*.dat');
        foreach ($files as $file) {
            @unlink($file);
        }
        return true;
    }

    /**
     * Pulisce cache scaduta
     */
    public static function gc()
    {
        self::init();
        $files = glob(self::$cacheDir . 'cache_*.dat');
        $cleaned = 0;

        foreach ($files as $file) {
            if (file_exists($file)) {
                $data = @unserialize(file_get_contents($file));
                if ($data && isset($data['expires']) && $data['expires'] < time()) {
                    @unlink($file);
                    $cleaned++;
                }
            }
        }

        return $cleaned;
    }

    /**
     * Ottiene il percorso del file cache
     *
     * @param string $key
     * @return string
     */
    private static function getCacheFile($key)
    {
        $hash = md5($key);
        return self::$cacheDir . 'cache_' . $hash . '.dat';
    }

    /**
     * Genera chiave cache da componenti
     *
     * @param string $prefix Prefisso
     * @param array $parts Parti della chiave
     * @return string
     */
    public static function key($prefix, ...$parts)
    {
        return $prefix . ':' . implode(':', array_map(function($part) {
            return is_array($part) ? md5(serialize($part)) : (string)$part;
        }, $parts));
    }
}
