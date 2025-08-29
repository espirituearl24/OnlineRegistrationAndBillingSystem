<?php
ob_start();
session_start();
include 'connection.php';
$alert = "";

if (!isset($_SESSION['user'])) {
  // If not set, redirect to login page
  header('location: index.php');
  exit(); // Ensure no further code is executed
} 


// Retrieve session data
$username = $_SESSION['user'];
$userType = $_SESSION['type'];
$userId = $_SESSION['id'];

// Fetch the user's grade level based on their ID
$grade = null; // Initialize grade variable

$sqlGrade = "SELECT `grade` FROM enroll WHERE `id` = ?";
$statementGrade = $conn->prepare($sqlGrade);
$statementGrade->bind_param("i", $userId); // Bind the user ID
$statementGrade->execute();
$resultGrade = $statementGrade->get_result();
$userData = $resultGrade->fetch_assoc();

if ($userData) {
    $grade = $userData['grade']; // Now $grade is defined
}

// Initialize total amount and total paid
$totalAmount = 0;
$totalPaid = 0;

// Fetch Payments
$sql3 = "SELECT * FROM payments WHERE `admission_id` = ?";
$statement3 = $conn->prepare($sql3);
$statement3->bind_param("i", $userId);
$statement3->execute();
$result3 = $statement3->get_result();
$paymentData = $result3->fetch_assoc();

if ($paymentData) {

  // Fetch admission_id
  $admissionId = $paymentData['admission_id']; // Assuming admission_id is in the payments table
  $_SESSION['admission_id'] = $admissionId; // Store admission_id in session
    // Check payment terms
    if ($paymentData['payment_terms'] === 'installment') {
        // Fetch Tuition based on Grade Level
        $sql4 = "SELECT `install_total`, `install_monthly` FROM tuition WHERE `grade_level` = ?";
        $statement4 = $conn->prepare($sql4);
        $statement4->bind_param("s", $grade); // Bind the grade
        $statement4->execute();
        $result4 = $statement4->get_result();
        $tuitionData = $result4->fetch_assoc();

        if ($tuitionData) {
            $totalAmount = (float)$tuitionData['install_total'];
        }
    } else {
        // Handle case for full payment or other payment terms
        $totalAmount = (float)$paymentData['full_total'];
    }
}

// Fetch all payments made by the user
$sql7 = "SELECT `amount` FROM payments WHERE `admission_id` = ?";
$statement7 = $conn->prepare($sql7);
$statement7->bind_param("i", $userId); // Bind the user ID
$statement7->execute();
$result7 = $statement7->get_result();

// Sum up all payments made
while ($row = $result7->fetch_assoc()) {
    $totalPaid += (float)$row['amount'];
}

// Calculate remaining balance
$remainingBalance = $totalAmount - $totalPaid;



// Check if remaining balance is zero for alert
$showZeroBalanceAlert = $remainingBalance === 0;

//Fetch student info
if (isset($_POST['fetch_student'])) {
  $studentId = $_POST['studentId'];

  // Prepare a statement to prevent SQL injection
  $stmt = $conn->prepare("SELECT * FROM enroll WHERE student_id = ?");
  $stmt->bind_param("s", $studentId); 

  // Execute the statement
  $stmt->execute();
  $result = $stmt->get_result(); 

  if ($result->num_rows > 0) {
      $studentData = $result->fetch_assoc();
      // Include the studentId and the id column in the fetched data
      $studentData['studentId'] = $studentId;  
      $studentData['enrollment_id'] = $studentData['id']; // Store the enrollment id
  } else {
      $alert = "No student found with this ID.";
  }

  // Close the statement
  $stmt->close();
}



if (isset($_POST['save_old'])) {
  // Enable error reporting
  error_reporting(E_ALL);
  ini_set('display_errors', 1);

  // Start a database transaction for better error handling
  $conn->begin_transaction();

  try {
    // Capture all form data
    $enrollmentId = !empty($_POST['enrollment_id']) ? intval($_POST['enrollment_id']) : null;
    $studentId = $_POST['studentId'] ?? '';
    $firstName = $_POST['firstName'] ?? '';
    $lastName = $_POST['lastName'] ?? '';
    $middleName = $_POST['middleName'] ?? '';
    $LRN = $_POST['LRN'] ?? '';
    $dob = !empty($_POST['dob']) ? date('Y-m-d', strtotime($_POST['dob'])) : null;
    $PSA = $_POST['PSA'] ?? '';
    $religion = $_POST['religion'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $phoneNumber = $_POST['phoneNumber'] ?? '';
    $emailAddress = $_POST['emailAddress'] ?? '';
    $homeAddress = $_POST['homeAddress'] ?? '';
    $specialED = $_POST['specialED'] ?? '';
    $grade = $_POST['grade'] ?? '';
    $withLRN = $_POST['withLRN'] ?? '';
    $lastGradelevel = $_POST['lastGradelevel'] ?? '';
    $lastSY = $_POST['lastSY'] ?? '';
    $lastSchool = $_POST['lastSchool'] ?? '';
    $schoolAddress = $_POST['schoolAddress'] ?? '';
    $schoolType = $_POST['schoolType'] ?? '';
    $fatherName = $_POST['fatherName'] ?? '';
    $fatherEmail = $_POST['fatherEmail'] ?? '';
    $fatherSchool = $_POST['fatherSchool'] ?? '';
    $fatherJob = $_POST['fatherJob'] ?? '';
    $fatherNumber = $_POST['fatherNumber'] ?? '';
    $motherName = $_POST['motherName'] ?? '';
    $motherEmail = $_POST['motherEmail'] ?? '';
    $motherSchool = $_POST['motherSchool'] ?? '';
    $motherJob = $_POST['motherJob'] ?? '';
    $motherNumber = $_POST['motherNumber'] ?? '';
    $currentdate = date('Y-m-d');

    // Prepare SQL with all columns, including the id
    $sql = "INSERT INTO `old_admission` (
      `id`, `student_id`, `firstname`, `lastname`, `middlename`, `LRN`, `birthday`, 
      `PSA`, `religion`, `gender`, `phonenumber`, `emailaddress`, 
      `homeaddress`, `specialED`, `grade`, `withLRN`, `lastGradelevel`, 
      `lastSY`, `lastSchool`, `schoolAddress`, `schoolType`, 
      `fatherName`, `fatheremail`, `fatherSchool`, `fatherJob`, `fatherNumber`, 
      `motherName`, `motheremail`, `motherSchool`, `motherJob`, `motherNumber`, 
      `currentdate`
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 
      ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    // Prepare statement
    $stmt = $conn->prepare($sql);
    
    $bindTypes = "i" . str_repeat("s", 31); // 1 integer for id, 31 strings for the rest

    // Bind parameters
    $stmt->bind_param(
      $bindTypes, 
      $enrollmentId,
      $studentId, $firstName, $lastName, $middleName, $LRN, $dob, 
      $PSA, $religion, $gender, $phoneNumber, $emailAddress, 
      $homeAddress, $specialED, $grade, $withLRN, $lastGradelevel, 
      $lastSY, $lastSchool, $schoolAddress, $schoolType, 
      $fatherName, $fatherEmail, $fatherSchool, $fatherJob, $fatherNumber, 
      $motherName, $motherEmail, $motherSchool, $motherJob, $motherNumber, 
      $currentdate
    );


    // Execute the insertion
    $result = $stmt->execute();
        if (!$result) {
          throw new Exception("Execution failed: " . $stmt->error);
        }
    if ($result) {
      // If insertion is successful, delete from enroll table
      $deleteQuery = "DELETE FROM enroll WHERE id = ?";
      $deleteStmt = $conn->prepare($deleteQuery);
      $deleteStmt->bind_param("i", $enrollmentId);
      $deleteResult = $deleteStmt->execute();

      if ($deleteResult) {
        // Commit the transaction
        $conn->commit();

        // Log the successful transfer
        error_log("Student {$studentId} transferred to old_admission successfully.");

        // Redirect with success message
        echo "<script>
          alert('Student successfully transferred to old admission!');
          window.location.href = 'index.php';
        </script>";
        exit();
      } else {
        // Rollback the transaction
        $conn->rollback();
        
        // Log the error
        error_log("Failed to delete from enroll table: " . $deleteStmt->error);

        echo "<script>
          alert('Error: Could not remove student from enrollment. " . $deleteStmt->error . "');
        </script>";
      }
    } else {
      // Rollback the transaction
      $conn->rollback();
      
      // Log the insertion error
      error_log("Insertion failed: " . $stmt->error);

      echo "<script>
        alert('Insertion failed: " . $stmt->error . "');
      </script>";
    }

    // Close statements
    $stmt->close();
    if (isset($deleteStmt)) {
      $deleteStmt->close();
    }

  } catch (Exception $e) {
    // Rollback the transaction in case of any exception
    $conn->rollback();
    
    // Log the exception
    error_log("Exception in old admission transfer: " . $e->getMessage());

    echo "<script>
      alert('An error occurred: " . $e->getMessage() . "');
    </script>";
  }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GBA | Admission</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
    rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" 
    crossorigin="anonymous">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" 
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" 
    crossorigin="anonymous"></script>
<!-- Bootstrap End-->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


<style class="">
    .navcolor {
        background-color: yellow; 
    }
</style>

</head>
<body class = "">


<?php 

// Calculate remaining balance
$remainingBalance = $totalAmount - $totalPaid;

// Store remaining balance in session
$_SESSION['remainingBalance'] = $remainingBalance;

// Check if remaining balance is zero for alert
$showZeroBalanceAlert = $remainingBalance === 0;



if (isset($_SESSION['no_balance_alert'])) {
  echo '
  <script>
      Swal.fire({
          title: "' . $_SESSION['no_balance_alert']['title'] . '",
          text: "' . $_SESSION['no_balance_alert']['text'] . '",
          icon: "' . $_SESSION['no_balance_alert']['icon'] . '",
          confirmButtonText: "Okay"
      });
  </script>';

  // Unset the no balance alert session variable after displaying the alert
  unset($_SESSION['no_balance_alert']);
}

?>

<script>
    function checkRemainingBalance() {
        fetch("check_remaining_balance.php")
            .then(response => response.json())
            .then(data => {
                if (data.remainingBalance > 0) {
                    showRemainingBalanceAlert(data.remainingBalance);
                }
            });
    }

    function showRemainingBalanceAlert(remainingBalance) {
    // Format the remaining balance with commas and two decimal places
    var formattedBalance = remainingBalance.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    
    Swal.fire({
        title: "You have Remaining Balance!",
        html: "Total Balance: ₱" + formattedBalance, // Display the formatted balance
        icon: "warning",
        confirmButtonText: "Go to Payment",
        allowOutsideClick: false,
        showCloseButton: false,
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "old_online_payment.php"; // Redirect to the desired page if needed
        }
    });
}

    setInterval(checkRemainingBalance, 5000);
    checkRemainingBalance();
</script>


<!-- Navbar -->
    <nav class="navbar navbar-expand-lg " style="background-color:#0a2757;">
    <div class="container-fluid py-1">
        <main>
            <!-- Logo -->
                <a class="navbar-brand fw-bold ps-3 text-warning" href="index.php">
                <img src="img/logo1.png" alt="Logo" width="40" height="40" class="d-inline-block align-text-center"> <!-- This is top now center -->
                Grace Baptist Academy
                </a>
            <!-- Logo End-->
        </main>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
        </button>


        </div>
    </div>
    </nav>
<!-- Navbar  End-->

<section class="vh-100 gradient-custom">
  <div class="container py-5 h-100">
    <!-- <div class="row justify-content-center align-items-center h-100">
      <div class="col-12 col-lg-9 col-xl-7">
        <div class="card shadow-2-strong card-registratio" style="border-radius: 15px;">
          <div class="card-body p-4 p-md-5"> -->
            <h3 class="">Old Student Admission Form</h3>
            <!-- <div class="alert alert-info" role="alert">
        <strong>Remaining Balance:</strong> ₱<?php echo number_format($remainingBalance, 2); ?>
    </div> -->
            <p class="mb-4 pb-2 pb-md-0 mb-md-5">Please indicate N/A if no applicable answer</p>


<!---------------- separation ------------------>
      <div class="row">
        <div class="col-md-3 mb-5 btn disabled bg-secondary fw-bold text-light">Student Information</div>
        <div class="col"><hr style="border-top: 2px solid black;"></div>
      </div>
<!---------------- form ------------------->
<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
<!-------------- Column 1 -------------->

  <div class="row"> 

    <!---- student id --------->
    <h6 class="mt-2 fst-italic text-danger"><?php echo $alert; ?></h6>

    <div class="row">
      <div class="col mb-4">
        <div class="form-outline">
          <input type="text" name="studentId" id="studentId" class="form-control form-control-sm" 
              value="<?php echo isset($studentId) ? htmlspecialchars($studentId) : ''; ?>" required />
          <label class="form-label" for="studentId">Student ID</label>
        </div>
      </div>
      <div class="col mb-4">
        <input class="mb-4 btn btn-primary btn-md" name="fetch_student" type="submit" value="Confirm" />
      </div>
    </div>
<!-- Add this hidden input after the other form inputs -->
<input type="hidden" name="enrollment_id" value="<?php echo isset($studentData['enrollment_id']) ? $studentData['enrollment_id'] : ''; ?>">
    <!-- firstname -->
    <div class="col mb-4">
      <div class="form-outline">
        <input type="text" name="firstName" class="form-control form-control-sm" value="<?php echo isset($studentData) ? $studentData['firstname'] : ''; ?>" readonly />
        <label class="form-label" for="firstName">First Name</label>
      </div>
    </div>
    <!-- lastname -->
    <div class="col mb-4">
      <div class="form-outline">
        <input type="text" name="lastName" class="form-control form-control-sm" value="<?php echo isset($studentData) ? $studentData['lastname'] : ''; ?>" readonly />
        <label class="form-label" for="lastName">Last Name</label>
      </div>
    </div>
    <!-- middle -->
    <div class="col mb-4">
      <div class="form-outline">
        <input type="text" name="middleName" class="form-control form-control-sm" value="<?php echo isset($studentData) ? $studentData['middlename'] : ''; ?>" readonly />
        <label class="form-label" for="middleName">Middle Name</label>
      </div>
    </div>
    <!-- LRN  -->
    <div class="col mb-4">
      <div class="form-outline">
        <input type="text" name="LRN" class="form-control form-control-sm" data-toggle="tooltip" data-placement="bottom" title="Type N/A if no applicable answer" value="<?php echo isset($studentData) ? $studentData['LRN'] : ''; ?>" readonly />
        <label class="form-label" for="LRN">LRN no.</label>
      </div>
    </div>
    <!-- bday -->
    <div class="col mb-4 d-flex align-items-center">
      <div class="form-outline datepicker w-100">
        <input type="date" class="form-control form-control-sm" name="dob" value="<?php echo isset($studentData) ? $studentData['birthday'] : ''; ?>" readonly />
        <label for="bod" class="form-label">Birthday</label>
      </div>
    </div>
  </div>
<!-------------- Column 2 -------------->

  <div class="row">

    <div class="col mb-4">
      <div class="form-outline">
        <input type="text" name="PSA" class="form-control form-control-sm" data-toggle="tooltip" data-placement="bottom" title="If applicable upon enrollment" value="<?php echo isset($studentData) ? $studentData['PSA'] : ''; ?>" readonly />
        <label class="form-label" for="PSA" >PSA Birth Certificate No.</label>
      </div>
    </div>

    <div class="col mb-4">
      <div class="form-outline">
        <input type="text" name="religion" class="form-control form-control-sm" value="<?php echo isset($studentData) ? $studentData['religion'] : ''; ?>" readonly />
        <label class="form-label" for="religion">Religion</label>
      </div>
    </div>

    <div class="col mb-3">
      <label class="form-label ms-1 me-2" for="sexSelect">Sex</label>
      <select name="gender" class="select form-control-sm" id="sexSelect" readonly>
        <option value="1" readonly <?php echo !isset($studentData) ? 'selected' : ''; ?>>Choose option</option>
        <option value="Female" <?php echo (isset($studentData) && $studentData['gender'] === 'Female') ? 'selected' : ''; ?>>Female</option>
        <option value="Male" <?php echo (isset($studentData) && $studentData['gender'] === 'Male') ? 'selected' : ''; ?>>Male</option>
      </select>
    </div>

    <div class="col mb-4 pb-2">
      <div class="form-outline">
        <input type="tel" name="phoneNumber" class="form-control form-control-sm" value="<?php echo isset($studentData) ? $studentData['phonenumber'] : ''; ?>" readonly />
        <label class="form-label" for="phoneNumber">Phone Number</label>
      </div>
    </div>
    
    <div class="col mb-4 pb-2">
      <div class="form-outline">
        <input type="email" name="emailAddress" class="form-control form-control-sm" value="<?php echo isset($studentData) ? $studentData['emailaddress'] : ''; ?>" readonly />
        <label class="form-label" for="emailAddress">Email Address</label>
      </div>
    </div>

  </div>

  <div class="row mb-4">
    <div class="col-sm-6">
      <div class="form-outline">
        <input type="text" name="homeAddress" class="form-control form-control-sm" value="<?php echo isset($studentData) ? $studentData['homeaddress'] : ''; ?>" />
        <label class="form-label" for="homeAddress">Permanent Home Address</label>
      </div>
    </div>

    <div class="col-sm-6">
      <div class="form-outline">
        <input type="text" name="specialED" class="form-control form-control-sm" data-toggle="tooltip" data-placement="bottom" title="If yes/ Please specify" value="<?php echo (!empty($studentData['specialED']) ? $studentData['specialED'] : 'N/A'); ?>" readonly />
        <label class="form-label" for="specialED">Does the learner have special education needs?</label>
      </div>
    </div>
  </div>

  <!---------------- separation ------------------>
  <div class="row mt-5">
    <div class="col-md-4 bg-primary mb-5 btn disabled bg-secondary fw-bold text-light">Grade Level and School Information</div>
    <div class="col"><hr style="border-top: 2px solid black;"></div>
  </div>

  <div class="row mb-4">
    <div class="col">
      <label class="form-label me-2">Grade level to enroll</label>
      <select name="grade" class="select form-control-sm">
        <option value="1" readonly>Choose option</option>
        <option value="Pre-school">Pre-school</option>
        <option value="Kinder">Kinder</option>
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

    <div class="col">
      <label class="form-label ms-4 me-2">With LRN</label>
      <select name="withLRN" class="select form-control-sm" readonly>
        <option value="1" readonly selected>Choose option</option>
        <option value="Pre-school">Yes</option>
        <option value="Kinder">No</option>
      </select>
    </div>

    <div class="col-sm-5">
    <div class="form-outline">
        <input type="text" name="lastGradelevel" class="form-control form-control-sm" value="<?php echo isset($studentData) ? htmlspecialchars($studentData['grade']) : (isset($grades[0]) ? htmlspecialchars($grades[0]) : ''); ?>"/>
        <label class="form-label" for="lastGradelevel">Last Grade Level Completed</label>
    </div>
</div>

  <div class="row mb-4">
    <div class="col">
      <div class="form-outline">
        <input type="text" name="lastSY" class="form-control form-control-sm" value="<?php echo isset($studentData) ? htmlspecialchars($studentData['lastSY']) : (isset($grades[0]) ? htmlspecialchars($grades[0]) : ''); ?>" />
        <label class="form-label" for="lastSY">Last School Year completed</label>
      </div>
    </div>

    <div class="col">
      <div class="form-outline">
        <input type="text" name="lastSchool" class="form-control form-control-sm" value="<?php echo isset($studentData) ? htmlspecialchars($studentData['lastSchool']) : (isset($grades[0]) ? htmlspecialchars($grades[0]) : ''); ?>" />
        <label class="form-label" for="lastSchool">Last School Attended</label>
      </div>
    </div>

    <div class="col">
      <div class="form-outline">
        <input type="text" name="schoolAddress" class="form-control form-control-sm" value="<?php echo isset($studentData) ? htmlspecialchars($studentData['schoolAddress']) : (isset($grades[0]) ? htmlspecialchars($grades[0]) : ''); ?>" />
        <label class="form-label" for="schoolAddress">School Address</label>
      </div>
    </div>

    <div class="col">
        <label class="form-label ms-4 me-2">School Type</label>
        <select name="schoolType" class="select form-control-sm">
            <option value="" readonly selected>Choose option</option>
            <option value="Private" <?php echo (isset($studentData) && $studentData['schoolType'] == 'Private') ? 'selected' : ''; ?>>Private</option>
            <option value="Public" <?php echo (isset($studentData) && $studentData['schoolType'] == 'Public') ? 'selected' : ''; ?>>Public</option>
        </select>
    </div>
  </div>

  <!---------------- separation ------------------>
  <div class="row mt-5">
    <div class="col-md-4 mb-5 btn disabled bg-secondary fw-bold text-light">Patient/Guardian Information</div>
    <div class="col"><hr style="border-top: 2px solid black;"></div>
  </div>
  <div class="row">

    <!-- Father -->
    <div class="col mb-4">
      <div class="form-outline">
        <input type="text" name="fatherName" class="form-control form-control-sm" value="<?php echo isset($studentData) ? $studentData['fatherName'] : ''; ?>" readonly />
        <label class="form-label" for="fatherName">Father's Full Name</label>
      </div>
    </div>
    <!-- FOccupation -->
    <div class="col mb-4">
      <select name="fatherSchool" class="select form-control-sm ms-4" readonly>
        <option value="1" readonly>Choose option</option>
        <option value="Elementary Graduate">Elementary Graduate</option>
        <option value="High School Graduate">High School Graduate</option>
        <option value="College Graduate">College Graduate</option>
        <option value="Vocational">Vocational</option>
        <option value="Master's/Doctorate Degree">Master's/Doctorate Degree</option>
        <option value="Did not attend school">Did not attend school</option>
      </select>
      <label class="form-label ms-4 me-2" for="fatherSchool">Highest Educational Attainment</label>
    </div>
    <!-- FC  -->
    <div class="col mb-4">
      <select name="fatherJob" class="select form-control-sm ms-4" readonly>
        <option value="1" readonly selected>Choose option</option>
        <option value="Full time employee">Full time employee</option>
        <option value="Part-time/Contractual Employee">Part-time/Contractual Employee</option>
        <option value="Self-employed">Self-employed</option>
        <option value="Currently not employed">Currently not employed</option>
      </select>
      <label class="form-label ms-4 me-2" for="fatherJob">Employment Status</label>
    </div>

    <div class="col mb-4">
      <div class="form-outline">
        <input type="tel" name="fatherNumber" class="form-control form-control-sm" value="<?php echo isset($studentData) ? $studentData['fatherNumber'] : ''; ?>" readonly />
        <label class="form-label" for="fatherNumber">Contact Number</label>
      </div>
    </div>

    <div class="row">

      <!-- Mother -->
      <div class="col mb-4">
        <div class="form-outline">
          <input type="text" name="motherName" class="form-control form-control-sm" value="<?php echo isset($studentData) ? $studentData['motherName'] : ''; ?>" readonly />
          <label class="form-label" for="motherName">Mother's Full Name</label>
        </div>
      </div>
      <!-- MOccupation -->
      <div class="col mb-4">
        <select name="motherSchool" class="select form-control-sm ms-4" readonly>
          <option value="1" readonly selected>Choose option</option>
          <option value="Elementary Graduate">Elementary Graduate</option>
          <option value="High School Graduate">High School Graduate</option>
          <option value=" College Graduate">College Graduate</option>
          <option value="Vocational">Vocational</option>
          <option value="Master's/Doctorate Degree">Master's/Doctorate Degree</option>
          <option value="Did not attend school">Did not attend school</option>
        </select>
        <label class="form-label ms-4 me-2" for="motherSchool">Highest Educational Attainment</label>
      </div>
      <!-- MC  -->
      <div class="col mb-4">
        <select name="motherJob" class="select form-control-sm ms-4" readonly>
          <option value="1" readonly>Choose option</option>
          <option value="Full time employee">Full time employee</option>
          <option value="Part-time/Contractual Employee">Part-time/Contractual Employee</option>
          <option value="Self-employed">Self-employed</option>
          <option value="Currently not employed">Currently not employed</option>
        </select>
        <label class="form-label ms-4 me-2" for="motherJob">Employment Status</label>
      </div>

      <div class="col mb-4">
        <div class="form-outline">
          <input type="tel" name="motherNumber" class="form-control form-control-sm" value="<?php echo isset($studentData) ? $studentData['motherNumber'] : ''; ?>" readonly />
          <label class="form-label" for="motherNumber">Contact Number</label>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col mt-2 pt-1">
        <a class="ms-2 mb-4 px-4 btn btn-secondary btn-md" href="index.php">Cancel</a>
      </div>
      <!-- Trigger modal on submit -->
      <div class="col text-end ms-5 mt-2 pt-1">
        <button type="button" class="mb-4 px-4 btn btn-primary btn-md" id="submitBtn">Proceed</button>
        <input type="hidden" name="save_old" value="true">
      </div>
    </div>
  </form>
<div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">
          <img src="img/logo.jpg" alt="logo" width="40" height="40"> Grace Baptist Academy
        </h5>
        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
  <form>
                      <!-- Grade/Level Row -->
                      <div class="mb-3 d-flex align-items-center">
              <label for="gradeLevel" class="me-2">Grade/Level</label>
              <select id="gradeLevel" name="gradeLevel" class="form-select w-50">
                <option value="K1">Kinder 1</option>
                <option value="K2">Kinder 2</option>
                <option value="1">Grade 1</option>
                <option value="2">Grade 2</option>
                <option value="3">Grade 3</option>
                <option value="4">Grade 4</option>
                <option value="5">Grade 5</option>
                <option value="6">Grade 6</option>
                <option value="7">Grade 7</option>
                <option value="8">Grade 8</option>
                <option value="9">Grade 9</option>
                <option value="10">Grade 10</option>
              </select>
            </div>


          <!-- Responsive Image Row -->
          <div class="mt-3 d-flex justify-content-center">
            <img id="gradeImage" src="img/fee_k1_2.jpg" alt="Grade 1 Image" class="img-fluid" />
          </div>

          <!-- Payment Options Header -->
          <h2 class="mt-4 text-center mb-3 fw-bold">Payment Options</h2>

          <!-- Payment Options Dropdown -->
          <div class="d-flex justify-content-center mb-4">
            <select id="paymentOption" name="paymentOption" class="form-select w-50">
              <option value="online">Online Payment</option>
              <option value="counter">Over-the-Counter</option>
            </select>
          </div>

      </div>
      <div class="modal-footer d-flex justify-content-between">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="confirmSubmit">Proceed to Payment</button>
      </div>
    </form>
  </div>
</div>
</div>

          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script>

// Get the form element
document.getElementById('submitBtn').addEventListener('click', function(event) {
  // Prevent the default form submission
  event.preventDefault();

  // Check for form validation
  if (form.checkValidity()) {
    // Show the modal
    var modal = new bootstrap.Modal(document.getElementById('confirmationModal'));
    
    // Get the selected grade level from the form's grade dropdown
    var selectedGrade = document.querySelector('select[name="grade"]').value;
    
    // Set the grade level in the modal dropdown
    var modalGradeLevelDropdown = document.getElementById('gradeLevel');
    
    // Find and select the matching option in the modal dropdown
    for (var i = 0; i < modalGradeLevelDropdown.options.length; i++) {
      if (modalGradeLevelDropdown.options[i].text === selectedGrade) {
        modalGradeLevelDropdown.selectedIndex = i;
        break;
      }
    }

    // Trigger the change event to update the image
    var event = new Event('change');
    modalGradeLevelDropdown.dispatchEvent(event);

    modal.show();
  } else {
    // Handle invalid form (previous validation code)
    form.reportValidity();
  }
});


// Add an event listener to the confirm submit button in the modal
document.getElementById('confirmSubmit').addEventListener('click', function() {
  // Submit the form
  form.action = 'admission1.php';
  form.method = 'post';
  form.submit();
});


// Get the form element
var form = document.querySelector('form');

// Add an event listener to the submit button
document.getElementById('submitBtn').addEventListener('click', function() {

  // Submit the form using AJAX
  var formData = new FormData(form);
  fetch(form.action, {
    method: 'POST',
    body: formData
  })
  .then(response => response.text())
  .then(data => console.log(data))
  .catch(error => console.error(error));
});
 


  // Get the image element and the dropdown
  var gradeImage = document.getElementById('gradeImage');
  var gradeLevelDropdown = document.getElementById('gradeLevel');

// Define images for each grade level
// Define images for each grade level
var images = {
  'K1': 'img/fee_k1_2.jpg',  // Kinder 1
  'K2': 'img/fee_k1_2.jpg',  // Kinder 2
  '1': 'img/fee_g1_2.jpg',
  '2': 'img/fee_g1_2.jpg',
  '3': 'img/fee_g3.jpg',
  '4': 'img/fee_g4_5.jpg', 
  '5': 'img/fee_g4_5.jpg',
  '6': 'img/fee_g6.jpg',
  '7': 'img/fee_g7_9.jpg',
  '8': 'img/fee_g7_9.jpg',
  '9': 'img/fee_g7_9.jpg',
  '10': 'img/fee_g10.jpg'
};



  // Event listener for the grade level dropdown change
  gradeLevelDropdown.addEventListener('change', function() {
    var selectedGrade = gradeLevelDropdown.value;
    // Change the image based on the selected grade level
    gradeImage.src = images[selectedGrade];
    gradeImage.alt = 'Grade ' + selectedGrade + ' Image';
  });
</script>



<script>
  document.getElementById('confirmSubmit').addEventListener('click', function() {
    // Get the selected grade level
    var selectedGrade = document.getElementById('gradeLevel').value;

    // Get the selected payment option
    var paymentOption = document.getElementById('paymentOption').value;

    // Determine the redirection URL based on the payment option
    if (paymentOption === 'online') {
      // Redirect to online payment page
      window.location.href = 'online_payment.php?grade=' + selectedGrade;
    } else if (paymentOption === 'counter') {
      // Redirect to index.php for Over-the-Counter option
      window.location.href = 'requirements.php';
    }
  });
</script>









<script>
    // Activate tooltip for all elements with data-toggle="tooltip"
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>

<script>
    console.log('Debug: Page loaded');
    // Add this to see if any specific script is causing the issue
</script>
</body>
</html>
