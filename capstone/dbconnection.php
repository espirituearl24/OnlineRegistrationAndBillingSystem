<?php

$dsn = 'mysql:host=localhost;dbname=capstone';
$user = 'root';
$pass = '';

try {
	$conn = new PDO($dsn, $user, $pass);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}catch (PDOException $e){
	echo 'Failed To Connect' . $e->getMessage();
}


?>
