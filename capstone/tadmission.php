<?php


session_start(); 

include 'dbconnection.php';

$user = $_SESSION['lastname'];
$userl = $_SESSION['lastname'];
$userf = $_SESSION['firstname'];
 

//calculate how many days
$currenttime = date('Y-m-d');
$countdate = date('Y-m-d', strtotime('-30 days'));
// $countdate = date('Y-m-d', strtotime('-1 day'));


//Lipat ng archive
$insert = "INSERT INTO `archive_admission`(`id`, `firstname`, `lastname`, `middlename`, `LRN`, `birthday`, `PSA`, `religion`, `gender`, `phonenumber`, `emailaddress`, `homeaddress`, `specialED`, `grade`, `withLRN`, `lastGradelevel`, `lastSY`, `lastSchool`, `schoolAddress`, `schoolType`, `fatherName`, `fatherSchool`, `fatherJob`, `fatherNumber`, `motherName`, `motherSchool`, `motherJob`, `motherNumber`, `currentdate`) SELECT `id`, `firstname`, `lastname`, `middlename`, `LRN`, `birthday`, `PSA`, `religion`, `gender`, `phonenumber`, `emailaddress`, `homeaddress`, `specialED`, `grade`, `withLRN`, `lastGradelevel`, `lastSY`, `lastSchool`, `schoolAddress`, `schoolType`, `fatherName`, `fatherSchool`, `fatherJob`, `fatherNumber`, `motherName`, `motherSchool`, `motherJob`, `motherNumber`, '$currenttime' FROM `admission` WHERE currentdate < :countdate ";
$insertstm = $conn->prepare($insert);
$insertstm->bindParam(':countdate',$countdate);
$insertstm->execute();

//Delete sa table
$sql = 'DELETE FROM admission WHERE currentdate < :countdate';
$stm = $conn->prepare($sql);
$stm->bindParam(':countdate',$countdate); 
$stm->execute();



?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>GBA | Registrar</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

<!----Jtable---->
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
  
  <style>
    /* Additional CSS for zooming effect */
    .zoom-img {
      cursor: pointer;
      transition: transform 0.5s ease;
    }

    .zoom-img:hover {
      transform: scale(1.2);
    }
/* Align buttons and search input in the same row */
.dataTables_wrapper .top {
    display: flex;
    justify-content: flex-start; /* Align items to the start */
    align-items: center;
    flex-wrap: wrap; /* Allow wrapping if necessary */
}

/* Adjust button spacing */
.dt-buttons {
    margin-right: 10px; /* Space between buttons */
}

/* Adjust search box spacing */
.dataTables_filter {
    margin-left: 10px; /* Space between search bar and buttons */
    display: flex; /* Use flex to align input and button */
    align-items: center; /* Center items vertically */
}

/* Style the search input */
.dataTables_filter input {
    width: 150px; /* Adjust width as necessary */
    margin-right: 5px; /* Space between input and button */
}

/* Style the search button */
.dataTables_filter button {
    /* You can add additional styling for the button here if needed */
}

/* Ensure the length menu is aligned properly */
.dataTables_length {
    margin-right: 10px; /* Space between length menu and search */
}

  </style>

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
<a href="admin.php"><img src="assets/img/sidebar/icon-1.png" alt="icon"><span>Dashboard</span></a>
</li>
<li class="submenu">
  <a href="javascript:void(0);"><img src="assets/img/sidebar/icon-2.png" alt="icon"> <span> Admission</span> <span class="menu-arrow"></span></a>
  <ul class="list-unstyled" style="display: none;">
  <li><a class="active" href="tadmission.php"><span>New Admissions</span></a></li>
  <li><a href="old_admission_table.php"><span>Readmission</span></a></li>
  </ul>
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
<li class="">
  <a href="account_table.php"><img src="assets/img/sidebar/icon-4.png" alt="icon"> <span>Accounts</span> <span class=""></span></a>
</li>

<li class="">
  <a href="events.php"><img src="assets/img/sidebar/icon-6.png" alt="icon"> <span>Events</span> <span class=""></span></a>
</li>
<!-- <li class="">
  <a href="email.php"><img src="assets/img/sidebar/icon-17.png" alt="icon"> <span>Email</span> <span class=""></span></a>
</li> -->


</ul>
</div>
</div>
</div>
<!---------------SIDE BAR END---------------->

<div class="page-wrapper">
<div class="content container-fluid">

<div class="page-header">

<div class="row">
<div class="col-lg-12">
<div class="card">
<div class="card-header">
<div class="row align-items-center">
<div class="col-sm-6">
 <div class="page-title">
Admissions
</div>
</div>
<!-- <div class="col-sm-6 text-sm-right">
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
</div> -->
</div>
</div>
<div class="card-body">
    <script>
        $(document).ready(function() {
            var table = $('#myTable').DataTable({
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

            // Populate the Grade Level filter
            var grades = [];
            table.column(5).data().each(function(value, index) {
                if ($.inArray(value, grades) === -1) {
                    grades.push(value);
                }
            });

            // Append options to the select input
            $.each(grades, function(index, value) {
                $('#gradeFilter').append($('<option></option>').attr('value', value).text(value));
            });

            // Filter the table based on the selected grade
            $('#gradeFilter').on('change', function() {
                var selectedGrade = $(this).val();
                table.column(5).search(selectedGrade).draw();
            });
        });
    </script>

    <!-- Buttons and Filter Dropdown in a Flex Container -->
    <div class="d-flex align-items-center mb-3">
        <label for="gradeFilter" class="me-2">Filter By Grade Level:</label>
        <select id="gradeFilter" class="form-control" style="width: 200px;">
            <option value="">All</option>
            <!-- Options will be populated dynamically -->
        </select>
    </div>

    <div class="table-responsive">
        <table id="myTable" class="table custom-table text-center"> 
            <thead class="thead-light">
                <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>LRN Number</th>
                    <th>Phone Number</th>
                    <th>Email</th>
                    <th>Grade Level</th>
                    <th>Date Created</th>
                    <th class="text-right">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Example of fetching data from the database
                $query2 = "SELECT * FROM admission";
                $statement2 = $conn->query($query2);
                $result2 = $statement2->fetchAll();

                if ($result2) {
                    foreach ($result2 as $row2) {
                ?>
                    <tr>
                        <td><?= $row2['firstname']; ?></td>
                        <td><?= $row2['lastname']; ?></td>
                        <td><?= $row2['LRN']; ?></td>
                        <td><?= $row2['phonenumber']; ?></td>
                        <td>    <a href="email.php?email=<?= urlencode($row2['emailaddress']); ?>" class="text-info">
        <?= $row2['emailaddress']; ?>
    </a></td>
                        <td><?= $row2['grade']; ?></td>
                        <td><?= $row2['currentdate']; ?></td>
                        <td>
                            <form action="process.php" method="POST">
                                            <!-- Button to trigger modal for detailed information -->
                                            <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#exampleModal<?= $row2['id']; ?>">
                                                <i class="bi bi-receipt-cutoff"></i>
                                            </button>
                            </form>
                        </td>
                    </tr> 
<!-- Modal for Detailed Information -->
<div class="modal fade" id="exampleModal<?= $row2['id']; ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Detailed Information</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Left Column -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <span class="h4"><strong>Name:</strong> <?= $row2['firstname']; ?> <?= $row2['lastname']; ?></span>
                        </div>
                        <div class="mb-3">
                            <strong>Birthday:</strong> <?= $row2['birthday']; ?>
                        </div>
                        <div class="mb-3">
                            <strong>PSA Number:</strong> <?= $row2['PSA']; ?>
                        </div>
                        <div class="mb-3">
                            <strong>Religion:</strong> <?= $row2['religion']; ?>
                        </div>
                        <div class="mb-3">
                            <strong>Home Address:</strong> <?= $row2['homeaddress']; ?>
                        </div>
                        <div class="mb-3">
                            <strong>Grade Level:</strong> <?= $row2['grade']; ?>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <strong>Father's Name:</strong> <?= $row2['fatherName']; ?>
                        </div>
                        <div class="mb-3">
                            <strong>Mobile #:</strong> <?= $row2['fatherNumber']; ?>
                        </div>
                        <div class="mb-3">
                            <strong>Mother's Name:</strong> <?= $row2['motherName']; ?>
                        </div>
                        <div class="mb-3">
                            <strong>Mobile #:</strong> <?= $row2['motherNumber']; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Student Documents Section -->
                <div class="row mt-4">
                    <div class="col">
                        <h5>Student Documents</h5>
                        <ul class="list-unstyled">
    <?php if ($row2['reportCard']): ?>
        <li>
            Report Card - <a href="<?= $row2['reportCard']; ?>" target="_blank" class="btn btn-primary p-1">View</a>
        </li>
    <?php endif; ?>
    <?php if ($row2['goodMoral']): ?>
        <li> 
            Good Moral Certificate - <a href="<?= $row2['goodMoral']; ?>" target="_blank" class="btn btn-primary p-1 mb-2">View</a>
        </li>
    <?php endif; ?>
    <?php if ($row2['birthCert']): ?>
        <li class="">
            Birth Certificate (PSA) - <a href="<?= $row2['birthCert']; ?>" target="_blank" class="btn btn-primary p-1">View</a>
        </li>
    <?php endif; ?>
    <?php if ($row2['id_pic']): ?>
        <li>
            ID Picture - <a href="<?= $row2['id_pic']; ?>" target="_blank" class="btn btn-primary p-1">View</a>
        </li>
    <?php endif; ?>
    <?php if ($row2['medCert']): ?>
        <li>
            Medical Certificate - <a href="<?= $row2['medCert']; ?>" target="_blank" class="btn btn-primary p-1">View</a>
        </li>
    <?php endif; ?>
</ul>

                    </div>
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



<!--------------------------- Enrollees and Archive Table here --------------------------->

<!-- <div class="row">
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

<!------------- ARCHIVES --------------------------------------->

<!-- <div class="row">
<div class="col-12">
<div class="card">
<div class="card-header">
<div class="row align-items-center">
<div class="col-sm-6">
<div class="page-title">
Archives
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
<div class="row">
<div class="col-lg-12 col-md-12 col-sm-12 col-12">
<div class="table-responsive">
<table class="table custom-table"> -->

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