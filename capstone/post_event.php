<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Processing</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    

<?php
session_start();
include 'dbconnection.php'; // Ensure your database connection is included

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form data
    $level_from = htmlspecialchars($_POST['level_from']);
    $level_to = htmlspecialchars($_POST['level_to']);
    
    // Define a mapping for grade levels to numeric values
    $grade_map = [
        'Kinder 1' => 1,
        'Kinder 2' => 2,
        'Grade 1' => 3,
        'Grade 2' => 4,
        'Grade 3' => 5, 
        'Grade 4' => 6,
        'Grade 5' => 7,
        'Grade 6' => 8,
        'Grade 7' => 9,
        'Grade 8' => 10,
        'Grade 9' => 11,
        'Grade 10' => 12,
    ];

    // Get numeric values for the selected levels
    $from_num = $grade_map[$level_from];
    $to_num = $grade_map[$level_to];

    // Generate the list of levels in the range
    $levels = [];
    for ($i = $from_num; $i <= $to_num; $i++) {
        // Find the corresponding grade level by numeric value
        $level_name = array_search($i, $grade_map);
        if ($level_name !== false) {
            $levels[] = $level_name;
        }
    }

    // Combine levels into a single string
    $level = implode(',', $levels);

    $event_name = htmlspecialchars($_POST['event_name']);
    $event_fee = $_POST['raw_event_fee'];
    $event_date = htmlspecialchars($_POST['event_date']);
    $payment_deadline_start = htmlspecialchars($_POST['payment_deadline_start']);
    $payment_deadline_end = htmlspecialchars($_POST['payment_deadline_end']);
    $remarks = htmlspecialchars($_POST['remarks']);

    try {
        // Prepare an INSERT statement
        $sql = "INSERT INTO events (level, event_name, event_fee, event_date, payment_deadline_start, payment_deadline_end, remarks) 
                VALUES (:level, :event_name, :event_fee, :event_date, :payment_deadline_start, :payment_deadline_end, :remarks)";
        
        $stmt = $conn->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':level', $level);
        $stmt->bindParam(':event_name', $event_name);
        $stmt->bindParam(':event_fee', $event_fee);
        $stmt->bindParam(':event_date', $event_date);
        $stmt->bindParam(':payment_deadline_start', $payment_deadline_start);
        $stmt->bindParam(':payment_deadline_end', $payment_deadline_end);
        $stmt->bindParam(':remarks', $remarks);

        // Execute the statement
        $stmt->execute();

        // Output success response
        echo "<script>
                Swal.fire({
                    title: 'Success!',
                    text: 'New record created successfully.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = 'events.php'; // Redirect to events.php after clicking OK
                });
              </script>";
        
    } catch (PDOException $e) {
        echo "<script>
                Swal.fire({
                    title: 'Error!',
                    text: 'Error: " . addslashes($e->getMessage()) . "',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
              </script>";
    }
}
?>

</body>
</html>