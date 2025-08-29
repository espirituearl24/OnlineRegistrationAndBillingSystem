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
$query2 = "SELECT * FROM payments WHERE status ='Enrolled'";


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
<a class="dropdown-item" href="../index.php" >Logout</a>
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
<a href="principal.php"><img src="assets/img/sidebar/icon-1.png" alt="icon"><span>Dashboard</span></a>
</li>
<li class="submenu">
  <a href="#"><img src="assets/img/sidebar/icon-3.png" alt="icon"><span> Registrar </span> <span class="menu-arrow"></span></a>
    <ul class="list-unstyled" style="display: none;">
    <li><a href="p_list.php"><span>Admission Info</span></a></li>
    <li><a href="p_rstudent.php"><span>Students</span></a></li>
    <li><a href="p_account.php"><span>Accounts</span></a></li>
    <li><a href="p_event.php"><span></span>Events</a></li>
  </ul>
</li>

<li class="submenu">
  <a href="#"><img src="assets/img/sidebar/icon-3.png" alt="icon"><span> Accounting </span> <span class="menu-arrow"></span></a>
  <ul class="list-unstyled" style="display: none;">
    <!-- Payments dropdown inside Accounting -->
    <li class="submenu ">
      <a href="javascript:void(0);"><img src="assets/img/sidebar/icon-11.png" alt="icon"><span> Payments </span> <span class="menu-arrow"></span></a>
      <ul class="list-unstyled" style="display: none;">
        <li><a class="active text-decoration-underline text-black" href="p_enrolled_payments.php"><span>History</span></a></li>
        <li><a href="registrar_dues.php"><span>Dues</span></a></li>   
      </ul>
    </li>
  </ul>
</li>

<!-- <li class="">
  <a href="pt_payment.php"><img src="assets/img/sidebar/icon-11.png" alt="icon"> <span> Payments</span> <span class=""></span></a>
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
Student - History Payments
</div>
</div>

</div>
</div>
<div class="card-body">
    <!-- DataTable Initialization Script -->
    <script>
        $(document).ready(function() {
            var table = $('#enrollmentTable').DataTable({
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
        <label for="gradeFilter" class="me-2">Filter By Payment Mode:</label>
        <select id="gradeFilter" class="form-control" style="width: 200px;">
            <option value="">All</option>
            <!-- Options will be populated dynamically -->
        </select>
    </div>


    <!-- Table Start -->
    <div class="table-responsive">
        <table id="enrollmentTable" class="table custom-table text-center">
            <thead class="thead-light">
                <tr>
                    <th>Admission ID</th>
                    <th>Full Name</th>
                    <th>Reference No.</th>
                    <th>Amount</th>
                    <th>Date & Time</th>
                    <th>Payment Mode</th>

                </tr>
            </thead>
            <tbody>
                <?php
                // Check if any results were returned
                if($result2) {
                    foreach($result2 as $row2) {
                ?>
                    <tr>
                        <td><?= $row2['admission_id']; ?></td>
                        <td><?= $row2['fullName']; ?></td>
                        <td><?= $row2['referenceNumber']; ?></td>
                        <td><?= $row2['amount']; ?></td>
                        <td><?= $row2['created_at']; ?></td>
                        <td><?= $row2['payment_mode']; ?></td>

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
                                    // Fetch the full name from the enroll table using admission_id
                                    $admissionId = $row2['admission_id'];
                                    $nameQuery = "SELECT firstname, lastname FROM enroll WHERE id = :admissionId";
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