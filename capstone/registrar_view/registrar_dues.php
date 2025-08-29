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
 


// Get the current month and year
$currentMonth = date('m');
$currentYear = date('Y');

// Fetch all enrolled students and their payment status
$query = "
    SELECT e.id, e.firstname, e.lastname, e.emailaddress, e.grade, 
           IFNULL(SUM(p.amount), 0) AS total_paid
    FROM enroll e
    LEFT JOIN payments p ON e.id = p.admission_id 
                          AND MONTH(p.created_at) = :currentMonth 
                          AND YEAR(p.created_at) = :currentYear
    GROUP BY e.id
    HAVING total_paid = 0
";
$statement = $conn->prepare($query);
$statement->bindParam(':currentMonth', $currentMonth);
$statement->bindParam(':currentYear', $currentYear);
$statement->execute();
$students = $statement->fetchAll(PDO::FETCH_ASSOC);





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
<a href="registrar.php"><img src="assets/img/sidebar/icon-1.png" alt="icon"><span>Dashboard</span></a>
</li>
<li class="submenu">
  <a href="javascript:void(0);"><img src="assets/img/sidebar/icon-2.png" alt="icon"> <span> Admission <span class="badge rounded-pill text-bg-danger"> <?php echo $admissionCount; ?></span></span> <span class="menu-arrow"></span></a>
  <ul class="list-unstyled" style="display: none;">
  <li><a href="list_due.php"><span>New Admissions <span class="badge rounded-pill text-bg-danger"> <?php echo $admissionCount; ?></span></span></a></li>
  <li><a href="old_admission_table.php"><span>Readmission</span></a></li>
  </ul>
</li>
<!-- <li class="active">
  <a href="enrolled_payments.php"><img src="assets/img/sidebar/icon-10.png" alt="icon"> <span> Students</span> <span class=""></span></a>
</li> -->
<li class="submenu">
  <a class="active" href="javascript:void(0);"><img src="assets/img/sidebar/icon-11.png" alt="icon"><span> Payments </span> <span class="menu-arrow"></span></a>
    <ul class="list-unstyled" style="display: none;">
    <li><a href="otc_registrar.php"><span>Over the Counter</span></a></li>
    <li><a  href="enrolled_payments.php"><span>History</span></a></li>
    <li><a class="active text-decoration-underline text-black" href="registrar_dues.php"><span>Dues</span></a></li>
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
Upcoming Payments Dues 
</div>
</div>

</div>
</div>
<div class="card-body">
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
                table.column(2).data().each(function(value, index) {
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
                    table.column(2).search(selectedGrade).draw();
                });
            });
        </script>

        <!-- Filter Dropdown -->
        <div class="d-flex align-items-center mb-3">
            <label for="gradeFilter" class="me-2">Filter By Grade:</label>
            <select id="gradeFilter" class="form-control" style="width: 200px;">
                <option value="">All</option>
            </select>
        </div>

        <div class="table-responsive">
            <table id="enrollmentTable" class="table custom-table text-center">
                <thead class="thead-light">
                    <tr>
                        <th>Full Name</th>
                        <th>Email Address</th>
                        <th>Student Number</th>
                        <th>Grade</th>
                        <th>Monthly Payment Due</th>
                        <th>Next Due Date</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($students as $student) {
                        // Fetch the grade of the student 
                        $grade = $student['grade'];

                        // Fetch tuition data for the student
                        $sql4 = "SELECT `install_monthly` FROM tuition WHERE `grade_level` = :grade";
                        $statement4 = $conn->prepare($sql4);
                        $statement4->execute([':grade' => $grade]);
                        $tuitionData = $statement4->fetch(PDO::FETCH_ASSOC);

                        // Initialize monthly payment due variable
                        $monthlyPaymentDue = 0;

                        if ($tuitionData !== false) {
                            $monthlyPaymentDue = number_format((float)$tuitionData['install_monthly'], 2, '.', ',');
                        } else {
                            echo "No tuition data found for grade: " . htmlspecialchars($grade);
                        }

                        // Fetch the first created_at date from the payments table
                        $sql6 = "SELECT MIN(`created_at`) as first_date FROM payments WHERE `admission_id` = :id";
                        $statement6 = $conn->prepare($sql6);
                        $statement6->execute([':id' => $student['id']]);
                        $firstPaymentDate = $statement6->fetchColumn();

                        // Initialize next due date variable
                        $nextDueDate = '';

                        if ($firstPaymentDate) {
                            // Convert the date to a DateTime object
                            $firstDateTime = new DateTime($firstPaymentDate);
                            // Calculate the next due date (one month after the first payment date)
                            $firstDateTime->modify('+1 month');
                            // Format the date as MM/DD/YYYY
                            $nextDueDate = $firstDateTime->format('m/d/Y');
                        } else {
                            echo "No payments found.";
                        }
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($student['firstname'] . ' ' . $student['lastname']); ?></td>
                        <td><?php echo htmlspecialchars($student['emailaddress']); ?></td>
                        <td><?= htmlspecialchars($student['id']); ?></td>
                        <td><?= htmlspecialchars($student['grade']); ?></td>
                        <td><?= $monthlyPaymentDue; ?></td>
                        <td><?= $nextDueDate; ?></td>
                        <td>
    <form action="send_email.php" method="POST" style="display:inline;">
        <input type="hidden" name="student_id" value="<?= htmlspecialchars($student['id']); ?>">
        <button type="submit" name="send_email" class="btn btn-primary">
            <i class="bi bi-envelope"></i> Send Email
        </button>
    </form>
</td>
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