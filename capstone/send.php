<?php
session_start(); // Start the session at the very top of the file

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_POST["send"])) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'gba.admissions@gmail.com';
        $mail->Password = 'ylvn aiva nbcy jjut'; // Store this securely
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;
 
        $mail->setFrom('gba.admissions@gmail.com', 'Admissions Office');
        $mail->addAddress($_POST["email"]);
        $mail->isHTML(true);

        $mail->Subject = $_POST["subject"];
        $mail->Body = $_POST["message"];

        $mail->send();

        // Set a success notification
        $_SESSION['notification'] = [
            'type' => 'success',
            'message' => 'Email sent successfully!'
        ];
    } catch (Exception $e) {
        // Set an error notification
        $_SESSION['notification'] = [
            'type' => 'error',
            'message' => 'Failed to send email: ' . $mail->ErrorInfo
        ];
    }

    header('Location: admin.php');
    exit();
}
?>
