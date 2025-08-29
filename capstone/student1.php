<?php

 
session_start();

include 'dbconnection.php';

// Check if the session variable 'id' is set
if (isset($_SESSION['id'])) {
    $id = $_SESSION['id'];
    echo "Session ID: " . htmlspecialchars($id); // Display the session ID safely
} else {
    echo "Session ID is not set.";
}
  
$userl = $_SESSION['lastname'];
$userf = $_SESSION['firstname'];

$sql = "SELECT `LRN` FROM enroll WHERE `firstname` = '$userf' AND `lastname` = '$userl'";
$statement = $conn->prepare($sql);
$statement->execute();
$lrn = $statement->fetchColumn();

$sql2 = "SELECT `grade` FROM enroll WHERE `firstname` = '$userf' AND `lastname` = '$userl'";
$statement2 = $conn->prepare($sql2);
$statement2->execute();
$grade = $statement2->fetchColumn(); 

// Fetch Payments
$sql3 = "SELECT * FROM payments WHERE `admission_id` = :id";
$statement3 = $conn->prepare($sql3);
$statement3->execute([':id' => $id]);
$paymentData = $statement3->fetch(PDO::FETCH_ASSOC);

if ($paymentData !== false) {
    // Check payment terms
    if ($paymentData['payment_terms'] === 'installment') {
        // Fetch Tuition based on Grade Level
        $sql4 = "SELECT `install_total` FROM tuition WHERE `grade_level` = :grade";
        $statement4 = $conn->prepare($sql4);
        $statement4->execute([':grade' => $grade]);
        $tuitionData = $statement4->fetch(PDO::FETCH_ASSOC);

        $sql5 = "SELECT `install_monthly` FROM tuition WHERE `grade_level` = :grade";
        $statement5 = $conn->prepare($sql5);
        $statement5->execute([':grade' => $grade]);
        $tuitionData2 = $statement5->fetch(PDO::FETCH_ASSOC);

        if ($tuitionData !== false) {
            // Set the total amount to the install_total
            $totalAmount = number_format((float)$tuitionData['install_total'], 2, '.', ',');
        } else {
            echo "No tuition data found for grade: " . htmlspecialchars($grade);
        }

        if ($tuitionData2 !== false) {
            // Set the total amount to the install_total
            $monthlyInstall = number_format((float)$tuitionData2['install_monthly'], 2, '.', ',');
        } else {
            echo "No tuition data found for grade: " . htmlspecialchars($grade);
        }
    } else {
        // Handle case for full payment or other payment terms
        $totalAmount = number_format((float)$paymentData['full_total'], 2, '.', ',');
    }
} else {
    echo "No payment data found.";
}



// Fetch Tuition based on Grade Level
$sql4 = "SELECT * FROM tuition WHERE `grade_level` = :grade";
$statement4 = $conn->prepare($sql4);
$statement4->execute([':grade' => $grade]);
$tuitionData = $statement4->fetch(PDO::FETCH_ASSOC);

if ($tuitionData !== false) {
    // // Tuition data exists, you can access it here
    // echo "<pre>"; // Optional: format output for better readability
    // print_r($tuitionData); // Display the fetched tuition data
    // echo "</pre>";
} else {
    // Tuition data does not exist
    echo "No tuition data found for grade: " . htmlspecialchars($grade);
}


// Fetch the latest payment information
$sql3 = "SELECT * FROM payments WHERE `admission_id` = :id ORDER BY `created_at` DESC LIMIT 1";
$statement3 = $conn->prepare($sql3);
$statement3->execute([':id' => $id]);
$latestPaymentData = $statement3->fetch(PDO::FETCH_ASSOC);

if ($latestPaymentData !== false) {
    // Check if the due_date is NULL or empty
    if (empty($latestPaymentData['due_date'])) {
        // If due_date is NULL or empty, use created_at to determine due dates
        $latestPaymentDate = $latestPaymentData['created_at'];
    } else {
        // If due_date exists, use it
        $latestPaymentDate = $latestPaymentData['due_date'];
    }

    // Convert the date to a DateTime object
    $latestDateTime = new DateTime($latestPaymentDate);
    
    // Initialize an array to hold due dates
    $dueDates = [];

    // Generate due dates for the next 9 months
    for ($i = 1; $i <= 9; $i++) {
        // Clone the latest date and add one month
        $nextDueDate = clone $latestDateTime;
        $nextDueDate->modify("+{$i} month");
        
        // Format the date as MM/DD/YYYY
        $formattedDueDate = $nextDueDate->format('m/d/Y');
        $dueDates[] = $formattedDueDate;
    }
} else {
    echo "No payments found.";
}





// After fetching the user's payments
// Fetch all payments made by the user
$sql7 = "SELECT `amount` FROM payments WHERE `admission_id` = :id";
$statement7 = $conn->prepare($sql7);
$statement7->execute([':id' => $id]);

// Initialize a variable to hold the total amount paid
$totalPaid = 0;

// Fetch the amounts and sum them up
while ($row = $statement7->fetch(PDO::FETCH_ASSOC)) {
    $totalPaid += (float)$row['amount']; // Cast to float for accurate summation
} 

// Format the total amount paid
$totalPaidFormatted = number_format($totalPaid, 2, '.', ',');

// Now you can use $totalPaidFormatted in your HTML to display the total amount paid


// Convert $totalAmount to float for calculation
$totalAmountFloat = (float)str_replace(',', '', $totalAmount); // Remove commas for conversion

// Calculate the remaining balance
$remainingBalance = max(0, $totalAmountFloat - $totalPaid); // Ensure balance does not go negative

$showDueDates = $remainingBalance > 0; // Determine if there is a balance to show due dates

// Fetch payment history
$sql8 = "SELECT `payment_mode`, `amount`, `created_at`, `discount` FROM payments WHERE `admission_id` = :id";
$statement8 = $conn->prepare($sql8);
$statement8->execute([':id' => $id]);

// Initialize an array to hold payment history
$paymentHistory = [];

// Fetch the payment history
while ($row = $statement8->fetch(PDO::FETCH_ASSOC)) {
    // Add the original payment record
    $paymentHistory[] = [
        'payment_mode' => strtoupper($row['payment_mode']), // Convert payment mode to uppercase
        'amount' => number_format((float)$row['amount'], 2, '.', ','), // Format amount
        'created_at' => date('m/d/Y', strtotime($row['created_at'])), // Format date
    ];
    
}

if ($latestPaymentDate) {
    $latestDateTime = new DateTime($latestPaymentDate);
    $nextDueDate = clone $latestDateTime;
    $nextDueDate->modify("+1 month"); // Assuming monthly payments

    // Get today's date
    $today = new DateTime();
    $interval = $today->diff($nextDueDate);
    
    // Check if the next due date is within the next 7 days
    if ($interval->days <= 7 && $interval->invert == 0) {
        // Fetch payment reminder details
        $sqlReminder = "SELECT `payment_mode`, `amount`, `created_at` FROM payments WHERE `admission_id` = :id ORDER BY `created_at` DESC LIMIT 1";
        $statementReminder = $conn->prepare($sqlReminder);
        $statementReminder->execute([':id' => $id]);
        
        $paymentReminder = $statementReminder->fetch(PDO::FETCH_ASSOC);
    }
}

// Generate due dates for the next 9 months along with the monthly installment
if ($latestPaymentDate) {
    for ($i = 1; $i <= 9; $i++) {
        $nextDueDate = clone $latestDateTime;
        $nextDueDate->modify("+{$i} month");
        $formattedDueDate = $nextDueDate->format('m/d/Y');
        
        // Convert monthly installment to float
        $monthlyInstallFloat = (float)str_replace(',', '', $monthlyInstall);
        
        // Determine the installment amount
        if ($remainingBalance < $monthlyInstallFloat) {
            // If remaining balance is less than monthly installment, use remaining balance
            $installmentAmount = number_format($remainingBalance, 2, '.', ',');
        } else {
            // Otherwise, use the standard monthly installment
            $installmentAmount = $monthlyInstall;
        }
        
        $installmentsWithDueDates[] = [
            'due_date' => $formattedDueDate,
            'monthly_install' => $installmentAmount
        ];
    }
}
// Initialize an array to hold missed payments
$missedPayments = [];

// Get today's date
$today = new DateTime();

// Check for missed payments
foreach ($dueDates as $dueDate) {
    // Convert the due date to Y-m-d format for comparison
    $formattedDueDateForDB = DateTime::createFromFormat('m/d/Y', $dueDate)->format('Y-m-d');

    // Check if this due date exists in the payments table
    $sqlMissed = "SELECT COUNT(*) FROM payments WHERE `admission_id` = :id AND DATE(`created_at`) = :due_date";
    $statementMissed = $conn->prepare($sqlMissed);
    $statementMissed->execute([':id' => $id, ':due_date' => $formattedDueDateForDB]);
    $count = $statementMissed->fetchColumn();

    // If no payment is found for this due date and it's past today, add it to missed payments
    if ($count == 0 && new DateTime($formattedDueDateForDB) < $today) {
        $missedPayments[] = $dueDate; // Store missed due date
    }
}

// Assuming you have already fetched the user's grade level
// Fetch events based on the user's grade level
// Modify the events query to handle multiple grade levels
$sqlEvents = "SELECT * FROM events WHERE FIND_IN_SET(:grade, `level`) > 0";
$statementEvents = $conn->prepare($sqlEvents);
$statementEvents->execute([':grade' => $grade]);
$events = $statementEvents->fetchAll(PDO::FETCH_ASSOC);
// Check for AJAX request to reject an event
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['event_id'])) {
    $eventId = $_POST['event_id'];

    // Fetch the event details from the events table
    $sqlFetchEvent = "SELECT * FROM events WHERE id = :event_id";
    $statementFetchEvent = $conn->prepare($sqlFetchEvent);
    $statementFetchEvent->execute([':event_id' => $eventId]);
    $event = $statementFetchEvent->fetch(PDO::FETCH_ASSOC);

    if ($event) {
        // Prepare the SQL for inserting into payment_events
        $sqlInsertPaymentEvent = "INSERT INTO payment_events (admission_id, event_name, event_date, remarks, payment_deadline_start, payment_deadline_end, event_fee, status) VALUES (:admission_id, :event_name, :event_date, :remarks, :payment_deadline_start, :payment_deadline_end, :event_fee, 'Rejected')";
        $statementInsertPaymentEvent = $conn->prepare($sqlInsertPaymentEvent);
        
        // Bind parameters and execute the insert
        $statementInsertPaymentEvent->execute([
            ':admission_id' => $id, // Include the admission_id
            ':event_name' => $event['event_name'],
            ':event_date' => $event['event_date'],
            ':remarks' => $event['remarks'],
            ':payment_deadline_start' => $event['payment_deadline_start'],
            ':payment_deadline_end' => $event['payment_deadline_end'],
            ':event_fee' => (float)$event['event_fee'] // Ensure event_fee is treated as a float
        ]);

        // Optionally, you can update the status of the original event if needed
        $sqlUpdateEventStatus = "UPDATE events SET status = 'Rejected' WHERE id = :event_id";
        $statementUpdateEventStatus = $conn->prepare($sqlUpdateEventStatus);
        $statementUpdateEventStatus->execute([':event_id' => $eventId]);

        // Return a success response
        echo json_encode(['success' => true, 'message' => 'Event has been rejected and recorded in payment_events.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Event not found.']);
    }
    exit; // Stop further execution
}


// Query to check the status of the admission_id in payment_events
$sqlCheckStatus = "SELECT status FROM payment_events WHERE admission_id = :admission_id";
$statementCheckStatus = $conn->prepare($sqlCheckStatus);
$statementCheckStatus->execute([':admission_id' => $id]);
$paymentEventStatus = $statementCheckStatus->fetchColumn();

// Check if the user has already paid

// Fetch Discounts
$sqlDiscounts = "SELECT `discount` FROM payments WHERE `admission_id` = :id";
$statementDiscounts = $conn->prepare($sqlDiscounts);
$statementDiscounts->execute([':id' => $id]);

// Initialize variable for maximum discount percentage
$maxDiscountPercentage = 0;

// Fetch the maximum discount percentage
while ($row = $statementDiscounts->fetch(PDO::FETCH_ASSOC)) {
    $discountPercentage = (float)$row['discount']; // Cast to float for accurate calculation
    if ($discountPercentage > $maxDiscountPercentage) {
        $maxDiscountPercentage = $discountPercentage; // Store the maximum discount percentage
    }
}

// Convert $totalAmount to float for calculation
$totalAmountFloat = (float)str_replace(',', '', $totalAmount); // Remove commas for conversion

// Calculate the discount amount
$discountAmount = $totalAmountFloat * ($maxDiscountPercentage / 100);

// Calculate the discounted total amount
$discountedTotalAmount = max(0, $totalAmountFloat - $discountAmount); // Ensure discounted total does not go negative

// Format the discounted total amount for display
$discountedTotalFormatted = number_format($discountedTotalAmount, 2, '.', ',');

// Calculate the remaining balance using the discounted total amount
$remainingBalance = max(0, $discountedTotalAmount - $totalPaid); // Ensure balance does not go negative

// Format the remaining balance for display
$remainingBalanceFormatted = number_format($remainingBalance, 2, '.', ',');

// Calculate the discounted monthly installment
if ($paymentData['payment_terms'] === 'installment' && $tuitionData2 !== false) {
    $monthlyInstallFloat = (float)str_replace(',', '', $tuitionData2['install_monthly']); // Remove commas for conversion
    $discountedMonthlyInstall = max(0, $monthlyInstallFloat - ($monthlyInstallFloat * ($maxDiscountPercentage / 100))); // Apply discount
    $monthlyInstall = number_format($discountedMonthlyInstall, 2, '.', ','); // Format for display
} else {
    $monthlyInstall = number_format((float)$tuitionData2['install_monthly'], 2, '.', ','); // No discount applied
}

// Initialize an array to hold due dates and monthly installment amounts
$installmentsWithDueDates = [];

// Generate due dates for the next 9 months along with the discounted monthly installment
// Generate due dates for the next 9 months along with the monthly installment
if ($latestPaymentDate) {
    for ($i = 1; $i <= 9; $i++) {
        $nextDueDate = clone $latestDateTime;
        $nextDueDate->modify("+{$i} month");
        $formattedDueDate = $nextDueDate->format('m/d/Y');
        
        // Convert monthly installment to float
        $monthlyInstallFloat = (float)str_replace(',', '', $monthlyInstall);
        
        // Determine the installment amount
        if ($remainingBalance < $monthlyInstallFloat) {
            // If remaining balance is less than monthly installment, use remaining balance
            $installmentAmount = number_format($remainingBalance, 2, '.', ',');
        } else {
            // Otherwise, use the standard monthly installment
            $installmentAmount = $monthlyInstall;
        }
        
        $installmentsWithDueDates[] = [
            'due_date' => $formattedDueDate,
            'monthly_install' => $installmentAmount
        ];
    }
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>GBA | Student</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

<link rel="shortcut icon" type="image/x-icon" href="img/logo1.png">

<link href="../../../../css?family=Roboto:300,400,500,700,900" rel="stylesheet">

<link rel="stylesheet" href="assets/css/bootstrap.min.css">

<link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css">
<link rel="stylesheet" href="assets/plugins/fontawesome/css/fontawesome.min.css">

<link rel="stylesheet" href="assets/css/fullcalendar.min.css">

<link rel="stylesheet" href="assets/css/dataTables.bootstrap4.min.css">

<link rel="stylesheet" href="assets/plugins/morris/morris.css">

<link rel="stylesheet" href="assets/css/style.css">
<!--[if lt IE 9]>
    <script src="assets/js/html5shiv.min.js"></script>
    <script src="assets/js/respond.min.js"></script>
  <![endif]-->
</head>
<body class="">
    

<div class="main-wrapper ">
    

<div class="header-outer">
<div class="header">
    
<a id="mobile_btn" class="mobile_btn float-left" href="#sidebar"><i class="fas fa-bars" aria-hidden="true"></i></a>
<a id="toggle_btn" class="float-left" href="javascript:void(0);">
<img src="assets/img/sidebar/icon-21.png" alt="">
</a>

<ul class="nav float-left ">
<li>
<div class="top-nav-search">
<!-- Header Session Name -->
<form action="inbox.html">
    <h3 class="mt-3"><b class=""><?php echo $userl . ", "; echo $userf; ?></h3>  
</form>
</div>
</li>
<li>
<a href="#" class="mobile-logo d-md-block d-lg-none d-block"><img src="img/logo1.png" alt="" width="30" height="30"></a>
</li>
</ul>

<ul class="nav user-menu float-right">

 <li class="nav-item dropdown has-arrow">
<a href="#" class=" nav-link user-link" data-toggle="dropdown">
<span class=" ">  
  </span>
<span> <?php echo $userl ?> </span>
</a>
<div class="dropdown-menu">
<a class="dropdown-item" href="#"> </a>
<a class="dropdown-item" href="#"> </a>
<a class="dropdown-item" href="studentpass.php?lastname=<?php echo $userl; ?>&firstname=<?php echo $userf; ?>">Change Password</a>
<a class="dropdown-item" href="logout.php" onclick="return confirm('Are you sure you want to logout?');">Logout</a>
</div>
</li>
</ul>
<div class="dropdown mobile-user-menu float-right"> 
<a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></a>
<div class="dropdown-menu dropdown-menu-right">
<a class="dropdown-item" href="#"> </a>
<a class="dropdown-item" href="#"> </a>
<a class="dropdown-item" href="studentpass.php?lastname=<?php echo $userl; ?>&firstname=<?php echo $userf; ?>">Change Password</a>
<a class="dropdown-item" href="nbox.html" >Logout</a>
</div>
</div>
</div>
</div>

<!-----------SIDE BAR--------------------------------->
<div class="sidebar" id="sidebar">
<div class="sidebar-inner slimscroll">
<div id="sidebar-menu" class="sidebar-menu">
<div class="header-left">
<a href="#" class="logo">
 <img src="img/logo1.png" width="60" height="60" alt="" class="mt-3"> 
<span class="text-uppercase ms-2 mt-5"></span>
</a>
    <div class="card-header"><br>
        <div class="page-title"> </div><br>
    </div>
    <div class="">
            LRN:<?php echo $lrn; ?><br>
            Grade Level: <?php echo $grade; ?><br><br>
        </div>
</div>

<!-- <li class="active">
<a href=""><img src="assets/img/sidebar/icon-1.png" alt="icon"><span>Dashboard</span></a>
</li>
<li class="">
  <a href="tadmission.php"><img src="assets/img/sidebar/icon-2.png" alt="icon"> <span> Admission</span> <span class=""></span></a>
</li>
<li class="">
  <a href="tstudent.php"><img src="assets/img/sidebar/icon-10.png" alt="icon"> <span> Students</span> <span class=""></span></a>
</li>
<li class="submenu">
  <a href="#"><img src="assets/img/sidebar/icon-3.png" alt="icon"><span> Archive </span> <span class="menu-arrow"></span></a>
    <ul class="list-unstyled" style="display: none;">
    <li><a href="tarchive_admission.php"><span>Admission</span></a></li>
    <li><a href="tarchive_estudent.php"><span>Enrolled</span></a></li>
  </ul>
</li>
<li class="submenu">
  <a href="#"><img src="assets/img/sidebar/icon-4.png" alt="icon"> <span> Accounts</span> <span class=""></span></a>
</li>
<li class="submenu">
<a href="javascript:void(0);"><img src="assets/img/sidebar/icon-11.png" alt="icon"> <span> Payments</span> <span class="menu-arrow"></span></a>
<ul style="display: none;">
<li>
  <a href="#">List and Due <span class="badge badge-pill bg-primary float-right">5</span></a>
</li>
<li>
  <a href="#">Missed<span class="badge badge-pill bg-primary float-right">5</span></a>
</li>
<li><a href="#"><span>History</span></a></li>
</ul>
</li> -->
<!-- <li>
<a href="inbox.html"><img src="assets/img/sidebar/icon-6.png" alt="icon"> <span>--</span></a>
</li>
<li>
<a href="inbox.html"><img src="assets/img/sidebar/icon-7.png" alt="icon"> <span>--</span></a>
</li>
<li>
<a href="inbox.html"><img src="assets/img/sidebar/icon-8.png" alt="icon"> <span>--</span></a>
</li>
<li>
<a href="inbox.html"><img src="assets/img/sidebar/icon-9.png" alt="icon"><span> --</span></a>
</li>

<li class="submenu">
<a href="#"><img src="assets/img/sidebar/icon-12.png" alt="icon"> <span> Blog</span> <span class="menu-arrow"></span></a>
<ul class="list-unstyled" style="display: none;">
<li><a href="inbox.html"><span>Blog</span></a></li>
<li><a href="inbox.html"><span>Blog View</span></a></li>
<li><a href="inbox.html"><span>Add Blog</span></a></li>
<li><a href="inbox.html"><span>Edit Blog</span></a></li>
</ul>
</li>
<li class="submenu">
<a href="javascript:void(0);" class="noti-dot"><img src="assets/img/sidebar/icon-13.png" alt="icon"> <span>Management </span> <span class="menu-arrow"></span></a>
<ul style="display: none;">
<li class="submenu">
<a href="#"><span> Employees</span> <span class="menu-arrow"></span></a>
<ul class="list-unstyled" style="display: none;">
<li><a href="inbox.html"><span>All Employees</span></a></li>
<li><a href="inbox.html"><span>Holidays</span></a></li>
<li><a href="inbox.html"><span>Leave Requests</span> <span class="badge badge-pill bg-primary float-right">1</span></a></li>
<li><a href="inbox.html"><span>Attendance</span></a></li>
<li><a href="inbox.html"><span>Departments</span></a></li>
<li><a href="inbox.html"><span>Designations</span></a></li>
</ul>
</li>
<li>
<a href="#"><span>Activities</span></a>
</li>
<li>
<a href="inbox.html"><span>Users</span></a>
</li>
<li class="submenu">
<a href="#"><span> Reports </span> <span class="menu-arrow"></span></a>
<ul class="list-unstyled" style="display: none;">
<li><a href="inbox.html"> <span>Expense Report </span></a></li>
<li><a href="inbox.html"> <span>Invoice Report</span> </a></li>
</ul>
</li>
</ul>
</li>
<li>
<a href="inbox.html"><img src="assets/img/sidebar/icon-14.png" alt="icon"> <span>Change Password</span></a>
</li>
<li class="menu-title">UI Elements</li>
<li class="submenu">
<a href="#"><img src="assets/img/sidebar/icon-15.png" alt="icon"> <span> Components</span> <span class="menu-arrow"></span></a>
<ul class="list-unstyled" style="display: none;">
<li><a href="inbox.html"><span>UI Kit</span></a></li>
<li><a href="inbox.html"><span>Typography</span></a></li>
<li><a href="inbox.html"><span>Tabs</span></a></li>
</ul>
</li>
<li class="submenu">
<a href="#"><img src="assets/img/sidebar/icon-26.png" alt="icon"> <span> Elements</span> <span class="menu-arrow"></span></a>
<ul class="list-unstyled" style="display: none;">
<li><a href="inbox.html">Sweet Alerts</a></li>
<li><a href="inbox.html">Tooltip</a></li>
<li><a href="inbox.html">Popover</a></li>
<li><a href="inbox.html">Ribbon</a></li>
<li><a href="inbox.html">Clipboard</a></li>
<li><a href="inbox.html">Drag & Drop</a></li>
<li><a href="inbox.html">Range Slider</a></li>
<li><a href="inbox.html">Rating</a></li>
<li><a href="inbox.html">Toastr</a></li>
<li><a href="inbox.html">Text Editor</a></li>
<li><a href="inbox.html">Counter</a></li>
<li><a href="inbox.html">Scrollbar</a></li>
<li><a href="inbox.html">Spinner</a></li>
<li><a href="inbox.html">Notification</a></li>
<li><a href="inbox.html">Lightbox</a></li>
<li><a href="inbox.html">Sticky Note</a></li>
<li><a href="inbox.html">Timeline</a></li>
<li><a href="inbox.html">Horizontal Timeline</a></li>
<li><a href="inbox.html">Form Wizard</a></li>
</ul>
</li>
<li class="submenu">
<a href="#"><img src="assets/img/sidebar/icon-27.png" alt="icon"> <span> Chart</span> <span class="menu-arrow"></span></a>
<ul class="list-unstyled" style="display: none;">
<li><a href="inbox.html">Apex Charts</a></li>
<li><a href="inbox.html">Chart Js</a></li>
<li><a href="inbox.html">Morris Charts</a></li>
<li><a href="inbox.html">Flot Charts</a></li>
<li><a href="inbox.html">Peity Charts</a></li>
<li><a href="inbox.html">C3 Charts</a></li>
</ul>
</li> -->


</div>
</div>
</div>
<!---------------SIDE BAR END---------------->

<div class="page-wrapper">
<div class="content container-fluid">

<div class="page-header">
<div class="">
<div class=""> 
<h3 class="page-title mb-0">Payment Account</h3><br>
        <div class="row">
            <div class="col mt-3 ">
                Total Amount<br><br>
                    <div>
                        Tuition<br>
                        Miscellaneous<br>
                        Books<br>
                        Registration<br>
                        Tech Fees<br>
                        Infection Controll<br>
                    </div>
            </div>
            <div class="col mt-3">
            <b class="text-warning"><?php echo  $discountedTotalFormatted; ?></b><br><br>
            <div>
                        9,000.00<br>
                        8,150.00<br>
                        4,000.00<br>
                        1,500.00<br>
                        3,000.00<br>
                    <br>
                    </div>
            </div>
            <div class="col ">
                Total Amount Paid
                    <div>
                        <br>
                        Balance
                   <br>
         <br>
                        Discount
                        <br>
                        <br>
                        <br>
                    </div>
            </div>
            <div class="col ">
                <b class="text-warning"><?php echo $totalPaidFormatted  ?></b>
                    <div><br>
                        <b class="text-warning"><?php echo $remainingBalanceFormatted  ?></b>
                        <br>
                        <br>
                        <b class="text-warning"><?php echo " ".$discountPercentage; echo "%";  ?></b>
                        <br>
                        <br>
                        <br>
                    </div>
            </div>
        </div>


</div>





<div class="notification-box">
<div class="msg-sidebar notifications msg-noti">
<div class="topnav-dropdown-header">
<span>Messages</span>
</div>
<div class="drop-scroll msg-list-scroll">
<ul class="list-box">
<li>
<a href="#">
<div class="list-item">
<div class="list-left">
<span class="avatar">R</span>
</div>
<div class="list-body">
<span class="message-author">Richard Miles </span>
<span class="message-time">12:28 AM</span>
<div class="clearfix"></div>
<span class="message-content">Lorem ipsum dolor sit amet, consectetur adipiscing</span>
</div>
</div>
</a>
</li>
<li>
<a href="#">
<div class="list-item new-message">
<div class="list-left">
<span class="avatar">J</span>
</div>
<div class="list-body">
<span class="message-author">Ruth C. Gault</span>
<span class="message-time">1 Aug</span>
<div class="clearfix"></div>
<span class="message-content">Lorem ipsum dolor sit amet, consectetur adipiscing</span>
</div>
</div>
</a>
</li>
<li>
<a href="#">
<div class="list-item">
<div class="list-left">
<span class="avatar">T</span>
</div>
<div class="list-body">
<span class="message-author"> Tarah Shropshire </span>
<span class="message-time">12:28 AM</span>
<div class="clearfix"></div>
<span class="message-content">Lorem ipsum dolor sit amet, consectetur adipiscing</span>
</div>
</div>
</a>
</li>
<li>
<a href="#">
<div class="list-item">
<div class="list-left">
<span class="avatar">M</span>
</div>
<div class="list-body">
<span class="message-author">Mike Litorus</span>
<span class="message-time">12:28 AM</span>
<div class="clearfix"></div>
<span class="message-content">Lorem ipsum dolor sit amet, consectetur adipiscing</span>
</div>
</div>
</a>
</li>
<li>
<a href="#">
<div class="list-item">
<div class="list-left">
<span class="avatar">C</span>
</div>
<div class="list-body">
<span class="message-author"> Catherine Manseau </span>
<span class="message-time">12:28 AM</span>
<div class="clearfix"></div>
<span class="message-content">Lorem ipsum dolor sit amet, consectetur adipiscing</span>
</div>
</div>
</a>
</li>
<li>
<a href="#">
<div class="list-item">
<div class="list-left">
<span class="avatar">D</span>
</div>
<div class="list-body">
<span class="message-author"> Domenic Houston </span>
<span class="message-time">12:28 AM</span>
<div class="clearfix"></div>
<span class="message-content">Lorem ipsum dolor sit amet, consectetur adipiscing</span>
</div>
</div>
</a>
</li>
<li>
<a href="#">
<div class="list-item">
<div class="list-left">
<span class="avatar">B</span>
</div>
<div class="list-body">
<span class="message-author"> Buster Wigton </span>
<span class="message-time">12:28 AM</span>
<div class="clearfix"></div>
<span class="message-content">Lorem ipsum dolor sit amet, consectetur adipiscing</span>
</div>
</div>
</a>
</li>
<li>
<a href="#">
<div class="list-item">
<div class="list-left">
<span class="avatar">R</span>
</div>
<div class="list-body">
<span class="message-author"> Rolland Webber </span>
<span class="message-time">12:28 AM</span>
<div class="clearfix"></div>
<span class="message-content">Lorem ipsum dolor sit amet, consectetur adipiscing</span>
</div>
</div>
</a>
</li>
<li>
<a href="#">
<div class="list-item">
<div class="list-left">
<span class="avatar">C</span>
</div>
<div class="list-body">
<span class="message-author"> Claire Mapes </span>
<span class="message-time">12:28 AM</span>
<div class="clearfix"></div>
<span class="message-content">Lorem ipsum dolor sit amet, consectetur adipiscing</span>
</div>
</div>
</a>
</li>
<li>
<a href="#">
<div class="list-item">
<div class="list-left">
<span class="avatar">M</span>
</div>
<div class="list-body">
<span class="message-author">Melita Faucher</span>
<span class="message-time">12:28 AM</span>
<div class="clearfix"></div>
<span class="message-content">Lorem ipsum dolor sit amet, consectetur adipiscing</span>
</div>
</div>
</a>
</li>
<li>
<a href="#">
<div class="list-item">
<div class="list-left">
<span class="avatar">J</span>
</div>
<div class="list-body">
<span class="message-author">Jeffery Lalor</span>
<span class="message-time">12:28 AM</span>
<div class="clearfix"></div>
<span class="message-content">Lorem ipsum dolor sit amet, consectetur adipiscing</span>
</div>
</div>
</a>
</li>
<li>
<a href="#">
<div class="list-item">
<div class="list-left">
<span class="avatar">L</span>
</div>
<div class="list-body">
<span class="message-author">Loren Gatlin</span>
<span class="message-time">12:28 AM</span>
<div class="clearfix"></div>
<span class="message-content">Lorem ipsum dolor sit amet, consectetur adipiscing</span>
</div>
</div>
</a>
</li>
<li>
<a href="#">
<div class="list-item">
<div class="list-left">
<span class="avatar">T</span>
</div>
<div class="list-body">
<span class="message-author">Tarah Shropshire</span>
<span class="message-time">12:28 AM</span>
<div class="clearfix"></div>
<span class="message-content">Lorem ipsum dolor sit amet, consectetur adipiscing</span>
</div>
</div>
</a>
</li>
</ul>
</div>
<div class="topnav-dropdown-footer">
<a href="#">See all messages</a>
</div>
</div>
</div>
</div>
</div>






<div class="row">
    <div class="col-12 col-md-4 mb-3">
        <div class="card">
            <div class="card-header">
                <div class="page-title">Payment Reminder</div>
            </div>
            <div class="card-body">
                <?php if (isset($paymentReminder) && $interval->days <= 7): ?>
                    <i class="text-danger">Due in: <?php echo $interval->days; ?> Days</i><br>
                    <div class="row">
                        <div class="col">
                            <b>Payment Mode:</b><br><?php echo htmlspecialchars($paymentReminder['payment_mode']); ?><br>
                        </div>
                        <div class="col">
                            <p class="h5"><br><?php echo number_format((float)$paymentReminder['amount'], 2, '.', ','); ?><br></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <b>Due Date:</b><br><?php echo date('m/d/Y', strtotime($paymentReminder['created_at'])); ?><br>
                        </div>
                    </div>
                <?php else: ?>
                    <i class="text-success">No upcoming payments due.</i><br>
                <?php endif; ?>
            </div>
        </div>
        <div class="card">
    <div class="card-header">
        <div class="page-title">Missed Payments</div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col">
                <b>Tuition</b><br>
                <?php
                // Display missed payment due dates
                if (!empty($missedPayments)) {
                    foreach ($missedPayments as $missedDate) {
                        echo htmlspecialchars($missedDate) . "<hr>";
                    }
                } else {
                    echo "<p>No missed payments.</p>";
                }
                ?>
                <hr>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header">
        <div class="page-title">Other payments</div>
    </div>
    <div class="card-body">
        <?php
        if ($events) {
            foreach ($events as $event) {
                // Format the dates
                $eventDate = date("F j, Y", strtotime($event['event_date']));
                $paymentDeadlineStart = date("F j, Y", strtotime($event['payment_deadline_start']));
                $paymentDeadlineEnd = date("F j, Y", strtotime($event['payment_deadline_end']));

                echo "<div class='card border-secondary mb-3'>";
                echo "<div class='card-body'>";
                echo "<h5 class='card-title'>" . htmlspecialchars($event['event_name']) . "</h5>";
                echo "<p class='card-text'>Event Date: " . htmlspecialchars($eventDate) . "</p>";
                echo "<p class='card-text'>Event Fee: ₱" . number_format((float)$event['event_fee'], 2, '.', ',') . "</p>";
                echo "<p class='card-text'>Payment Deadline: <br>" . htmlspecialchars($paymentDeadlineStart) . " to " . htmlspecialchars($paymentDeadlineEnd) . "</p>";

                $sqlCheckReject = "SELECT COUNT(*) FROM payment_events WHERE admission_id = :admission_id AND status = 'Rejected'";
                $statementCheckReject = $conn->prepare($sqlCheckReject);
                $statementCheckReject->execute([':admission_id' => $id]);
                $isReject = $statementCheckReject->fetchColumn() > 0; // true if there are any records with status 'Rejected'

                // Check if the status is "Rejected"
                if ($isReject) {
                    echo "<p class='text-danger'>This event has been rejected.</p>";
                    echo "<div class='response-message mt-2' id='response-" . $event['id'] . "'></div>";
                } else {
                    // Check if the user has already paid for this event
                    $sqlCheckPaid = "SELECT COUNT(*) FROM payment_events WHERE admission_id = :admission_id AND event_name = :event_name AND status = 'Paid'";
                    $sqlCheckOTC = "SELECT COUNT(*) FROM payment_events WHERE admission_id = :admission_id AND status = 'OTC'";
                    
                    $statementCheckPaid = $conn->prepare($sqlCheckPaid);
                    $statementCheckPaid->execute([':admission_id' => $id, ':event_name' => $event['event_name']]);
                    $isPaid = $statementCheckPaid->fetchColumn() > 0; // true if there are any records with status 'Paid'

                    $statementCheckOTC = $conn->prepare($sqlCheckOTC);
                    $statementCheckOTC->execute([':admission_id' => $id]);
                    $isOTC = $statementCheckOTC->fetchColumn() > 0; // true if there are any records with status 'OTC'

                    // Check if the payment status is "OTC"
                    if ($isOTC) {
                        echo "<p class='text-danger'>Over the Counter</p>";
                    } elseif ($isPaid) {
                        // If already paid, show the message instead of buttons
                        echo "<p class='text-success'>Already Paid</p>";
                    }
                }

                echo "</div>"; // Close card-body
                echo "</div>"; // Close card
            }
        } else {
            echo "<p>No events found for your grade level.</p>";
        }
        ?>
    </div>
</div>
    </div>
<div class="col-12 col-md-4 mb-3">
    <div class="card">
        <div class="card-header">
            <div class="page-title">Due Dates</div>
        </div>
        <div class="card-body">
            <?php if ($showDueDates): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Monthly Installment</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody> 
                        <?php 
                        // Limit the display to the first 6 due dates
                        $limitedInstallments = array_slice($installmentsWithDueDates, 0, 1);
                        $firstUnpaidFound = false; // To track if we found the first unpaid due date
                        
                        foreach ($limitedInstallments as $item): 
                            // Get the current due date
                            $currentDueDate = DateTime::createFromFormat('m/d/Y', $item['due_date']);
                            
                            // Check if there is a payment for this due date
                            $sqlCheckPayment = "SELECT COUNT(*) FROM payments WHERE admission_id = :id AND DATE(created_at) = :due_date";
                            $statementCheckPayment = $conn->prepare($sqlCheckPayment);
                            $statementCheckPayment->execute([':id' => $id, ':due_date' => $currentDueDate->format('Y-m-d')]);
                            $paymentCount = $statementCheckPayment->fetchColumn();

                            // Determine if the link should be enabled or disabled
                            $isPayable = $paymentCount > 0; // Payable if payment exists
                            $linkClass = (!$isPayable && $firstUnpaidFound) ? 'btn-secondary' : 'btn-success'; // Use btn-secondary for disabled links
                            $linkDisabled = (!$isPayable && $firstUnpaidFound) ? 'javascript:void(0);' : "advance_payment.php?due_date=" . urlencode($item['due_date']) . "&session_id=" . urlencode($id) . "&grade_level=" . urlencode($grade);

                            // If this is an unpaid due date, mark it as found
                            if (!$isPayable && !$firstUnpaidFound) {
                                $firstUnpaidFound = true; // Mark the first unpaid due date
                            }
                        ?>
                            <tr>
                                <td class="">
                                    <small><?php echo htmlspecialchars($item['due_date']); ?></small></br>
                                    <?php echo htmlspecialchars($item['monthly_install']); ?>
                                </td>
                                <td class="text-center">
                                    <a href="<?php echo $linkDisabled; ?>" class="btn <?php echo $linkClass; ?>" <?php echo (!$isPayable && $firstUnpaidFound) ? 'tabindex="-1" aria-disabled="true"' : ''; ?>>Pay Now</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-success">Tuition Paid in Full</p>
            <?php endif; ?>
        </div>
    </div>
</div>
    <div class="col-12 col-md-4 mb-3">
        <div class="card">
            <div class="card-header">
                <div class="page-title">Payment History</div>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Payment Mode</th>
                            <th>Amount</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($paymentHistory as $payment): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($payment['payment_mode']); ?></td>
                                <td><?php echo $payment['amount']; ?></td>
                                
                                <td><?php echo $payment['created_at']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card">
    <div class="card-header">
        <h5 class="mb-0 page-title">OP Notification</h5>
    </div>
    <div class="card-body">
        <?php
        if ($events) {
            foreach ($events as $event) {
                // Query to get the most recent payment status for the admission_id
                $sqlRecentPayment = "SELECT status FROM payment_events WHERE admission_id = :admission_id ORDER BY created_at DESC LIMIT 1"; // Assuming you have a created_at column
                $statementRecentPayment = $conn->prepare($sqlRecentPayment);
                $statementRecentPayment->execute([':admission_id' => $id]);
                $recentPaymentStatus = $statementRecentPayment->fetchColumn(); // Fetch the status of the most recent payment
                echo "<script>console.log('Recent Payment Status: " . addslashes($recentPaymentStatus) . "');</script>";

                // Check if the recent payment status is 'Not Paid'
                if ($recentPaymentStatus == 'Rejected'|| $recentPaymentStatus == 'Paid' || $recentPaymentStatus == 'OTC' ) {
                    // If the status is 'Not Paid', do not show anything
                    continue; // Skip to the next event
                }

                // Format the dates
                $eventDate = date("F j, Y", strtotime($event['event_date']));
                $paymentDeadlineStart = date("F j, Y", strtotime($event['payment_deadline_start']));
                $paymentDeadlineEnd = date("F j, Y", strtotime($event['payment_deadline_end']));

                echo "<div class='card border-secondary mb-3'>";
                echo "<div class='card-body'>";
                echo "<h5 class='card-title'>" . htmlspecialchars($event['event_name']) . "</h5>";
                echo "<p class='card-text'>Date: " . htmlspecialchars($eventDate) . "</p>";
                echo "<p class='card-text'>Description: " . htmlspecialchars($event['remarks']) . "</p>";
                echo "<p class='card-text'>Event Fee: ₱" . number_format((float)$event['event_fee'], 2, '.', ',') . "</p>";
                echo "<p class='card-text'>Payment Deadline: <br>" . htmlspecialchars($paymentDeadlineStart) . " to " . htmlspecialchars($paymentDeadlineEnd) . "</p>";

                $sqlCheckReject = "SELECT COUNT(*) FROM payment_events WHERE admission_id = :admission_id AND status = 'Rejected'";
                $statementCheckReject = $conn->prepare($sqlCheckReject);
                $statementCheckReject->execute([':admission_id' => $id]);
                $isReject = $statementCheckReject->fetchColumn() > 0; // true if there are any records with status 'Rejected'

                // Check if the status is "Rejected"
                if ($isReject) {
                    echo "<p class='text-danger'>This event has been rejected.</p>";
                    echo "<div class='response-message mt-2' id='response-" . $event['id'] . "'></div>";
                } else {
                    // Check if the user has already paid for this event
                    $sqlCheckPaid = "SELECT COUNT(*) FROM payment_events WHERE admission_id = :admission_id AND event_name = :event_name AND status = 'Paid'";
                    $sqlCheckOTC = "SELECT COUNT(*) FROM payment_events WHERE admission_id = :admission_id AND status = 'OTC'";
                    
                    $statementCheckPaid = $conn->prepare($sqlCheckPaid);
                    $statementCheckPaid->execute([':admission_id' => $id, ':event_name' => $event['event_name']]);
                    $isPaid = $statementCheckPaid->fetchColumn() > 0; // true if there are any records with status 'Paid'

                    $statementCheckOTC = $conn->prepare($sqlCheckOTC);
                    $statementCheckOTC->execute([':admission_id' => $id]);
                    $isOTC = $statementCheckOTC->fetchColumn() > 0; // true if there are any records with status 'OTC'

                    // Check if the payment status is "OTC"
                    if ($isOTC) {
                        echo "<p class='text-danger'>Over the Counter</p>";
                    }
                    else if ($isPaid) {
                        // If already paid, show the message instead of buttons
                        echo "<p class='text-success'>Already Paid</p>";
                    } else {
                        // Show the buttons if not paid
                        echo "<button class='btn btn-success mx-3' data-bs-toggle='modal' data-bs-target='#paymentModal' 
                        data-event-id='" . $event['id'] . "' 
                        data-event-name='" . htmlspecialchars($event['event_name']) . "' 
                        data-event-fee='" . htmlspecialchars($event['event_fee']) . "' 
                        data-event-date='" . htmlspecialchars($eventDate) . "' 
                        data-remarks='" . htmlspecialchars($event['remarks']) . "' 
                        data-payment-deadline-start='" . htmlspecialchars($paymentDeadlineStart) . "' 
                        data-payment-deadline-end='" . htmlspecialchars($paymentDeadlineEnd) . "'>
                        Pay Now</button>";                
                        echo "<button class='btn btn-danger' onclick='rejectEvent(" . $event['id'] . ")'>Reject</button>";
                    }
                    echo "</div>";
                    echo "<div class='response-message mt-2' id='response-" . $event['id'] . "'></div>";
                }

                echo "</div>"; // Close card-body
                echo "</div>"; // Close card
            }
        } else {
            echo "<p>No events found for your grade level.</p>";
        }
        ?>
    </div>
</div>


<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fs-5" id="paymentModalLabel">Event Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3 ">
                    <label for="paymentMethod" class="form-label mt-2">Select Payment Method:</label>
                    <select id="paymentMethod" class="form-select" onchange="togglePaymentForm()">
                        <option value="online">Online Payment</option>
                        <option value="overCounter">Over the Counter</option>
                    </select>
                </div>

                <!-- Online Payment Form -->
                <form id="onlinePaymentForm" action="event_payment.php" method="POST" enctype="multipart/form-data">
                    <p class="fs-5">Step 1. Scan the QR code or type the mobile number below</p>
                    <img src="img/gcash_qr.jpg" alt="QR Code" class="img-fluid">
                    <p class="mt-4">Other Options: Bank Payment</p>
                    <img src="img/ub_details.JPG" alt="QR Code" class="img-fluid"> 
                    <p class="fs-5 mt-3">Step 2. Please upload the screenshot/downloaded receipt (accepts png, jpg, jpeg)</p>
                    <input type="file" class="form-control" name="image" id="image" accept="image/*" required>
                    
                    <!-- Hidden fields to store event data -->
                    <input type="hidden" name="event_name" id="event_name">
                    <input type="hidden" name="event_date" id="event_date">
                    <input type="hidden" name="remarks" id="remarks">
                    <input type="hidden" name="payment_deadline_start" id="payment_deadline_start">
                    <input type="hidden" name="payment_deadline_end" id="payment_deadline_end">
                    <input type="hidden" name="event_fee" id="event_fee">

                    <p class="fs-5 mt-3">Step 3. Please enter your full name, reference number, and the amount of the payment</p>
                    <div class="mb-3">
                        <p class="">Full Name:</p>
                        <input type="text" class="form-control" name="fullName" id="fullName" placeholder="Enter your full name" required>
                    </div>
                    <div class="mb-3">
                        <p class="">Reference Number:</p>
                        <input type="text" class="form-control" name="referenceNumber" id="referenceNumber" placeholder="Enter Payment Reference number" required>
                    </div>
                    <div class="mb-3">
                        <p class="">Amount:</p>
                        <input type="text" class="form-control" name="amount" id="amount" placeholder="Enter the amount you are paying" required oninput="formatAmount(this)">
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Submit Online Payment</button>
                    </div>
                </form>

                <!-- Over the Counter Payment Form -->
                <form id="overCounterPaymentForm" action="otc_event_payment.php" method="POST" enctype="multipart/form-data" style="display: none;">
                    <p class="fs-5">You can pay over-the-counter for this event in the school</p>
                                        <!-- Hidden fields to store event data -->
                                        <input type="hidden" name="event_name" id="event_name">
                    <input type="hidden" name="event_date" id="event_date">
                    <input type="hidden" name="remarks" id="remarks">
                    <input type="hidden" name="payment_deadline_start" id="payment_deadline_start">
                    <input type="hidden" name="payment_deadline_end" id="payment_deadline_end">
                    <input type="hidden" name="event_fee" id="event_fee">
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Submit Over the Counter Payment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



<script>
    // Listen for the show.bs.modal event to populate the modal with data
    const paymentModal = document.getElementById('paymentModal');
    paymentModal.addEventListener('show.bs.modal', function (event) {
        // Get the button that triggered the modal
        const button = event.relatedTarget; 

        // Extract the data from the button
        const eventId = button.getAttribute('data-event-id');
        const eventName = button.getAttribute('data-event-name');
        const eventFee = button.getAttribute('data-event-fee');
        const eventDate = button.getAttribute('data-event-date');
        const remarks = button.getAttribute('data-remarks');
        const paymentDeadlineStart = button.getAttribute('data-payment-deadline-start'); // Add this line
        const paymentDeadlineEnd = button.getAttribute('data-payment-deadline-end'); // Add this line

        // Populate the modal fields
        const fullNameInput = document.getElementById('fullName');
        const referenceInput = document.getElementById('referenceNumber');
        const amountInput = document.getElementById('amount');

        // Set the amount field to the event fee
        amountInput.value = eventFee;

        // Set the hidden fields
        document.getElementById('event_name').value = eventName;
        document.getElementById('event_date').value = eventDate;
        document.getElementById('remarks').value = remarks;
        document.getElementById('payment_deadline_start').value = paymentDeadlineStart; // Set this field
        document.getElementById('payment_deadline_end').value = paymentDeadlineEnd; // Set this field
    });
</script>




<script>




// Function to reject an event
function rejectEvent(eventId) {
    if (confirm("Are you sure you want to reject this event?")) {
        // Make an AJAX request to update the event status in the database
        const xhr = new XMLHttpRequest();
        xhr.open("POST", window.location.href, true); // Send the request to the same page
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onload = function () {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.success) {
                    // Update the UI or show a success message
                    document.getElementById('response-' + eventId).innerHTML = "<span class='text-danger'>Not Accepted</span>";
                } else {
                    alert(response.message);
                }
            } else {
                alert('Failed to update event status');
            }
        };
        xhr.send("event_id=" + eventId);
    }
}
</script>

<script>
</script>
    </div>
</div>

<div class="row">
    <div class="col-12 col-md-4 mb-3">

    </div>

    <div class="col-12 col-md-4 mb-3">

    </div>

    <div class="col-12 col-md-4 mb-3">

    </div>
</div>










</div>


<!----------------Sweet Alert---------------------->

<script src="assets/js/sweetalert.min.js"></script>
<?php
    if(isset($_SESSION['error'])){
        ?>
        <script>
        swal({
            title: "<?php echo $_SESSION['title'];   ?>",
            text: "<?php echo $_SESSION['error'];   ?>",
            icon: "<?php echo $_SESSION['status'];   ?>",
            button: "Ok",
          });
        </script>
          <?php
          unset($_SESSION['error']);
          unset($_SESSION['status']);
    }
?>

<script src="assets/js/jquery-3.6.0.min.js"></script>

<script src="assets/js/bootstrap.bundle.min.js"></script>

<script src="assets/js/jquery.slimscroll.js"></script>
 
<script src="assets/js/select2.min.js"></script>
<script src="assets/js/moment.min.js"></script>

<script src="assets/js/fullcalendar.min.js"></script>
<script src="assets/js/jquery.fullcalendar.js"></script>

<script src="assets/plugins/morris/morris.min.js"></script>
<script src="assets/plugins/raphael/raphael-min.js"></script>
<script src="assets/js/apexcharts.js"></script>
<script src="assets/js/chart-data.js"></script>

<script src="assets/js/app.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<script>
function togglePaymentForm() {
    const paymentMethod = document.getElementById('paymentMethod').value;
    const onlinePaymentForm = document.getElementById('onlinePaymentForm');
    const overCounterPaymentForm = document.getElementById('overCounterPaymentForm');

    if (paymentMethod === 'online') {
        onlinePaymentForm.style.display = 'block';
        overCounterPaymentForm.style.display = 'none';
    } else {
        onlinePaymentForm.style.display = 'none';
        overCounterPaymentForm.style.display = 'block';
    }
}
</script>

</body>
</html>