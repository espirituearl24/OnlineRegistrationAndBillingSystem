<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
include('dbconnection.php');

if (isset($_POST['send_email'])) {
    $studentId = $_POST['student_id'];

    // Fetch student details from the database
    $query = "SELECT firstname, lastname, emailaddress FROM enroll WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $studentId);
    $stmt->execute();
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($student) {
        $studentEmail = $student['emailaddress'];
        $fullName = $student['firstname'] . ' ' . $student['lastname'];
// Fetch tuition data for the student
$sql4 = "SELECT `install_monthly` FROM tuition WHERE `grade_level` = (SELECT grade FROM enroll WHERE id = :id)";
$statement4 = $conn->prepare($sql4);
$statement4->bindParam(':id', $studentId);
$statement4->execute();
$tuitionData = $statement4->fetch(PDO::FETCH_ASSOC);

$monthlyDue = $tuitionData ? number_format((float)$tuitionData['install_monthly'], 2, '.', ',') : '0.00';

// Fetch the first created_at date from the payments table
$sql6 = "SELECT MIN(`created_at`) as first_date FROM payments WHERE `admission_id` = :id";
$statement6 = $conn->prepare($sql6);
$statement6->execute([':id' => $studentId]);
$firstPaymentDate = $statement6->fetchColumn();

// Initialize next due date variable
$nextDueDate = '';

if ($firstPaymentDate) {
    // Convert the date to a DateTime object
    $firstDateTime = new DateTime($firstPaymentDate);
    // Calculate the next due date (one month after the first payment date)
    $firstDateTime->modify('+1 month');
    // Format the date as MM/DD/YYYY
    $nextDueDate = $firstDateTime->format('m/d/Y');
} else {
    $nextDueDate = date('m/d/Y', strtotime('+1 month')); // Default to one month from now if no payments found
}
        // Send email notification using PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'gba.admissions@gmail.com'; // Your email
            $mail->Password = 'ylvn aiva nbcy jjut'; // Your email password or app-specific password
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;

            $mail->setFrom('gba.admissions@gmail.com', 'Admissions Office');
            $mail->addAddress($studentEmail); // Use student's email

            $mail->isHTML(true);
            $mail->Subject = 'Monthly Payment Due Notification';
            $mail->Body = "
                <html>
                <head>
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                            line-height: 1.6;
                            color: #333;
                        }
                        .container {
                            max-width: 600px;
                            margin: 0 auto;
                            padding: 20px;
                            border: 1px solid #ddd;
                            border-radius: 8px;
                            background-color: #f9f9f9;
                        }
                        .header {
                            text-align: center;
                            font-size: 18px;
                            font-weight: bold;
                            margin-bottom: 20px;
                            color: #0056b3;
                        }
                        .content {
                            font-size: 16px;
                        }
                        .footer {
                            margin-top: 20px;
                            font-size: 14px;
                            color: #555;
                        }
                        .highlight {
                            font-weight: bold;
                            color: #d9534f;
                        }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <div class='header'>Monthly Payment Due Notification</div>
                        <div class='content'>
                            Dear $fullName,<br><br>
                            This is a gentle reminder that your monthly payment of 
                            <span class='highlight'>PHP $monthlyDue</span> is due on 
                            <span class='highlight'>$nextDueDate</span>.<br><br>
                            Please ensure timely payment to avoid any inconvenience.<br><br>
                            Should you have any questions or concerns, feel free to contact our office.<br><br>
                        </div>
                        <div class='footer'>
                            Best regards,<br>
                            Grace Baptist Academy Admissions Office
                        </div>
                    </div>
                </body>
                </html>";
            $mail->send();

            $_SESSION['error'] = "Email sent successfully!";
            $_SESSION['status'] = "success";
            $_SESSION['title'] = "Success!";
        } catch (Exception $e) {
            $_SESSION['error'] = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            $_SESSION['status'] = "error";
            $_SESSION['title'] = "Email Error!";
        }
    } else {
        $_SESSION['error'] = "Student not found.";
        $_SESSION['status'] = "error";
        $_SESSION['title'] = "Error!";
    }

    header('Location: registrar_dues.php'); // Redirect back to your dues page
    exit();
}
?>