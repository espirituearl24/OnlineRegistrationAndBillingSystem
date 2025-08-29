<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTC Payment Process</title>
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
    // Get the posted data from hidden fields
    $eventName = isset($_POST['event_name']) ? $_POST['event_name'] : null;
    $eventDate = isset($_POST['event_date']) ? $_POST['event_date'] : null;
    $remarks = isset($_POST['remarks']) ? $_POST['remarks'] : null;
    $paymentDeadlineStart = isset($_POST['payment_deadline_start']) ? $_POST['payment_deadline_start'] : null;
    $paymentDeadlineEnd = isset($_POST['payment_deadline_end']) ? $_POST['payment_deadline_end'] : null;
    $eventFee = isset($_POST['event_fee']) ? $_POST['event_fee'] : null;

    // Prepare the SQL for inserting into payment_events
    $sqlInsertPaymentEvent = "INSERT INTO payment_events (admission_id, event_name, event_date, remarks, payment_deadline_start, payment_deadline_end, event_fee, status, receipt, full_name, reference_number, amount) 
    VALUES (:admission_id, :event_name, :event_date, :remarks, :payment_deadline_start, :payment_deadline_end, :event_fee, 'OTC', NULL, NULL, NULL, NULL)";

    // Prepare and execute the statement
    $statementInsertPaymentEvent = $conn->prepare($sqlInsertPaymentEvent);
    $statementInsertPaymentEvent->execute([
        ':admission_id' => $id,
        ':event_name' => $eventName,
        ':event_date' => $eventDate,
        ':remarks' => $remarks,
        ':payment_deadline_start' => $paymentDeadlineStart,
        ':payment_deadline_end' => $paymentDeadlineEnd,
        ':event_fee' => $eventFee
    ]);

    // Success message
    echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Please Remind of the School hours 7am - 6pm.',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'student1.php'; // Redirect after success
            });
          </script>";
} else {
    // Error message for invalid request method
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