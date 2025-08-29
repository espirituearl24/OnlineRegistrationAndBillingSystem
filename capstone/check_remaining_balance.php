<?php
session_start();
$remainingBalance = $_SESSION['remainingBalance'];
echo json_encode(['remainingBalance' => $remainingBalance]);
?>