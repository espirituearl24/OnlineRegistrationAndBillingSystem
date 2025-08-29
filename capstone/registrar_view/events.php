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
<title>GBA | Admin</title>
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
<div class="top-nav-search">
<a href="javascript:void(0);" class="responsive-search">
<i class="fa fa-search"></i>
</a>
<form action="inbox.html">
<input class="form-control pt-4" type="text" placeholder="Search here">
<button class="btn" type="submit"><i class="fa fa-search"></i></button>
</form>
</div>
</li>
<li>
<a href="#" class="mobile-logo d-md-block d-lg-none d-block"><img src="img/logo1.png" alt="" width="30" height="30"></a>
</li>
</ul>

<ul class="nav user-menu float-right">
<li class="nav-item dropdown d-none d-sm-block">
<a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown">
<img src="assets/img/sidebar/icon-22.png" alt="">
</a>
<div class="dropdown-menu notifications">
<div class="topnav-dropdown-header">
<span>Notifications</span>
</div>
<div class="drop-scroll">
<ul class="notification-list">
<li class="notification-message">
<a href="#">
<div class="media">
<span class="avatar">
<img alt="John Doe" src="assets/img/user-06.jpg" class="img-fluid rounded-circle">
</span>
<div class="media-body">
<p class="noti-details"><span class="noti-title">John Doe</span> is now following you </p>
<p class="noti-time"><span class="notification-time">4 mins ago</span></p>
</div>
</div>
</a>
</li>
<li class="notification-message">
<a href="#">
<div class="media">
<span class="avatar">T</span>
<div class="media-body">
<p class="noti-details"><span class="noti-title">Tarah Shropshire</span> sent you a message.</p>
<p class="noti-time"><span class="notification-time">6 mins ago</span></p>
</div>
</div>
</a>
</li>
<li class="notification-message">
<a href="#">
<div class="media">
<span class="avatar">L</span>
<div class="media-body">
<p class="noti-details"><span class="noti-title">Misty Tison</span> like your photo.</p>
<p class="noti-time"><span class="notification-time">8 mins ago</span></p>
</div>
</div>
</a>
</li>
<li class="notification-message">
<a href="#">
<div class="media">
<span class="avatar">G</span>
<div class="media-body">
<p class="noti-details"><span class="noti-title">Rolland Webber</span> booking appoinment for meeting.</p>
<p class="noti-time"><span class="notification-time">12 mins ago</span></p>
</div>
</div>
</a>
</li>
<li class="notification-message">
<a href="#">
<div class="media">
<span class="avatar">T</span>
<div class="media-body">
<p class="noti-details"><span class="noti-title">Bernardo Galaviz</span> like your photo.</p>
<p class="noti-time"><span class="notification-time">2 days ago</span></p>
</div>
</div>
</a>
</li>
</ul>
</div>
<div class="topnav-dropdown-footer">
<a href="#">View all Notifications</a>
</div>
</div>
</li>
<li class="nav-item dropdown d-none d-sm-block">
<a href="javascript:void(0);" id="open_msg_box" class="hasnotifications nav-link"><img src="assets/img/sidebar/icon-23.png" alt=""> </a>
</li>
 <li class="nav-item dropdown has-arrow">
<a href="#" class=" nav-link user-link" data-toggle="dropdown">
<span class=" ">  
  </span>
<span> <?php echo $user ?> </span>
</a>
<div class="dropdown-menu">
<a class="dropdown-item" href="#"> </a>
<a class="dropdown-item" href="#"> </a>
<a class="dropdown-item" href="changerpass.php?lastname=<?php echo $userl; ?>&firstname=<?php echo $userf; ?>">Change Password</a>
<a class="dropdown-item" href="index.php" onclick="return confirm('Are you sure you want to logout?');">Logout</a>
</div>
</li>
</ul>
<div class="dropdown mobile-user-menu float-right"> 
<a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></a>
<div class="dropdown-menu dropdown-menu-right">
<a class="dropdown-item" href="#"> </a>
<a class="dropdown-item" href="#"> </a>
<a class="dropdown-item" href="changerpass.php?lastname=<?php echo $userl; ?>&firstname=<?php echo $userf; ?>">Change Password</a>
<a class="dropdown-item" href="../index.php" onclick="return confirm('Are you sure you want to logout?');">Logout</a>
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
<a href="registrar.php"><img src="assets/img/sidebar/icon-1.png" alt="icon"><span>Dashboard</span></a>
</li>
<li class="">
  <a href="list_due.php"><img src="assets/img/sidebar/icon-2.png" alt="icon"> <span> Admission</span> <span class=""></span></a>
</li>
<li class="">
  <a href="enrolled_payments.php"><img src="assets/img/sidebar/icon-10.png" alt="icon"> <span> Students</span> <span class=""></span></a>
</li>
<li class="submenu">
  <a href="javascript:void(0);"><img src="assets/img/sidebar/icon-3.png" alt="icon"><span> Archive </span> <span class="menu-arrow"></span></a>
    <ul class="list-unstyled" style="display: none;">
    <li><a href="tarchive_admission.php"><span>Admission</span></a></li>
    <li><a href="tarchive_estudent.php"><span>Student</span></a></li>
  </ul>
</li>

<li><a href="events_registrar.php"><img src="assets/img/sidebar/icon-11.png" alt="icon"><span>Events</span></a></li>

<li class="active">
  <a href="events.php"><img src="assets/img/sidebar/icon-4.png" alt="icon"> <span>Events</span> <span class=""></span></a>
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
    <form action="post_event.php" method="post" class="mt-3">

      <!-- Level Dropdown -->
      <div class="mb-3"> 
        <label for="level" class="form-label">Level</label>
        <select class="form-select" id="level" name="level" required>
          <option selected disabled>Select Level</option>
          <option value="Kinder 1">Kinder 1</option>
          <option value="Kinder 2">Kinder 2</option>
          <option value="Grade 1">Grade 1</option>
          <option value="Grade 2">Grade 2</option>
          <option value="Grade 3">Grade 3</option>
          <option value="Grade 4">Grade 4</option>
          <option value="Grade 5">Grade 5</option>
          <option value="Grade 6">Grade 6</option>
          <option value="Grade 7">Grade 7</option>
          <option value="Grade 8">Grade 8</option>
          <option value="Grade 9">Grade 9</option>
          <option value="Grade 10">Grade 10</option>
        </select>
      </div>

      <!-- Event Info Row -->
      <div class="row mb-3">
  <div class="col-md-4">
    <label for="event_name" class="form-label p-1">Event Name</label>
    <input type="text" class="form-control pt-4" id="event_name" name="event_name" required>
  </div>
  <div class="col-md-4">
    <label for="event_fee" class="form-label p-1">Event Fee</label>
    <input type="text" class="form-control pt-4" id="event_fee" name="event_fee" required oninput="formatMoney(this)">
    <input type="hidden" id="raw_event_fee" name="raw_event_fee">
</div>
  <div class="col-md-4">
    <label for="event_date" class="form-label p-1">Event Date</label>
    <input type="text" class="form-control pt-4" id="event_date" name="event_date" required>
</div>

</div>



      <!-- Payment Deadline and Remarks Row -->
      <div class="row mb-3">
        <div class="col-md-4">
          <label for="payment_deadline_start" class="form-label p-1">Payment Deadline Start</label>
          <input type="date" class="form-control pt-4" id="payment_deadline_start" name="payment_deadline_start" required>
        </div>
        <div class="col-md-4">
          <label for="payment_deadline_end" class="form-label p-1">Payment Deadline End</label>
          <input type="date" class="form-control pt-4" id="payment_deadline_end" name="payment_deadline_end" required>
        </div>
        <div class="col-md-4">
          <label for="remarks" class="form-label p-1">Remarks</label>
          <input type="text" class="form-control pt-4" id="remarks" name="remarks">
        </div>
      </div>

      <!-- Submit Button -->
      <div class="text-center mt-4">
        <button type="submit" class="btn btn-primary">Submit</button>
      </div>

    </form>
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
                            <th>Action</th> <!-- New column for actions -->
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
                                echo "<td>
                                        <button class='btn btn-danger btn-sm' onclick='confirmDelete(" . $row['id'] . ")'><i class='bi bi-trash-fill'></i></button>
                                      </td>";
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