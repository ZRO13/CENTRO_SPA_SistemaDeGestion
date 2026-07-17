<?php

class Database
{
    public function conectar(): PDO
    {
        $databaseUrl = getenv('DATABASE_URL');

        if (!$databaseUrl) {
            throw new Exception("Error: La variable de entorno DATABASE_URL no está configurada.");
        }

        $dbParts = parse_url($databaseUrl);

        $host = $dbParts['host'];
        $user = $dbParts['user'];
        $password = $dbParts['pass'];
        $dbname = ltrim($dbParts['path'], '/'); 

        $dsn = "pgsql:host={$host};dbname={$dbname};sslmode=require";

        return new PDO(
            $dsn,
            $user,
            $password,
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false
            ]
        );
    }
}