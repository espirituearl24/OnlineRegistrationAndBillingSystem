<?php


session_start();

include 'dbconnection.php';

$user = $_SESSION['lastname'];
$userl = $_SESSION['lastname'];
$userf = $_SESSION['firstname'];
// SQL Query to count the number of rows in the admission table
$admissionCountQuery = "SELECT COUNT(*) AS admission_count FROM admission";
$admissionCountResult = $conn->query($admissionCountQuery);

$admissionCount = 0; // Default value
if ($admissionCountResult) {
    $row = $admissionCountResult->fetch(PDO::FETCH_ASSOC);
    $admissionCount = $row['admission_count'] ?? 0; // Get admission count or default to 0
}


$adminctr = "SELECT * FROM admission";
$adminctr_run = $conn->query($adminctr);
$adminctr_exe = $adminctr_run->rowCount();

$enrollctr = "SELECT * FROM enroll";
$enrollctr_run = $conn->query($enrollctr); 
$enrollctr_exe = $enrollctr_run->rowCount();

$total = $adminctr_exe+$enrollctr_exe;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>GBA | Accountant</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">


<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.css">
<!-- DataTables Buttons CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.2.0/css/buttons.dataTables.css">

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>

<!-- DataTables Buttons JS -->
<script src="https://cdn.datatables.net/buttons/3.2.0/js/dataTables.buttons.js"></script>
<script src="https://cdn.datatables.net/buttons/3.2.0/js/buttons.dataTables.js"></script>

<!-- JSZip (required for Excel/CSV export) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<!-- pdfMake (for PDF export functionality) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<!-- DataTables Buttons HTML5 Export (for PDF, CSV, Excel) -->
<script src="https://cdn.datatables.net/buttons/3.2.0/js/buttons.html5.min.js"></script>
<!-- DataTables Buttons Print Export -->
<script src="https://cdn.datatables.net/buttons/3.2.0/js/buttons.print.min.js"></script>


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
<a class="dropdown-item" href="changerpass.php?lastname=<?php echo $userl; ?>&firstname=<?php echo $userf; ?>">Change Password</a>
<a class="dropdown-item" href="../logout.php" onclick="return confirm('Are you sure you want to logout?');">Logout</a>
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
<span class="text-uppercase ms-2 mt-5">Accountant</span>
</a>
</div>
<ul class="sidebar-ul">
<li class="menu-title"></li>
<li class="">
<a href="registrar.php"><img src="assets/img/sidebar/icon-1.png" alt="icon"><span>Dashboard</span></a>
</li>
<li class="submenu">
  <a href="javascript:void(0);"><img src="assets/img/sidebar/icon-2.png" alt="icon"> <span> Admission <span class="badge rounded-pill text-bg-danger"> <?php echo $admissionCount; ?></span></span> <span class="menu-arrow"></span></a>
  <ul class="list-unstyled" style="display: none;">
  <li><a href="list_due.php"><span>New Admissions <span class="badge rounded-pill text-bg-danger"> <?php echo $admissionCount; ?></span> </span></a></li>
  <li><a href="old_admission_table.php"><span>Readmission</span></a></li>
  </ul>
</li>
<!-- <li class="">
  <a href="enrolled_payments.php"><img src="assets/img/sidebar/icon-10.png" alt="icon"> <span> Students</span> <span class=""></span></a>
</li> -->
<li class="submenu">
  <a href="javascript:void(0);"><img src="assets/img/sidebar/icon-11.png" alt="icon"><span> Payments </span> <span class="menu-arrow"></span></a>
    <ul class="list-unstyled" style="display: none;">
    <li><a  href="otc_registrar.php"><span>Over the Counter</span></a></li>
    <li><a href="enrolled_payments.php"><span>History</span></a></li>
    <li><a href="registrar_dues.php"><span>Dues</span></a></li>
  </ul>
</li>
<li class="submenu">
  <a class="active" href="javascript:void(0);"><img src="assets/img/sidebar/icon-3.png" alt="icon"><span> Archive </span> <span class="menu-arrow"></span></a>
    <ul class="list-unstyled" style="display: none;">
    <li><a href="tarchive_admission.php"><span>Admission </span></a></li>
    <li><a class="active text-decoration-underline text-black" href="tarchive_estudent.php"><span>Student</span></a></li>
  </ul>
</li>

<li><a href="events_registrar.php"><img src="assets/img/sidebar/icon-17.png" alt="icon"><span>Events</span></a></li>

<!-- <li class="">
  <a href="email.php"><img src="assets/img/sidebar/icon-4.png" alt="icon"> <span>Email</span> <span class=""></span></a>
</li> -->


</ul>
</div>
</div>
</div>
<!---------------SIDE BAR END---------------->

<div class="page-wrapper">
<div class="content container-fluid">

<div class="page-header">
<!-- <div class="row">
<div class="col-md-6">
<h3 class="page-title mb-0">Dashboard</h3>
</div>
<div class="col-md-6">
<ul class="breadcrumb mb-0 p-0 float-right">
<li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Home</a></li>
<li class="breadcrumb-item"><span>Dashboard</span></li>
</ul>
</div>
</div>
</div> -->

<!------------------------------ total number ------------------------------>

<!-- <div class="row">
<div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
<div class="dash-widget dash-widget5">
<span class="float-left"><img src="assets/img/dash/dash-1.png" alt="" width="80"></span>
<div class="dash-widget-info text-right">
<span>Admissions</span>
<h3><?php  echo $adminctr_exe  ?></h3>
</div>
</div>
</div>
<div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
<div class="dash-widget dash-widget5">
<div class="dash-widget-info text-left d-inline-block">
<span>Enrolled</span>
<h3><?php  echo $enrollctr_exe  ?></h3>
</div>
<span class="float-right"><img src="assets/img/dash/dash-2.png" width="80" alt=""></span>
</div>
</div>
<div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
<div class="dash-widget dash-widget5">
<span class="float-left"><img src="assets/img/dash/dash-3.png" alt="" width="80"></span>
<div class="dash-widget-info text-right">
<span>Total</span>
<h3><?php  echo $total  ?></h3>
</div>
</div>
</div> -->

<!------------------------------ total number ------------------------------>


<!-- <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
<div class="dash-widget dash-widget5">
<div class="dash-widget-info d-inline-block text-left">
<span>Total Earnings</span>
<h3>$20,000</h3>
</div>
<span class="float-right"><img src="assets/img/dash/dash-4.png" alt="" width="80"></span>
</div>
</div>
</div> -->
<!-- <div class="row">
<div class="col-lg-6 d-flex">
<div class="card flex-fill">
<div class="card-header">
<div class="row align-items-center">
<div class="col-auto">
<div class="page-title">
Students Survay
</div>
</div>
<div class="col text-right">
<div class=" mt-sm-0 mt-2">
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
<div id="chart1"></div>
</div>
</div>
</div>
<div class="col-lg-6 d-flex">
<div class="card flex-fill">
<div class="card-header">
<div class="row align-items-center">
<div class="col-auto">
<div class="page-title">
Student Performance
</div>
</div>
<div class="col text-right">
<div class=" mt-sm-0 mt-2">
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
<div id="chart2"></div>
</div>
</div>
</div>
</div>
<div class="row">
<div class="col-lg-6 col-md-12 col-12 d-flex">
<div class="card flex-fill">
<div class="card-header">
<div class="row align-items-center">
<div class="col-auto">
<div class="page-title">
Events
</div>
</div>
<div class="col text-right">
<div class=" mt-sm-0 mt-2">
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
<div id="calendar" class=" overflow-hidden"></div>
</div>
</div>
</div>
<div class="col-lg-6 col-md-12 col-12 d-flex">
<div class="card flex-fill">
<div class="card-header">
<div class="row align-items-center">
<div class="col-auto">
<div class="page-title">
Total Member
</div>
</div>
<div class="col text-right">
<div class=" mt-sm-0 mt-2">
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
<div class="card-body d-flex align-items-center justify-content-center overflow-hidden">
<div id="chart3"> </div>
</div>
</div>
</div>
</div>
<div class="row">
<div class="col-lg-6 col-md-12 col-12 d-flex">
<div class="card flex-fill">
<div class="card-header">
<div class="row align-items-center">
<div class="col-auto">
<div class="page-title">
Income Monthwise
</div>
</div>
<div class="col text-right">
<div class=" mt-sm-0 mt-2">
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
<div id="chart4"></div>
</div>
</div>
</div>
<div class="col-lg-6 d-flex">
<div class="card flex-fill">
<div class="card-header">
<div class="row align-items-center">
<div class="col-auto">
<div class="page-title">
Exam List
</div>
</div>
<div class="col text-right">
<div class=" mt-sm-0 mt-2">
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
<div class="col-md-12">
<div class="table-responsive">
<table class="table custom-table">
<thead class="thead-light">
<tr>
<th>Exam Name </th>
<th>Subject</th>
<th>Class</th>
<th>Section</th>
<th>Time</th>
<th>Date</th>
<th class="text-right">Action</th>
</tr>
</thead>
<tbody>
<tr>
<td>
<a href="#" class="avatar bg-green">C</a>
</td>
<td>English</td>
<td>5</td>
<td>B</td>
<td>10.00am</td>
<td>20/1/2019</td>
<td class="text-right">
<a href="#" class="btn btn-primary btn-sm mb-1">
<i class="far fa-edit"></i>
</a>
<button type="submit" data-toggle="modal" data-target="#delete_employee" class="btn btn-danger btn-sm mb-1">
<i class="far fa-trash-alt"></i>
</button>
</td>
</tr>
<tr>
<td>
<a href="#" class="avatar bg-purple">C</a>
</td>
<td>English</td>
<td>4</td>
<td>A</td>
<td>10.00am</td>
<td>2/1/2019</td>
<td class="text-right">
<a href="#" class="btn btn-primary btn-sm mb-1">
<i class="far fa-edit"></i>
</a>
<button type="submit" data-toggle="modal" data-target="#delete_employee" class="btn btn-danger btn-sm mb-1">
<i class="far fa-trash-alt"></i>
</button>
</td>
</tr>
<tr>
<td>
<a href="#" class="avatar bg-green">C</a>
</td>
<td>Maths</td>
<td>6</td>
<td>B</td>
<td>10.00am</td>
<td>2/1/2019</td>
<td class="text-right">
<a href="#" class="btn btn-primary btn-sm mb-1">
<i class="far fa-edit"></i>
</a>
<button type="submit" data-toggle="modal" data-target="#delete_employee" class="btn btn-danger btn-sm mb-1">
<i class="far fa-trash-alt"></i>
</button>
</td>
</tr>
<tr>
<td>
<a href="#" class="avatar bg-dark">C</a>
</td>
<td>Science</td>
<td>3</td>
<td>B</td>
<td>10.00am</td>
<td>20/1/2019</td>
<td class="text-right">
<a href="#" class="btn btn-primary btn-sm mb-1">
<i class="far fa-edit"></i>
</a>
<button type="submit" data-toggle="modal" data-target="#delete_employee" class="btn btn-danger btn-sm mb-1">
<i class="far fa-trash-alt"></i>
</button>
</td>
</tr>
<tr>
<td>
<a href="#" class="avatar bg-green">C</a>
</td>
<td>Maths</td>
<td>6</td>
<td>B</td>
<td>10.00am</td>
<td>20/1/2019</td>
<td class="text-right">
<a href="#" class="btn btn-primary btn-sm mb-1">
<i class="far fa-edit"></i>
</a>
<button type="submit" data-toggle="modal" data-target="#delete_employee" class="btn btn-danger btn-sm mb-1">
<i class="far fa-trash-alt"></i>
</button>
</td>
</tr>
<tr>
<td>
<a href="#" class="avatar bg-dark">C</a>
</td>
<td>English</td>
<td>7</td>
<td>B</td>
<td>10.00am</td>
<td>20/1/2019</td>
<td class="text-right">
<a href="#" class="btn btn-primary btn-sm mb-1">
<i class="far fa-edit"></i>
</a>
<button type="submit" data-toggle="modal" data-target="#delete_employee" class="btn btn-danger btn-sm mb-1">
<i class="far fa-trash-alt"></i>
</button>
</td>
</tr>
<tr>
<td>
<a href="#" class="avatar bg-purple">C</a>
</td>
<td>Science</td>
<td>5</td>
<td>B</td>
<td>10.00am</td>
<td>20/1/2019</td>
<td class="text-right">
<a href="#" class="btn btn-primary btn-sm mb-1">
<i class="far fa-edit"></i>
</a>
<button type="submit" data-toggle="modal" data-target="#delete_employee" class="btn btn-danger btn-sm mb-1">
<i class="far fa-trash-alt"></i>
</button>
</td>
</tr>
</tbody>
</table>
</div>
</div>
</div>  
</div>
</div>
</div>
</div> -->

<!--------------------------- Admission and Enrolled Table here --------------------------->

<!-- <div class="row">
<div class="col-lg-12">
<div class="card">
<div class="card-header">
<div class="row align-items-center">
<div class="col-sm-6">
 <div class="page-title">
Admissions
</div>
</div>
<div class="col-sm-6 text-sm-right">
<div class=" mt-sm-0 mt-2">
<a href="export_admission.php"><button class="btn btn-outline-primary mr-2"><img src="assets/img/excel.png" alt=""><span class="ml-2">Excel</span></button></a>
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
<div class="table-responsive">
<table class="table custom-table">
<thead class="thead-light">
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
</thead> -->
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
</div>



<div class="row">
<div class="col-12">
<div class="card">
<div class="card-header">
<div class="row align-items-center">
<div class="col-sm-6">
<div class="page-title">
Enrolled
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
<table class="table custom-table"> -->

  <!-----------------View of Enrollees--------------------->

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

    $query2 = "SELECT * FROM enroll";
    $statement2 = $conn->prepare($query2);
    $statement2->execute();

    $result2 = $statement2->fetchAll();
    if($result2)
    {
      foreach($result2 as $row2)
      {
        ?>

          <tr>
            <td><?= $row2['fname']; ?></td>
            <td><?= $row2['lname']; ?></td>
            <td><?= $row2['birthday']; ?></td>
            <td><?= $row2['gender']; ?></td>
            <td><?= $row2['email']; ?></td>
            <td><?= $row2['phonenumber']; ?></td>
            <td><?= $row2['grade']; ?></td>
            <td>
              <a href="enroll_update.php?id=<?= $row2['id'] ?>"  class="btn btn-primary btn-sm mb-1">
              <i class="far fa-edit"></i>
              </a>
            <form action="process.php" method="POST">
              <button type="submit" class="btn btn-danger btn-sm mb-1" onclick="return confirm('Are you sure you want to archive this data?');" name="archive" value="<?= $row2['id'] ?>">
              <i class="far fa-trash-alt"></i>
              </button>
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
</div>
</div>
</div> -->

<!--------------------------- Admission and Enrolled Table End here --------------------------->

<!--------------------------------- ARCHIVES --------------------------------------->

<div class="row">
<div class="col-12">
<div class="card">
<div class="card-header">
<div class="row align-items-center">
<div class="col-sm-6">
<div class="page-title">
Student Archives
</div>
</div>
<div class="col-sm-6 text-sm-right">
<div class=" mt-sm-0 mt-2">
<a href="export_archive.php"><button class="btn btn-outline-primary mr-2" name="excel"><img src="assets/img/excel.png" alt=""><span class="ml-2">Excel</span></button></a>
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

<!-- DataTable Initialization Script -->
<script>
  $(document).ready(function() {
    var table = $('#archiveTable').DataTable({
      "paging": true,
      "searching": true,
      "info": true,
      "ordering": true,
      "lengthMenu": [5, 10, 25, 50],
      "pageLength": 10,
      dom: '<"top"lfB>rt<"bottom"p>',
      buttons: [
        'copy',
        'csv',
        'excel',
        'pdf',
        'print'
      ]
    });
  });
</script>

  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-12">
      <div class="table-responsive">
        <table id="archiveTable" class="table custom-table">
          <!-----------------View of Archive--------------------->
          <thead class="thead-light">
            <tr>
              <th class="text-center">First Name</th>
              <th class="text-center">Last Name</th>
              <th class="text-center">LRN Number</th>
              <th class="text-center">Phone Number</th>
              <th class="text-center">Email</th>
              <th class="text-center">Grade Level</th>
              <th class="text-center">Action</th>
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
                <td class="text-center"><?= $row3['firstname']; ?></td>
                <td class="text-center"><?= $row3['lastname']; ?></td>
                <td class="text-center"><?= $row3['LRN']; ?></td>
                <td class="text-center"><?= $row3['phonenumber']; ?></td>
                <td class="text-center"><?= $row3['emailaddress']; ?></td>
                <td class="text-center"><?= $row3['grade']; ?></td>
                <td class="text-center">
                  <form action="process.php" method="POST">
                    <!----------------- Button trigger modal --------------->
                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#exampleModal<?=  $row3['id'];  ?>">
                      <i class="bi bi-receipt-cutoff"></i>
                    </button>
                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#payment<?=  $row3['id'];  ?>">
                      <i class="bi bi-wallet-fill"></i>
                    </button>
                    <button type="submit" class="btn btn-primary btn mb-1" onclick="return confirm('Are you sure you want to recover this data?');" name="return" value="<?= $row3['id'] ?>">
                      <i class="fa fa-redo"></i>
                    </button>

                    <!-- Modal for Detailed Information -->
                    <div class="modal fade" id="exampleModal<?=  $row3['id'];  ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                      <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Detailed Information</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body">
                            <div class="row">
                              <div class="col">
                                <span class="h3"><b>Name: </b> <?=  $row3['firstname'];  ?> <?= $row3['lastname']; ?></span> <br><br>
                                <b>Birthday: </b> <?= $row3['birthday']; ?><br>
                                <b>PSA Number: </b> <?= $row3['PSA']; ?><br>
                                <b>Religion: </b> <?= $row3['religion']; ?><br>
                                <b>Home Address: </b><?= $row3['homeaddress']; ?><br>
                                <b>Grade Level: </b><?= $row3['grade']; ?><br>
                                <b>Father's Name: </b><?= $row3['fatherName']; ?><br>
                                <b>Mobile #: </b><?= $row3['fatherNumber']; ?><br><br>
                                <b>Mother' Name: </b><?= $row3['motherName']; ?><br>
                                <b>Mobile #: </b><?= $row3['motherNumber']; ?><br>
                              </div>
                            </div>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Modal for Payment-->
                    <div class="modal fade" id="payment<?=  $row3['id'];  ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Payment Details</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body">
                            <div class="text-center">
                              <span class="h3"><b>Name: </b> <?=  $row3['firstname'];  ?> <?= $row3['lastname']; ?></span> <br><br><br>
                              Screenshot of payment:<br><br>
                              <img src="img/gcash.jpg" width="80" height="120" class="img-fluid zoom-img" data-toggle="modal" data-target="#imageModal" alt="Sample Image"><br><br><br>
                              <b>Reference Number:</b> 5006 428 656516

                              <!-- Modal for making the image bigger -->
                              <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                                  <div class="modal-content">
                                    <div class="modal-header">
                                      <h5 class="modal-title" id="exampleModalLabel">Proof of Payment</h5>
                                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                      </button>
                                    </div>
                                    <div class="modal-body">
                                      <img src="img/gcash.jpg" class="img-fluid" alt="Sample Image">
                                    </div>
                                    <div class="modal-footer">
                                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </form>
                </td>
              </tr>
            <?php
              }
            } else {
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