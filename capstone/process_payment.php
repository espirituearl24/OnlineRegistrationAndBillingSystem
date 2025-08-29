<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Processing</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<?php
// Include your database connection using PDO
session_start(); 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
include('dbconnection.php');
 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form inputs
    $fullName = $_POST['fullName'];
    $referenceNumber = $_POST['referenceNumber'];
    $amount = str_replace(',', '', $_POST['amount']);
    $paymentMode = $_POST['paymentMode']; // Get the payment mode
    $paymentTerms = $_POST['paymentTerms']; // Get the payment terms

    // Validate that 'amount' is numeric
    if (!is_numeric($amount)) {
        echo "<script> 
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid amount',
                    text: 'The amount must be a valid numeric value.'
                }).then(() => {
                    window.history.back(); // Go back to the previous page
                });
              </script>";
        exit();
    }
    
    // Convert 'amount' to a float
    $amount = floatval($amount);

    // Handle image upload
    $image = $_FILES['image'];
    $imageName = $image['name'];
    $imageTmpName = $image['tmp_name'];
    $imageSize = $image['size'];
    $imageError = $image['error'];
    $imageType = $image['type'];
    
    // Validate image upload
    $allowedExtensions = ['jpg', 'jpeg', 'png'];
    $imageExt = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
    
    if (in_array($imageExt, $allowedExtensions) && $imageError === 0 && $imageSize < 5000000) {
        // Set a unique name for the image and move it to the upload folder
        $newImageName = uniqid('', true) . '.' . $imageExt;
        $imageDestination = 'receipts/' . $newImageName;
        
        if (move_uploaded_file($imageTmpName, $imageDestination)) {
            try {
                // Start a transaction
                $conn->beginTransaction();

                // Query to get the latest 'id' from the admission table
                $latestAdmissionSql = "SELECT id FROM admission ORDER BY id DESC LIMIT 1";
                $admissionStmt = $conn->query($latestAdmissionSql);
                $latestAdmissionRow = $admissionStmt->fetch(PDO::FETCH_ASSOC);

                if ($latestAdmissionRow) {
                    $latestAdmissionId = $latestAdmissionRow['id'];

                    // Now insert into the payments table with the foreign key from admission
                    $sql = "INSERT INTO payments (admission_id, fullName, referenceNumber, amount, receiptImage, payment_mode, payment_terms) 
                            VALUES (:admission_id, :fullName, :referenceNumber, :amount, :receiptImage, :paymentMode, :paymentTerms)";
                    $stmt = $conn->prepare($sql);

                    // Bind parameters for payments
                    $stmt->bindValue(':admission_id', $latestAdmissionId); // Bind the foreign key from admission
                    $stmt->bindValue(':fullName', $fullName);
                    $stmt->bindValue(':referenceNumber', $referenceNumber);
                    $stmt->bindValue(':amount', $amount, PDO::PARAM_STR); // Bind 'amount' as a string but treat it as a float
                    $stmt->bindValue(':receiptImage', $newImageName);
                    $stmt->bindValue(':paymentMode', $paymentMode); // Bind payment mode
                    $stmt->bindValue(':paymentTerms', $paymentTerms); // Bind payment terms

// Execute the payment statement

if ($stmt->execute()) {
    // Commit the transaction
    $conn->commit();

    // Fetch the email address from the admission table
    $emailSql = "SELECT emailaddress FROM admission WHERE id = :id";
    $emailStmt = $conn->prepare($emailSql);
    $emailStmt->bindValue(':id', $latestAdmissionId);
    $emailStmt->execute();
    $emailRow = $emailStmt->fetch(PDO::FETCH_ASSOC);

    if ($emailRow) {
        $studentEmail = $emailRow['emailaddress'];

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
            $mail->Subject = 'Payment Confirmation';
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
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>Payment Confirmation</div>
                    <div class='content'>
                        Dear Student,<br><br>
                        Your payment of <span class='highlight'>PHP $amount</span> has been successfully recorded on <span class='highlight'>" . date('m/d/Y') . "</span>. Thank you for your timely payment!<br><br>
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
            $_SESSION['flash_message'] = [
                'type' => 'success',
                'message' => 'Your payment has been recorded successfully. Please give us at least 3-5 working days to validate your payment.'
            ];
        
            // Redirect using PHP header
            header("Location: index.php");
            exit();
        } catch (Exception $e) {
            // Handle email sending error
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }

    // Check if paymentTerms is 'installment' or 'full'
    if ($paymentTerms === 'installment' || $paymentTerms === 'full') {
        // Update the status in the payments table
        $updateStatusSql = "UPDATE payments SET status = 'Enrolled' WHERE admission_id = :admission_id";
        $updateStmt = $conn->prepare($updateStatusSql);
        $updateStmt->bindValue(':admission_id', $latestAdmissionId); // Bind the foreign key from admission

        // Execute the update statement
        if ($updateStmt->execute()) {  
            // Optionally handle success for status update
        } else {
            // Handle error for status update
            echo "Error updating status: " . implode(" | ", $updateStmt->errorInfo());
        } 
    }

    // Success message with SweetAlert and redirect to index.php
    echo "<script>
    Swal.fire({
        icon: 'success',
        title: 'Payment Successful',
        text: 'Your payment has been recorded successfully. <br> Please give us at atleast <strong>3-5
working days<strong> to validate your payment.',
        showConfirmButton: true,
        confirmButtonText: 'OK'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'index.php'; // Redirect to index.php
        }
    });
  </script>";

} else {
    // Rollback the transaction on failure
    $conn->rollBack();
    echo "Error: " . implode(" | ", $stmt->errorInfo());
}
                } else {
                    // Handle case where no admission record exists
                    echo "<script>
                            Swal.fire({
                                icon: 'error',
                                title: 'No Admission Record',
                                text: 'No admission records were found. Please ensure that an admission exists before making a payment.'
                            });
                          </script>";
                }

            } catch (Exception $e) {
                // Rollback the transaction if something goes wrong
                $conn->rollBack();
                echo "Transaction failed: " . $e->getMessage();
            }
        } else {
            echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Upload Error',
                        text: 'Error uploading the file.'
                    });
                  </script>";
        }
    } else {
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid file',
                    text: 'Invalid file type or size. Please upload a valid image.'
                });
              </script>";
    }
}
?>
</body>
</html>