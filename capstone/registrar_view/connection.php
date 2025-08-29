<?php

$servername = 'localhost';
$uname = 'root';
$password = '';
$database = 'capstone';

 $conn = mysqli_connect($servername, $uname, $password, $database);
 
 if(!$conn)
	die("Connection Failed" . mysqli_connect_error());
?>