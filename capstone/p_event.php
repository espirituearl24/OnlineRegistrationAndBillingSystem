<?php


session_start();

include 'dbconnection.php';

$user = $_SESSION['lastname'];

$userl = $_SESSION['lastname'];
$userf = $_SESSION['firstname'];

$adminctr = "SELECT * FROM admission";
$adminctr_run = $conn->query($adminctr);
$adminctr_exe = $adminctr_run->rowCount(); 

$enrollctr = "SELECT * FROM enroll";
$enrollctr_run = $conn->query($enrollctr);
$enrollctr_exe = $enrollctr_run->rowCount();

$total = $adminctr_exe+$enrollctr_exe;




// Handle event deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['event_id'])) {
    $event_id = $_POST['event_id'];

    // Prepare the SQL statement to delete the event
    $stmt = $conn->prepare("DELETE FROM events WHERE id = :id");
    $stmt->bindParam(':id', $event_id);

    if ($stmt->execute()) {
        // Set success message
        $_SESSION['message'] = "Event deleted successfully.";
    } else {
        // Set error message
        $_SESSION['message'] = "Error deleting event.";
    }

    // Redirect to the same page to avoid resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Fetch events from the database
$events_query = "SELECT * FROM events"; // Adjust the query as needed
$events_run = $conn->query($events_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>GBA | Registrar</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
<!-- Include SweetAlert -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

<link rel="shortcut icon" type="image/x-icon" href="img/logo1.png">

<link href="../../../../css?family=Roboto:300,400,500,700,900" rel="stylesheet">

<link rel="stylesheet" href="assets/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css">
<link rel="stylesheet" href="assets/plugins/fontawesome/css/fontawesome.min.css">

<link rel="stylesheet" href="assets/css/style.css">
<!--[if lt IE 9]>
    <script src="assets/js/html5shiv.min.js"></script>
    <script src="assets/js/respond.min.js"></script>
  <![endif]-->


</head>
<body>

<div class="main-wrapper">

<div class="header-outer">
<div class="header">
<a id="mobile_btn" class="mobile_btn float-left" href="#sidebar"><i class="fas fa-bars" aria-hidden="true"></i></a>
<a id="toggle_btn" class="float-left" href="javascript:void(0);">
<img src="assets/img/sidebar/icon-21.png" alt="">
</a>

<ul class="nav float-left">

<li>
<a href="#" class="mobile-logo d-md-block d-lg-none d-block"><img src="img/logo1.png" alt="" width="30" height="30"></a>
</li>
</ul>

<ul class="nav user-menu float-right">

 <li class="nav-item dropdown has-arrow">
<a href="#" class=" nav-link user-link" data-toggle="dropdown">
<span class=" ">  
  </span>
<span> <?php echo $user ?> </span>
</a>
<div class="dropdown-menu">
<a class="dropdown-item" href="#"> </a>
<a class="dropdown-item" href="#"> </a>
<a class="dropdown-item" href="changepass.php?lastname=<?php echo $userl; ?>&firstname=<?php echo $userf; ?>">Change Password</a>

<a class="dropdown-item" href="logout.php" onclick="return confirm('Are you sure you want to logout?');">Logout</a>
</div>
</li>
</ul>
<div class="dropdown mobile-user-menu float-right"> 
<a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></a>
<div class="dropdown-menu dropdown-menu-right">
<a class="dropdown-item" href="#"> </a>
<a class="dropdown-item" href="#"> </a>
<a class="dropdown-item" href="changepass.php?lastname=<?php echo $userl; ?>&firstname=<?php echo $userf; ?>">Change Password</a>

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
<img src="img/logo1.png" width="40" height="40" alt="" class="">
<span class="text-uppercase ms-2 mt-5">Registrar</span>
</a>
</div>
<ul class="sidebar-ul">
<li class="menu-title"></li>
<li class="">
<a href="principal.php"><img src="assets/img/sidebar/icon-1.png" alt="icon"><span>Dashboard</span></a>
</li>
<li class="submenu">
  <a href="#"><img src="assets/img/sidebar/icon-3.png" alt="icon"><span> Registrar </span> <span class="menu-arrow"></span></a>
    <ul class="list-unstyled" style="display: none;">
    <li><a  href="p_list.php"><span>Admission Info</span></a></li>
    <li><a  href="p_rstudent.php"><span>Students</span></a></li>
    <li><a href="p_account.php"><span>Accounts</span></a></li>
    <li><a class="active text-decoration-underline text-black" href="p_event.php"><span></span>Events</a></li>
  </ul>
</li>

<li class="submenu">
  <a href="#"><img src="assets/img/sidebar/icon-3.png" alt="icon"><span> Accounting </span> <span class="menu-arrow"></span></a>
  <ul class="list-unstyled" style="display: none;">
    <!-- Payments dropdown inside Accounting -->
    <li class="submenu">
      <a href="javascript:void(0);"><img src="assets/img/sidebar/icon-11.png" alt="icon"><span> Payments </span> <span class="menu-arrow"></span></a>
      <ul class="list-unstyled" style="display: none;">
        <li><a href="p_enrolled_payments.php"><span>History</span></a></li>
        <li><a href="registrar_dues.php"><span>Dues</span></a></li>   
      </ul>
    </li>
  </ul>
</li>




</ul>
</div>
</div>
</div>
<!---------------SIDE BAR END---------------->

<div class="page-wrapper">
<div class="content container-fluid">

<div class="page-header">
<div class="row">
<div class="col-md-6">
<h3 class="page-title mb-0">Create An Event</h3>
</div>

</div>
</div>




<div class="row">
  <div class="col-12">
    <h3 class="mt-4">Event List</h3>
    <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Grade Level</th>
                            <th>Event Name</th>
                            <th>Event Fee</th>
                            <th>Event Date</th>
                            <th>Payment Deadline Start</th>
                            <th>Payment Deadline End</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($events_run) {
                            while ($row = $events_run->fetch(PDO::FETCH_ASSOC)) {
                                echo "<tr>";
                                echo "<td>" . $row['level'] . "</td>";
                                echo "<td>" . $row['event_name'] . "</td>";
                                echo "<td>" . number_format($row['event_fee'], 2) . "</td>"; // Format the event fee
                                echo "<td>" . $row['event_date'] . "</td>";
                                echo "<td>" . $row['payment_deadline_start'] . "</td>";
                                echo "<td>" . $row['payment_deadline_end'] . "</td>";
                                echo "<td>" . $row['remarks'] . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='8'>No events found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
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

</div>

<script>
function confirmDelete(eventId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            // Create a form to submit the delete request
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = ''; // Current page
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'event_id';
            input.value = eventId;
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit(); // Submit the form
        }
    });
}
</script>


<script>
function formatMoney(input) {
    // Remove any non-digit characters to get the raw value
    let rawValue = input.value.replace(/[^0-9]/g, '');
    
    // Format the number with commas for display
    let formattedValue = parseInt(rawValue).toLocaleString('en-US');

    // Update the input value to display
    input.value = formattedValue;

    // Update the hidden input with the raw value (for form submission)
    document.getElementById('raw_event_fee').value = rawValue;
}

</script>


<script>
document.addEventListener('DOMContentLoaded', function() {
    flatpickr("#event_date", {
        dateFormat: "Y-m-d", // Format for database
        placeholder: "YYYY-MM-DD" // Placeholder reflecting the format
    });
});

</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    flatpickr("#payment_deadline_start", {
        dateFormat: "Y-m-d", // Format for database
        placeholder: "YYYY-MM-DD" // Placeholder reflecting the format
    });
});

</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    flatpickr("#payment_deadline_end", {
        dateFormat: "Y-m-d", // Format for database
        placeholder: "YYYY-MM-DD" // Placeholder reflecting the format
    });
});



</script>



<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">


<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

<script src="assets/js/bootstrap.bundle.min.js"></script>

<script src="assets/js/jquery.slimscroll.js"></script>
 
<script src="assets/js/select2.min.js"></script>
<script src="assets/js/moment.min.js"></script>

<script src="assets/js/fullcalendar.min.js"></script>
<script src="assets/js/jquery.fullcalendar.js"></script>


<script src="assets/js/app.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>