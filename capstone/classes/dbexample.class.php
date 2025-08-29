<?php

    class Dbexample{
        private $hostname = "localhost";
        private $username = "root";
        private $password = "";
        private $dbname = "gba_sample";

        protected function connect(){
            $dsn = 'mysql:hostname=' . $this->hostname . ';dbname=' . $this->dbname;
            $pdo = new PDO($dsn, $this->username, $this->password);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            return $pdo;
        }
    }

?>