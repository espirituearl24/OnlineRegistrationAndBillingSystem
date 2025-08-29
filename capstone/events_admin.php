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



// Initialize variables for filtering
$start_date = isset($_POST['start_date']) ? $_POST['start_date'] : '';
$end_date = isset($_POST['end_date']) ? $_POST['end_date'] : '';

// Base query
$query2 = "SELECT * FROM payment_events ";

// Check if dates are provided
if (!empty($start_date) && !empty($end_date)) {
    $start_month = date('m', strtotime($start_date));
    $start_year = date('Y', strtotime($start_date));
    $end_month = date('m', strtotime($end_date));
    $end_year = date('Y', strtotime($end_date));

    // Modify query to filter by month and year
    $query2 .= " AND (MONTH(created_at) BETWEEN :start_month AND :end_month) 
                 AND (YEAR(created_at) BETWEEN :start_year AND :end_year)";
}

// Prepare the query
$statement2 = $conn->prepare($query2);
if (!empty($start_date) && !empty($end_date)) {
    $statement2->bindParam(':start_month', $start_month);
    $statement2->bindParam(':end_month', $end_month);
    $statement2->bindParam(':start_year', $start_year);
    $statement2->bindParam(':end_year', $end_year);
}

// Execute the query
$statement2->execute();
$result2 = $statement2->fetchAll();




?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>GBA | Admin</title>
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
<input class="form-control" type="text" placeholder="Search here">
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
<a class="dropdown-item" href="changepass.php?lastname=<?php echo $userl; ?>&firstname=<?php echo $userf; ?>">Change Password</a>

<a class="dropdown-item" href="index.php" onclick="return confirm('Are you sure you want to logout?');">Logout</a>
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
<span class="text-uppercase ms-2 mt-5">Admin</span>
</a>
</div>
<ul class="sidebar-ul">
<li class="menu-title"></li>
<li class="">
<a href="admin.php"><img src="assets/img/sidebar/icon-1.png" alt="icon"><span>Dashboard</span></a>
</li>
<li class="">
  <a href="tadmission.php"><img src="assets/img/sidebar/icon-2.png" alt="icon"> <span> Admission</span> <span class=""></span></a>
</li>
<li class="">
  <a href="#"><img src="assets/img/sidebar/icon-10.png" alt="icon"> <span> Students</span> <span class=""></span></a>
</li>
<li class="submenu">
  <a href="#"><img src="assets/img/sidebar/icon-3.png" alt="icon"><span> Archive </span> <span class="menu-arrow"></span></a>
    <ul class="list-unstyled" style="display: none;">
    <li><a href="tarchive_admission.php"><span>Admission</span></a></li>
    <li><a href="tarchive_estudent.php"><span>Enrolled</span></a></li>
  </ul>
</li>
<li class="">
  <a href="account_table.php"><img src="assets/img/sidebar/icon-4.png" alt="icon"> <span> Accounts</span> <span class=""></span></a>
</li>
<li class="submenu">
<a href="javascript:void(0);"><img src="assets/img/sidebar/icon-11.png" alt="icon"> <span> Payments</span> <span class="menu-arrow"></span></a>
<ul style="display: none;">
<li class="active">
  <a href="list_due.php">Admission <span class="badge badge-pill bg-primary float-right">5</span></a>
</li>
<li>
  <a href="enrolled_payments.php">Enrolled<span class="badge badge-pill bg-primary float-right">5</span></a>
</li>
<li><a href="#"><span>History</span></a></li>
</ul>

</ul>
</div>
</div>
</div>
<!---------------SIDE BAR END---------------->

<div class="page-wrapper">
<div class="content container-fluid">

<div class="page-header">

<!------------------------------View of Admission--------------------------------------------------------------->
<!-- <tbody>
  <?php

    $query= "SELECT * FROM admission";
    $statement = $conn->prepare($query);
    $statement->execute();

    $result = $statement->fetchAll();
    if($result){
      foreach($result as $row)
      {
        ?>

          <tr>
            <td><?= $row['fname']; ?></td>
            <td><?= $row['lname']; ?></td>
            <td><?= $row['birthday']; ?></td>
            <td><?= $row['gender']; ?></td>
            <td><?= $row['email']; ?></td>
            <td><?= $row['phonenumber']; ?></td>
            <td><?= $row['grade']; ?></td>
            <td>
              <form action="process.php" method="POST">
              <button class="btn text-success" type="submit" onclick="return confirm('Are you sure you want to confirm this?');" name="save_enroll" value="<?=  $row['id'];  ?>"><i class="bi bi-check-square-fill"></i></button>
              </form>
            </td>
           
            
          <tr>

        <?php
      }
    }
    else{
      ?>

        <tr>

          <td colspan="7">No Record Found</td>

        </tr>

      <?php
    }

  ?>
</tbody>
</table>
</div>
</div>
</div>
</div>
</div> -->

<!------------------------------ Admission table End Here -------------------------->

<div class="row">
<div class="col-12">
<div class="card">
<div class="card-header">
<div class="row align-items-center">
<div class="col-sm-6">
<div class="page-title">
Events Payments
</div>
</div>
<div class="col-sm-6 text-sm-right">
<div class=" mt-sm-0 mt-2">
<a href="export_enroll.php"><button class="btn btn-outline-primary mr-2" name="excel"><img src="assets/img/excel.png" alt=""><span class="ml-2">Excel</span></button></a>
<button class="btn btn-outline-danger mr-2"><img src="assets/img/pdf.png" alt="" height="18"><span class="ml-2">PDF</span></button>
<button class="btn btn-light" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-h"></i></button>
<div class="dropdown-menu dropdown-menu-right">
<a class="dropdown-item" href="#">Action</a>
<div role="separator" class="dropdown-divider"></div>
<a class="dropdown-item" href="#">Another action</a>
<div role="separator" class="dropdown-divider"></div>
<a class="dropdown-item" href="#">Something else here</a>
</div>
</div>
</div>
</div>
</div>
<div class="card-body">
<div class="row">
<div class="col-lg-12 col-md-12 col-sm-12 col-12">
<div class="table-responsive">


<table class="table custom-table text-center"> 
  <!-----------------View of Enrollees--------------------->
  <thead class="thead-light">
    <tr>
      <th>Admission ID</th>
      <th>Full Name</th>
      <th>Reference No.</th>
      <th>Amount</th>
      <th>Date & Time</th>
      <th>Event Name</th>
      <th class="text-right">Action</th>
    </tr>
  </thead>
  <tbody>

  <form method="POST" action="">
    <label for="date_range">Filter by Month and Year:</label><br>
    <label for="start_date">From:</label>
    <input type="month" class="mx-3" name="start_date" id="start_date" value="<?= htmlspecialchars($start_date) ?>">
    <label for="end_date">To:</label>
    <input type="month" name="end_date" id="end_date" value="<?= htmlspecialchars($end_date) ?>">
    <button type="submit" name="filter_by_date">Filter</button>
</form>


    <?php
    // $query= "SELECT * FROM payments";
    // $statement = $conn->prepare($query);
    // $statement->execute();
      // Check if any results were returned
      if($result2) {
        foreach($result2 as $row2) {
    ?>
    <tr>
      <td><?= $row2['admission_id']; ?></td>
      <td><?= $row2['full_name']; ?></td>
      <td><?= $row2['reference_number']; ?></td>
      <td><?= $row2['amount']; ?></td>
      <td><?= $row2['created_at']; ?></td>
      <td><?= $row2['event_name']; ?></td>
      <td>
<!-- Button to open modal to show receipt -->
<button type="button" class="btn btn-info btn-sm mb-1" data-bs-toggle="modal" data-bs-target="#receiptModal<?= $row2['admission_id']; ?>">
                <i class="bi bi-receipt"></i>
            </button>

            <!-- Second button (no action, just the icon) -->
            <button type="button" class="btn btn-success btn-sm mb-1">
            <i class="bi bi-check-square"></i>
            </button>

            <!-- Third button (no action, just the icon) -->
            <button type="button" class="btn btn-danger btn-sm mb-1">
            <i class="bi bi-x-square"></i>
            </button>
      </td>
    </tr>
<!-- Modal for displaying the receipt image -->
<div class="modal fade" id="receiptModal<?= $row2['admission_id']; ?>" tabindex="-1" aria-labelledby="receiptModalLabel<?= $row2['admission_id']; ?>" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="receiptModalLabel<?= $row2['admission_id']; ?>">Receipt Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Display the receipt image -->
                <img src="e-receipts/<?= $row2['receipt']; ?>" alt="Receipt" class="img-fluid">

                <?php
                // Fetch the full name from the admission table using admission_id
                $admissionId = $row2['admission_id'];
                $nameQuery = "SELECT firstname, lastname FROM admission WHERE id = :admissionId";
                $stmt = $conn->prepare($nameQuery);
                $stmt->execute(['admissionId' => $admissionId]);
                $nameResult = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($nameResult) {
                    $fullName = $nameResult['firstname'] . ' ' . $nameResult['lastname'];
                } else {
                    $fullName = 'Name not found';
                }
                ?>

                <div class="mt-3">
                    <strong>Full Name of the Student: </strong> <?= htmlspecialchars($fullName); ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

    <?php
        }
      } else {
    ?>
    <tr>
      <td colspan="8">No Record Found</td>
    </tr>
    <?php
      }
    ?>
  </tbody>
</table>



</div>
</div>
</div>
</div>
</div>
</div>
</div>

  <!-----------------View of Archive--------------------->

<!-- <thead class="thead-light">
<tr>
<th>First Name</th>
<th>Last Name</th>
<th>Birthday</th>
<th>Gender</th>
<th>Email</th>
<th>Phone Number</th>
<th>Grade</th>
<th class="text-right">Action</th>
</tr>
</thead>
<tbody>
  <?php

    $query3 = "SELECT * FROM archive";
    $statement3 = $conn->prepare($query3);
    $statement3->execute();

    $result3 = $statement3->fetchAll();
    if($result3)
    {
      foreach($result3 as $row3)
      {
        ?>

          <tr>
            <td><?= $row3['fname']; ?></td>
            <td><?= $row3['lname']; ?></td>
            <td><?= $row3['birthday']; ?></td>
            <td><?= $row3['gender']; ?></td>
            <td><?= $row3['email']; ?></td>
            <td><?= $row3['phonenumber']; ?></td>
            <td><?= $row3['Grade']; ?></td>
            <td>
          <form action="process.php" method="POST">
            <button type="submit" class="btn btn-primary btn-sm mb-1" onclick="return confirm('Are you sure you want to recover this data?');" name="return" value="<?= $row3['id'] ?>">
              <i class="far fa-edit"></i>
            </button>
          </form>
            
            <a href="enroll_update.php?id=<?= $row2['id'] ?>"  class="btn btn-danger btn-sm mb-1">
              <i class="far fa-trash-alt"></i>
            </a>
            </td>
           
            
          <tr>

        <?php
      }
    }
    else{
      ?>

        <tr>

          <td colspan="7">No Record Found</td>

        </tr>

      <?php
    }
  
  ?>

</tbody>
</table>
</div>
</div>
</div>
</div>
</div>
</div>
</div> -->


<!--------------------------- Enrollees and Archive Table here --------------------------->

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
</body>
</html>