<?php
    class DataBase{
        #  Conexión Local
        // public static function connection(){
        //     $hostname = "localhost";
        //     $port = "3306";
        //     $database = "database_php";
        //     $username = "root";
        //     $password = "";
		// 	$pdo = new PDO("mysql:host=$hostname;port=$port;dbname=$database;charset=utf8",$username,$password);
		// 	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		// 	return $pdo;
		// }
        
        ## Conexión Azure
        public static function connection(){
            $hostname = "server-database.mysql.database.azure.com";
            $port = "3306";
            $database = "database_php";
            $username = "Pipe;
            $password = "Pipe9427*";
            $options = array(
                PDO::MYSQL_ATTR_SSL_CA => 'assets/database/DigiCertGlobalRootG2.crt.pem'
            );
            $pdo = new PDO("mysql:host=$hostname;port=$port;dbname=$database;charset=utf8",$username,$password,$options);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                return $pdo;
        }
        
        ## https://php-limpio-fpeccygaf2czhjbg.canadacentral-01.azurewebsites.net/
	}
?>