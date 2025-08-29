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



// Initialize variables for filtering
$start_date = isset($_POST['start_date']) ? $_POST['start_date'] : '';
$end_date = isset($_POST['end_date']) ? $_POST['end_date'] : '';


// Add this line to define the number of entries per page
$entries_per_page = isset($_POST['entries_per_page']) ? (int)$_POST['entries_per_page'] : 10;

// Base query
$query2 = "SELECT * FROM payments WHERE payment_terms = 'admission fee' AND status = 'Admission' ";

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

// Add the limit clause to the query before preparing it
$query2 .= " LIMIT :limit";

// Prepare the query
$statement2 = $conn->prepare($query2);

if (!empty($start_date) && !empty($end_date)) {
    $statement2->bindParam(':start_month', $start_month);
    $statement2->bindParam(':end_month', $end_month);
    $statement2->bindParam(':start_year', $start_year);
    $statement2->bindParam(':end_year', $end_year);
}

// Bind the limit parameter before executing the query
$statement2->bindParam(':limit', $entries_per_page, PDO::PARAM_INT);

// Execute the query
$statement2->execute();
$result2 = $statement2->fetchAll();


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
<a href="registrar.php" class="mobile-logo d-md-block d-lg-none d-block"><img src="img/logo1.png" alt="" width="30" height="30"></a>
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
  <li><a href="list_due.php"><span>New Admissions</span></a></li>
  <li><a class="active text-decoration-underline text-black" href="old_admission_table.php"><span>Readmission</span></a></li>
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
  <a href="javascript:void(0);"><img src="assets/img/sidebar/icon-3.png" alt="icon"><span> Archive </span> <span class="menu-arrow"></span></a>
    <ul class="list-unstyled" style="display: none;">
    <li><a href="tarchive_admission.php"><span>Admission</span></a></li>
    <li><a href="tarchive_estudent.php"><span>Student</span></a></li>
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
 

<div class="row">
<div class="col-12">
<div class="card">
<div class="card-header">
<div class="row align-items-center">
<div class="col-sm-6">
<div class="page-title">
Admission Info
</div>
</div>

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

    // Check if there are any records in the table
    var data = table.rows().data();
    if (data.length === 0) {
        // If no records, disable the filter dropdown
        $('#gradeFilter').prop('disabled', true);
    } else {
        // If there are records, populate the Grade Level filter
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

        // Enable the filter dropdown
        $('#gradeFilter').prop('disabled', false);

        // Filter the table based on the selected grade
        $('#gradeFilter').on('change', function() {
            var selectedGrade = $(this).val();
            table.column(5).search(selectedGrade).draw();
        });
    }
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
                    <th>Student ID</th>
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
                $query2 = "SELECT old_admission.*, payments.* 
           FROM old_admission 
           LEFT JOIN payments ON old_admission.id = payments.admission_id";

                $statement2 = $conn->query($query2);
                $result2 = $statement2->fetchAll();

                if ($result2) {
                    foreach ($result2 as $row2) {
                ?>
                    <tr>
                        <td><?= $row2['student_id']; ?></td>
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
                        <button type="button" class="btn btn-info btn-sm mb-1" data-bs-toggle="modal" data-bs-target="#receiptModal<?= $row2['admission_id']; ?>">
                                <i class="bi bi-receipt"></i>
                            </button>
    <button type="submit" name="save_add" class="btn btn-success btn-sm mb-1" value="<?= $row2['id']; ?>">
    <i class="bi bi-check-square"></i>
    </button>
    <button type="submit" name="archive_addmission" class="btn btn-danger btn-sm mb-1" value="<?= $row2['id']; ?>">
        <i class="fas fa-archive"></i>
    </button>

</form>

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
                <img src="../receipts/<?= $row2['receiptImage']; ?>" alt="Receipt" class="img-fluid">

                <?php
                // Fetch the payment details using the admission_id
                $admissionId = $row2['admission_id'];
                $paymentQuery = "SELECT referenceNumber, amount, created_at, payment_mode 
                                 FROM payments 
                                 WHERE admission_id = :admissionId";
                $stmt = $conn->prepare($paymentQuery);
                $stmt->execute(['admissionId' => $admissionId]);
                $paymentDetails = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($paymentDetails) {
                    // Extract payment data
                    $referenceNumber = $paymentDetails['referenceNumber'];
                    $amount = $paymentDetails['amount'];
                    $createdAt = $paymentDetails['created_at'];
                    $paymentMode = $paymentDetails['payment_mode'];

                    // Format the created_at datetime field
                    $dateTime = new DateTime($createdAt);
                    // Format to 'Month Day, Year - H:iAM/PM'
                    $formattedDate = $dateTime->format('F j, Y - g:iA');  // Example: November 11, 2024 - 9:00am
                } else {
                    $referenceNumber = $amount = $createdAt = $paymentMode = 'No Payment Info Available';
                    $formattedDate = '';
                }
                ?>

                <!-- Display Payment Details -->
                <div class="mt-3">
                    <strong>Reference Number: </strong> <?= htmlspecialchars($referenceNumber); ?>
                </div>
                <div class="mt-3">
                    <strong>Amount Paid: ₱</strong> <?= htmlspecialchars($amount); ?>
                </div>
                <div class="mt-3">
                    <strong>Payment Date: </strong> <?= htmlspecialchars($formattedDate); ?>
                </div>
                <div class="mt-3">
                    <strong>Payment Mode: </strong> <?= htmlspecialchars($paymentMode); ?>
                </div>

                <!-- Display the full name of the student -->
                <?php
                // Fetch the full name of the student (already fetched previously)
                $admissionId = $row2['admission_id'];
                $nameQuery = "SELECT firstname, lastname FROM old_admission WHERE id = :admissionId";
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