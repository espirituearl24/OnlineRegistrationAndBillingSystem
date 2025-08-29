<?php


session_start();

include 'dbconnection.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

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

// Initialize variables
$studentInfo = [];
$admissionInfo = [];
$otc_id = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search'])) {
    if (isset($_POST['studentNumber']) && !empty($_POST['studentNumber'])) {
        $studentNumber = $_POST['studentNumber'];

        // Prepare and execute the query to find the student in the enroll table
        $stmt = $conn->prepare("SELECT id, firstname, lastname, grade FROM enroll WHERE student_id = :student_number");
        $stmt->bindParam(':student_number', $studentNumber);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $studentInfo = $stmt->fetch(PDO::FETCH_ASSOC);
            $otc_id = $studentInfo['id']; // Store the student ID from the enroll table
        } else {
            $_SESSION['error'] = "Student not found.";
            $_SESSION['status'] = "error";
        }
    } elseif (isset($_POST['admissionNumber']) && !empty($_POST['admissionNumber'])) {
        $admissionNumber = $_POST['admissionNumber'];

        // Prepare and execute the query to find the admission record
        $stmt = $conn->prepare("SELECT id, firstname, lastname, grade FROM admission WHERE id = :admission_number");
        $stmt->bindParam(':admission_number', $admissionNumber);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $admissionInfo = $stmt->fetch(PDO::FETCH_ASSOC);
            $otc_id = $admissionInfo['id']; // Store the admission ID
        } else {
 $_SESSION['error'] = "Admission record not found.";
            $_SESSION['status'] = "error";
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search_admission'])) {
    $admissionNumber = $_POST['admissionNumber'];

    // Prepare and execute the query to find the admission record
    $stmt = $conn->prepare("SELECT id, firstname, lastname, grade FROM admission WHERE id = :admission_number");
    $stmt->bindParam(':admission_number', $admissionNumber);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $admissionInfo = $stmt->fetch(PDO::FETCH_ASSOC);
        $otc_id = $admissionInfo['id'];
    } else {
        $_SESSION['error'] = "Admission record not found.";
        $_SESSION['status'] = "error";
    }
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_payment'])) {
    // Retrieve the OTC ID from the POST data 
    $otc_id = $_POST['otc_id']; // Get the OTC ID from the hidden input

    // Check if there is an existing discount for this admission_id
    $checkDiscountStmt = $conn->prepare("SELECT COUNT(*) FROM payments WHERE admission_id = :admission_id AND discount > 0");
    $checkDiscountStmt->bindParam(':admission_id', $otc_id);
    $checkDiscountStmt->execute();
    $discountExists = $checkDiscountStmt->fetchColumn() > 0;

    // Get the amount and discount to be inserted
    $amount = floatval($_POST['amount']); // Ensure amount is treated as a float
    $discount = floatval($_POST['discount']); // Ensure discount is treated as a float

    // Debugging output
    error_log("Debugging Info: discountExists = $discountExists, amount = $amount, discount = $discount");

    // Check the conditions for inserting payment
    if ($discountExists && $discount > 0) {
        $_SESSION['error'] = "The student already has a discount record!";
        $_SESSION['status'] = "error";
    } else {
        // Proceed with payment insertion
        $fullName = $_POST['fullName'];
        $receiptNumber = $_POST['receiptNumber'];
        $paymentTerms = $_POST['paymentTerms'];
        $paymentMode = 'OTC'; // Set the payment mode to OTC
        $status = 'Enrolled';

        // Prepare and execute the payment insertion
        $stmt = $conn->prepare("INSERT INTO payments (admission_id, fullName, referenceNumber, amount, payment_terms, discount, status, payment_mode) VALUES (:admission_id, :fullName, :referenceNumber, :amount, :payment_terms, :discount, :status, :payment_mode)");
        $stmt->bindParam(':admission_id', $otc_id);
        $stmt->bindParam(':fullName', $fullName);
        $stmt->bindParam(':referenceNumber', $receiptNumber);
        $stmt->bindParam(':amount', $amount);
        $stmt->bindParam(':payment_terms', $paymentTerms);
        $stmt->bindParam(':discount', $discount);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':payment_mode', $paymentMode); // Bind payment mode

        try {
            if ($stmt->execute()) {
                // Fetch the email address
                $emailStmt = $conn->prepare("SELECT emailaddress FROM admission WHERE id = :admission_id UNION SELECT emailaddress FROM enroll WHERE id = :admission_id");
                $emailStmt->bindParam(':admission_id', $otc_id);
                $emailStmt->execute();
                $email = $emailStmt->fetchColumn();

                // Prepare the email content
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'gba.admissions@gmail.com';
                $mail->Password = 'ylvn aiva nbcy jjut'; // Store this securely
                $mail->SMTPSecure = 'ssl';
                $mail->Port = 465;

                $mail->setFrom('gba.admissions@gmail.com', 'Admissions Office');
                $mail->addAddress($email);
                $mail->isHTML(true);

                $mail->Subject = 'Payment Confirmation';
                $mail->Body = "
                <html>
                <head>
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                            line-height: 1.6;
                            color: #333;
                        }
                        .container {
                            max-width: 600px;
                            margin: 0 auto;
                            padding: 20px;
                            border: 1px solid #ddd;
                            border-radius: 8px;
                            background-color: #f9f9f9;
                        }
                        .header {
                            text-align: center;
                            font-size: 18px;
                            font-weight: bold;
                            margin-bottom: 20px;
                            color: #0056b3;
                        }
                        .content {
                            font-size: 16px;
                        }
                        .footer {
                            margin-top: 20px;
                            font-size: 14px;
                            color: #555;
                        }
                        .highlight {
                            font-weight: bold;
                            color: #d9534f;
                        }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <div class='header'>Payment Confirmation</div>
                        <div class='content'>
                            Dear GBA Student,<br><br>
                            Your payment of <span class='highlight'>PHP {$amount}</span> has been successfully processed on 
                            <span class='highlight'>" . date('Y-m-d H:i:s') . "</span>.<br><br>
                            Thank you for your prompt payment!<br><br>
                            Should you have any questions or need further assistance, please do not hesitate to reach out to us.<br><br>
                        </div>
                        <div class='footer'>
                            Best regards,<br>
                            Grace Baptist Academy Accounting Office
                        </div>
                    </div>
                </body>
                </html>";
                $mail->send();

                $_SESSION['success'] = "Payment submitted successfully & confirmation email sent.";
                $_SESSION['status'] = "success";
            } else {
                $_SESSION['error'] = "Failed to submit payment.";
                $_SESSION['status'] = "error";
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Database error: " . $e->getMessage();
            $_SESSION['status'] = "error";
        } catch (Exception $e) {
            $_SESSION['error'] = "Email error: " . $mail->ErrorInfo;
            $_SESSION['status'] = "error ";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>GBA | Accountant</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


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
  <li><a href="list_due.php"><span>New Admissions <span class="badge rounded-pill text-bg-danger"> <?php echo $admissionCount; ?></span></span></a></li>
  <li><a href="old_admission_table.php"><span>Readmission</span></a></li>
  </ul>
</li>
<!-- <li class="">
  <a href="enrolled_payments.php"><img src="assets/img/sidebar/icon-10.png" alt="icon"> <span> Students</span> <span class=""></span></a>
</li> -->
<li class="submenu">
  <a href="javascript:void(0);"><img src="assets/img/sidebar/icon-11.png" alt="icon"><span> Payments </span> <span class="menu-arrow"></span></a>
    <ul class="list-unstyled" style="display: none;">
    <li><a class="active text-decoration-underline text-black" href="otc_registrar.php"><span>Over the Counter</span></a></li>
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
            Over the Counter Payment
          </div>
        </div>
      </div>
    </div>
    <div class="card-body">
    <form method="POST">
    <div class="row mb-3">
        <div class="col-md-3">
            <label for="searchType" class="">Search Type</label>
            <select id="searchType" class="form-select" onchange="toggleSearchInput()">
                <option value="">Select Search Type</option>
                <option value="student">Student ID</option>
                <option value="admission">Admission ID</option>
            </select>
        </div>
        <div class="col-md-3" id="searchInputContainer" style="display: none;">
            <label for="searchInput" class="">Search Input</label>
            <input type="text" name="searchInput" id="searchInput" class="form-control" required>
        </div>
        <div class="col-md-3 d-flex align-items-end" id="searchButtonContainer" style="display: none;">
            <button type="submit" name="search" class="btn btn-primary mt-4">Search</button>
        </div>
    </div>
</form>
    <div class="row mb-3">                                    
        <div class="col-md-3">
            <label for="name" class="">Name</label>
            <input type="text" id="name" class="form-control" value="<?php echo isset($studentInfo['firstname']) ? $studentInfo['firstname'] . ' ' . $studentInfo['lastname'] : (isset($admissionInfo['firstname']) ? $admissionInfo['firstname'] . ' ' . $admissionInfo['lastname'] : ''); ?>" disabled>
        </div>
        <div class="col-md-2">
            <label for="grade" class="">Grade</label>
            <input type="text" id="grade" class="form-control" value="<?php echo isset($studentInfo['grade']) ? $studentInfo['grade'] : (isset($admissionInfo['grade']) ? $admissionInfo['grade'] : ''); ?>" disabled>
        </div>
    </div>  
    <!-- Second Row: Payment Details -->
    <form method="POST">
        <input type="hidden" name="otc_id" value="<?php echo isset($otc_id) ? $otc_id : ''; ?>">
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="fullName" class="">Full Name of Payor</label>
                <input type="text" name="fullName" id="fullName" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label for="paymentTerms" class="">Payment Terms</label>
                <select name="paymentTerms" id="paymentTerms" class="form-select" required>
                    <option value="Installment">Installment</option>
                    <option value="Full">Full Payment</option>
                    <option value="Admission fee">Admission</option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <label for="receiptNumber" class="">Receipt/OR Number</label>
                <input type="text" name="receiptNumber" id="receiptNumber" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label for="amount" class="">Amount</label>
                <input type="text" name="amount" id="amount" class="form-control" required>
            </div>
            <div class="col-md-2">
                <label for="discount" class="">Discount</label>
                <input type="text" name="discount" id="discount" class="form-control">
            </div>
            <div class="col-md-2">
                <button type="submit" name="submit_payment" class="btn btn-secondary mt-4">Submit</button>
            </div>
        </div>
    </form>
    </di>
  </div>
</div>


</div>

</div>
</div>

</div>


<!----------------Sweet Alert---------------------->
<script>
function toggleSearchInput() {
    var searchType = document.getElementById("searchType").value;
    var searchInputContainer = document.getElementById("searchInputContainer");
    var searchButtonContainer = document.getElementById("searchButtonContainer");

    if (searchType === "student") {
        searchInputContainer.style.display = "block";
        searchInputContainer.querySelector("label").innerText = "Student ID";
        searchInputContainer.querySelector("input").name = "studentNumber"; // Set name for form submission
        searchButtonContainer.style.display = "block";
    } else if (searchType === "admission") {
        searchInputContainer.style.display = "block";
        searchInputContainer.querySelector("label").innerText = "Admission ID";
        searchInputContainer.querySelector("input").name = "admissionNumber"; // Set name for form submission
        searchButtonContainer.style.display = "block";
    } else {
        searchInputContainer.style.display = "none";
        searchButtonContainer.style.display = "none";
    }
}
</script>
<script>
        $(document).ready(function() {
            <?php if (isset($_SESSION['error'])): ?>
                Swal.fire({
                    title: 'Error!',
                    text: "<?php echo $_SESSION['error']; ?>",
                    icon: 'error',
                    confirmButtonText: 'Ok'
                });
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                Swal.fire({
                    title: 'Success!',
                    text: "<?php echo $_SESSION['success']; ?>",
                    icon: 'success',
                    confirmButtonText: 'Ok'
                });
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
        });
    </script>


<script src="assets/js/sweetalert.min.js"></script>


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