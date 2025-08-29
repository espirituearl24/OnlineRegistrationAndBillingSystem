<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Payment Process</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
</head>
<body>
<?php
session_start();
include 'dbconnection.php';

// Check if the session variable 'id' is set
if (!isset($_SESSION['id'])) {
    echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Session ID is not set.',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'your_redirect_page.php'; // Redirect if needed
            });
          </script>";
    exit;
}

$id = $_SESSION['id'];

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the posted data
    $fullName = $_POST['fullName'];
    $referenceNumber = $_POST['referenceNumber'];
    $amount = $_POST['amount'];

    // Convert and format the dates
    $eventDate = DateTime::createFromFormat('F j, Y', $_POST['event_date']);
    $paymentDeadlineStart = DateTime::createFromFormat('F j, Y', $_POST['payment_deadline_start']);
    $paymentDeadlineEnd = DateTime::createFromFormat('F j, Y', $_POST['payment_deadline_end']);

    // Ensure the dates are in the proper format for MySQL
    $formattedEventDate = $eventDate ? $eventDate->format('Y-m-d') : null;
    $formattedPaymentDeadlineStart = $paymentDeadlineStart ? $paymentDeadlineStart->format('Y-m-d') : null;
    $formattedPaymentDeadlineEnd = $paymentDeadlineEnd ? $paymentDeadlineEnd->format('Y-m-d') : null;

    // Format the amount as a float (if necessary)
    $formattedAmount = floatval(str_replace(',', '', $amount));

    // Handle the file upload for the receipt
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "e-receipts/"; // Directory where the file will be uploaded
        $fileName = basename($_FILES['image']['name']);
        $targetFilePath = $targetDir . $fileName;

        // Check if the directory exists, if not, create it
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
            // Prepare the SQL for inserting into payment_events
            $sqlInsertPaymentEvent = "INSERT INTO payment_events (admission_id, event_name, event_date, remarks, payment_deadline_start, payment_deadline_end, event_fee, status, receipt, full_name, reference_number, amount) 
            VALUES (:admission_id, :event_name, :event_date, :remarks, :payment_deadline_start, :payment_deadline_end, :event_fee, 'Paid', :receipt, :full_name, :reference_number, :amount)";

            // Prepare and execute the statement
            $statementInsertPaymentEvent = $conn->prepare($sqlInsertPaymentEvent);
            $statementInsertPaymentEvent->execute([
                ':admission_id' => $id,
                ':event_name' => $_POST['event_name'],
                ':event_date' => $formattedEventDate,
                ':remarks' => $_POST['remarks'],
                ':payment_deadline_start' => $formattedPaymentDeadlineStart,
                ':payment_deadline_end' => $formattedPaymentDeadlineEnd,
                ':event_fee' => $formattedAmount,
                ':receipt' => $fileName,
                ':full_name' => $fullName,
                ':reference_number' => $referenceNumber,
                ':amount' => $formattedAmount
            ]);

            echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Payment has been processed successfully.',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = 'student1.php'; // Redirect after success
                    });
                  </script>";
        } else {
            echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Failed to upload the receipt.',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = 'student1.php'; // Redirect if needed
                    });
                  </script>";
        }
    } else {
 echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'No receipt uploaded or there was an upload error.',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = 'student1.php'; // Redirect if needed
                });
              </script>";
    }
} else {
    echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Invalid request method.',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'student1.php'; // Redirect if needed
            });
          </script>";
}
?>
    
</body>
</html>
