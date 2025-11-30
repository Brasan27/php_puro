<?php
class DataBase
{
    // Conexión Azure
    public static function connection()
    {
        // Host/puerto/bd pueden tener valor por defecto,
        // pero permiten ser sobreescritos por variables de entorno.
        $hostname = getenv('DB_HOST') ?: 'serverphplimpio.mysql.database.azure.com';
        $port     = getenv('DB_PORT') ?: '3306';
        $database = getenv('DB_NAME') ?: 'database_php';

        // Usuario y contraseña SIEMPRE deben venir de variables de entorno
        $username = getenv('DB_USER');
        $password = getenv('DB_PASSWORD');

        if ($username === false || $password === false) {
            throw new RuntimeException('Las variables de entorno DB_USER o DB_PASSWORD no están definidas.');
        }

        // Ruta al certificado SSL
        $options = array(
            PDO::MYSQL_ATTR_SSL_CA => __DIR__ . '/../assets/database/DigiCertGlobalRootG2.crt.pem',
        );

        $dsn = "mysql:host=$hostname;port=$port;dbname=$database;charset=utf8";

        $pdo = new PDO($dsn, $username, $password, $options);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $pdo;
    }
}
?>
