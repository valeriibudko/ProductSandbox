<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use PDO;

final class PdoConnectionFactory
{
//    public static function sqlite(string $path): PDO
//    {
//        $pdo = new PDO('sqlite:' . $path);
//        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//        return $pdo;
//    }

    public static function sqlite(string $path): \PDO
    {
        if (!in_array('sqlite', \PDO::getAvailableDrivers(), true)) {
            $loadedIni = php_ini_loaded_file() ?: '(ini not found)';
            $drivers   = implode(', ', \PDO::getAvailableDrivers());
            throw new \RuntimeException(
                "PDO SQLite driver is not enabled. Loaded php.ini: {$loadedIni}. ".
                "Available PDO drivers: [{$drivers}]. Enable extensions: pdo_sqlite, sqlite3."
            );
        }
        $pdo = new \PDO('sqlite:' . $path);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }

}
