<?php

session_start();
include 'connection.php';
$alert = "";

if (isset($_POST['save_add'])) {
    // Collect all input fields
    $inputFields = [
        'firstName' => $_POST['firstName'],
        'lastName' => $_POST['lastName'],
        'middleName' => $_POST['middleName'],
        'LRN' => $_POST['LRN'],
        'dob' => date('Y-m-d', strtotime($_POST['dob'])),
        'PSA' => $_POST['PSA'],
        'religion' => $_POST['religion'],
        'gender' => $_POST['gender'], 
        'phoneNumber' => $_POST['phoneNumber'],
        'emailAddress' => $_POST['emailAddress'],
        'homeAddress' => $_POST['homeAddress'],
        'specialED' => $_POST['specialED'],
        'grade' => $_POST['grade'],
        'withLRN' => $_POST['withLRN'],
        'lastGradelevel' => $_POST['lastGradelevel'],
        'lastSY' => $_POST['lastSY'],
        'lastSchool' => $_POST['lastSchool'],
        'schoolAddress' => $_POST['schoolAddress'],
        'schoolType' => $_POST['schoolType'],
        'fatherName' => $_POST['fatherName'],
        'fatheremail' => $_POST['fatherEmail'],
        'fatherSchool' => $_POST['fatherSchool'],
        'fatherJob' => $_POST['fatherJob'],
        'fatherNumber' => $_POST['fatherNumber'],
        'motherName' => $_POST['motherName'],
        'motheremail' => $_POST['motherEmail'],
        'motherSchool' => $_POST['motherSchool'],
        'motherJob' => $_POST['motherJob'],
        'motherNumber' => $_POST['motherNumber'],
    ];

 // Validate required fields
 $requiredFields = [
  'firstName', 'lastName', 'middleName', 'LRN', 'dob', 'PSA', 'religion',
  'gender', 'phoneNumber', 'emailAddress', 'homeAddress', 'specialED',
  'grade', 'withLRN', 'lastGradelevel', 'lastSY', 'lastSchool',
  'schoolAddress', 'schoolType', 'fatherName', 'fatherSchool', 'fatherJob',
  'fatherNumber', 'motherName', 'motherSchool', 'motherJob', 'motherNumber'
];

// Validate text input fields
foreach ($requiredFields as $field) {
  if (empty($inputFields[$field])) {
      $alert = "Please Input your " . str_replace('_', ' ', $field);
      break;
  }
}

// Validate file uploads (excluding medCert)
$fileFieldsRequired = ['reportCard', 'goodMoral', 'birthCert', 'id_pic'];
$uploadDir = 'documents/'; // Ensure this directory exists and is writable
$uploadedFiles = [];

// Check if all required files are uploaded
if (empty($alert)) {
  foreach ($fileFieldsRequired as $field) {
      if (empty($_FILES[$field]['name'])) {
          $alert = "Please upload all required documents: " . $field;
          break;
      }
  }
}

// If there are no validation errors, proceed with file upload and database insertion
if (empty($alert)) {
  $currentdate = date('Y-m-d');

  // Handle file uploads with unique filenames
  // Include logic for medCert as optional
  $allFileFields = ['reportCard', 'goodMoral', 'birthCert', 'id_pic', 'medCert'];

  foreach ($allFileFields as $key) {
      // Only process if file is uploaded
      if (!empty($_FILES[$key]['name'])) {
          // Generate a unique filename
          $fileExtension = pathinfo($_FILES[$key]['name'], PATHINFO_EXTENSION);
          $uniqueFilename = uniqid() . '_' . $key . '.' . $fileExtension;
          $filePath = $uploadDir . $uniqueFilename;

          // Move uploaded file
          if (move_uploaded_file($_FILES[$key]['tmp_name'], $filePath)) {
              $uploadedFiles[$key] = $filePath;
          } else {
              $alert = "File upload failed for " . $key;
              break;
          }
      } else {
          // For medCert, set to null if not uploaded
          if ($key === 'medCert') {
              $uploadedFiles[$key] = NULL;
          }
      }
  }

  // If all files uploaded successfully, proceed with database insertion
  if (empty($alert)) {
    // Use prepared statement for safer insertion
$stmt = $conn->prepare("INSERT INTO `admission` (
  `firstname`, `lastname`, `middlename`, `LRN`, `birthday`, `PSA`, 
  `religion`, `gender`, `phonenumber`, `emailaddress`, `homeaddress`, 
  `specialED`, `grade`, `withLRN`, `lastGradelevel`, `lastSY`, 
  `lastSchool`, `schoolAddress`, `schoolType`, `fatherName`, 
  `fatheremail`, `fatherSchool`, `fatherJob`, `fatherNumber`, 
  `motherName`, `motheremail`, `motherSchool`, `motherJob`, 
  `motherNumber`, `currentdate`, `reportCard`, `goodMoral`, 
  `birthCert`, `id_pic`, `medCert`
) VALUES (
  ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 
  ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
)");

// Prepare the bind_param string with the correct number of parameters 
$bindTypes = str_repeat('s', 35); // 35 string parameters

// Bind parameters
$stmt->bind_param(
  $bindTypes,
  $inputFields['firstName'], 
  $inputFields['lastName'], 
  $inputFields['middleName'], 
  $inputFields['LRN'], 
  $inputFields['dob'], 
  $inputFields['PSA'], 
  $inputFields['religion'], 
  $inputFields['gender'], 
  $inputFields['phoneNumber'], 
  $inputFields['emailAddress'], 
  $inputFields['homeAddress'], 
  $inputFields['specialED'], 
  $inputFields['grade'], 
  $inputFields['withLRN'], 
  $inputFields['lastGradelevel'], 
  $inputFields['lastSY'], 
  $inputFields['lastSchool'], 
  $inputFields['schoolAddress'], 
  $inputFields['schoolType'], 
  $inputFields['fatherName'], 
  $inputFields['fatheremail'], 
  $inputFields['fatherSchool'], 
  $inputFields['fatherJob'], 
  $inputFields['fatherNumber'], 
  $inputFields['motherName'], 
  $inputFields['motheremail'], 
  $inputFields['motherSchool'], 
  $inputFields['motherJob'], 
  $inputFields['motherNumber'], 
  $currentdate, 
  $uploadedFiles['reportCard'], 
  $uploadedFiles['goodMoral'], 
  $uploadedFiles['birthCert'], 
  $uploadedFiles['id_pic'], 
  $uploadedFiles['medCert']
);

      // Execute the statement
      if ($stmt->execute()) {
          header('Location: index.php');
          exit();
      } else {
          $alert = "Admission Failed: " . $stmt->error;
      }

      // Close statement
      $stmt->close();
  }
}
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GBA | Admission</title>

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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style class="">
    .navcolor {
        background-color: yellow;
    }
</style>

</head>
<body class = "">
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
            <h3 class="">New Student Admission Form</h3>
            <p class="mb-4 pb-2 pb-md-0 mb-md-5">Please indicate N/A if no applicable answer</p>


<!---------------- separation ------------------>
      <div class="row">
        <div class="col-md-3 mb-5 btn disabled bg-secondary fw-bold text-light">Student Information</div>
        <div class="col"><hr style="border-top: 2px solid black;"></div>
      </div>
<!---------------- form ------------------->
            <form action="admission.php" method="POST" novalidate>

<!-------------- Column 1 -------------->

<h6 class="mt-2 fst-italic text-danger"><?php echo $alert; ?></h6>
              <div class="row">

<!-- firstname -->
                <div class="col mb-4">
                  <div class="form-outline">
                    <input type="text" name="firstName" class="form-control form-control-sm" required />
                    <label class="form-label" for="firstName">First Name</label>
                  </div>
                </div>
<!-- lastname -->
                <div class="col mb-4">
                  <div class="form-outline">
                    <input type="text" name="lastName" class="form-control form-control-sm" required />
                    <label class="form-label" for="lastName">Last Name</label>
                  </div>
                </div>
<!-- middle -->
                <div class="col mb-4">
                  <div class="form-outline">
                    <input type="text" name="middleName" class="form-control form-control-sm" required />
                    <label class="form-label" for="middleName">Middle Name</label>
                  </div>
                </div>
<!-- LRN  -->
                <div class="col mb-4">
                  <div class="form-outline">
                    <input type="text" name="LRN" class="form-control form-control-sm" data-toggle="tooltip" data-placement="bottom" title="Type N/A if no applicable answer" required />
                    <label class="form-label" for="LRN">LRN no.</label>
                  </div>
                </div>
<!-- bday -->
            <div class="col mb-4 d-flex align-items-center">
              <div class="form-outline datepicker w-100">
                <input type="date" class="form-control form-control-sm" name="dob" id="dobInput" required/>
                <label for="dob" class="form-label">Birthday</label>
              </div>
            </div>
              </div>
<!-------------- Column 2 -------------->


              <div class="row">

                <div class="col mb-4">
                    <div class="form-outline">
                      <input type="text" name="PSA" class="form-control form-control-sm" data-toggle="tooltip" data-placement="bottom" title="If applicable upon enrollment" required />
                      <label class="form-label" for="PSA">PSA Birth Certificate No.</label>
                    </div>
                  </div>

                  <div class="col mb-4">
                    <div class="form-outline">
                      <input type="text" name="religion" class="form-control form-control-sm" required />
                      <label class="form-label" for="religion">Religion</label>
                    </div>
                  </div>

                  <div class="col mb-3">
                    <label class="form-label ms-1 me-2" >Sex</label>
                    <select name="gender" class="select form-control-sm" required>
                      <option value="1" disabled>Choose option</option>
                      <option value="Female">Female</option>
                      <option value="Male">Male</option>
                      
                    </select>
                  </div>

                  <div class="col mb-4 pb-2">
                    <div class="form-outline">
                      <input type="tel" name="phoneNumber" class="form-control form-control-sm" required />
                      <label class="form-label" for="phoneNumber">Phone Number</label>
                    </div>
                  </div>
                  
                  <div class="col mb-4 pb-2">
                   <div class="form-outline">
                      <input type="email" name="emailAddress" class="form-control form-control-sm" required />
                      <label class="form-label" for="emailAddress">Email Address</label>
                    </div>
                  </div>

                </div>

              <div class="row mb-4">
                  <div class="col-sm-6">
                    <div class="form-outline">
                      <input type="text" name="homeAddress" class="form-control form-control-sm" required/>
                      <label class="form-label" for="homeAddress">Permanent Home Address</label>
                    </div>
                  </div>

                  <div class="col-sm-6">
                    <div class="form-outline">
                      <input type="text" name="specialED" class="form-control form-control-sm" data-toggle="tooltip" data-pslacement="bottom" title="If yes/ Please specify" required />
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
                    <option value="1" disabled>Choose option</option>
                    <option value="Kinder">Pre-Kinder</option>
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
                <label class="form-label ms-4 me-2" for="emailAddress">With LRN</label>
                  <select name="withLRN" class="select form-control-sm">
                    <option value="1" disabled>Choose option</option>
                    <option value="Yes">Yes</option>
                    <option value="No">No</option>
                  </select>
                </div>

                  <div class="col-sm-5">
                    <div class="form-outline">
                      <input type="text" name="lastGradelevel" class="form-control form-control-sm" required/>
                      <label class="form-label" for="lastGradelevel">Last Grade Level Completed</label>
                    </div>
                  </div>

              </div>


              <div class="row mb-4">
                <div class="col">
                  <div class="form-outline">
                      <input type="text" name="lastSY" class="form-control form-control-sm" required/>
                      <label class="form-label" for="lastSY">Last School Year completed</label>
                  </div>
                </div>

                <div class="col">
                  <div class="form-outline">
                      <input type="text" name="lastSchool" class="form-control form-control-sm" required/>
                      <label class="form-label" for="lastSchool">Last School Attended</label>
                  </div>
                </div>

                <div class="col">
                  <div class="form-outline">
                      <input type="text" name="schoolAddress" class="form-control form-control-sm" required/>
                      <label class="form-label" for="schoolAddress">School Address</label>
                  </div>
                </div>


                <div class="col">
                <label class="form-label ms-4 me-2">School Type</label>
                  <select name="schoolType" class="select form-control-sm">
                    <option value="1" disabled>Choose option</option>
                    <option value="Private">Private</option>
                    <option value="Public">Public</option>
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
                    <input type="text" name="fatherName" class="form-control form-control-sm" required />
                    <label class="form-label" for="fatherName">Father's Full Name</label>
                  </div>
                </div>
<!-- Father -->
                <div class="col mb-4">
                  <div class="form-outline">
                    <input type="text" name="fatherEmail" class="form-control form-control-sm" />
                    <label class="form-label" for="fatherEmail">Email Address</label>
                  </div>
                </div>
<!-- FOccupation -->
                <div class="col mb-4">
                  <select name="fatherSchool" class="select form-control-sm ms-4" required>
                    <option value="1" disabled>Choose option</option>
                    <option value="Elementary Graduate">Elementary Graduate</option>
                    <option value="High School Graduate">High School Graduate</option>
                    <option value="College Graduate">College Graduate</option>
                    <option value="Vocational">Vocational</option>
                    <option value="Masteral or Doctorate Degree">Master's/Doctorate Degree</option>
                    <option value="Did not attend school">Did not attend school</option>
                  </select>
                  <label class="form-label ms-4 me-2" for="fatherSchool">Highest Educational Attainment</label>
                </div>
<!-- FC  -->
                <div class="col mb-4">
                  <select name="fatherJob" class="select form-control-sm ms-4" required>
                    <option value="1" disabled>Choose option</option>
                    <option value="Full time employee">Full time employee</option>
                    <option value="Part-time/Contractual Employee">Part-time/Contractual Employee</option>
                    <option value="Self-employed">Self-employed</option>
                    <option value="Currently not employed">Currently not employed</option>
                  </select>
                  <label class="form-label ms-4 me-2" for="fatherJob">Employment Status</label>
                </div>

                <div class="col mb-4">
                  <div class="form-outline">
                    <input type="tel" name="fatherNumber" class="form-control form-control-sm" required />
                    <label class="form-label" for="fatherNumber">Contact Number</label>
                  </div>
                </div>


                <div class="row">

<!-- Mother -->
                <div class="col mb-4">
                  <div class="form-outline">
                    <input type="text" name="motherName" class="form-control form-control-sm" required />
                    <label class="form-label" for="motherName">Mother's Full Name</label>
                  </div>
                </div>
<!-- MEmail-->
                <div class="col mb-4">
                  <div class="form-outline">
                    <input type="text" name="motherEmail" class="form-control form-control-sm" />
                    <label class="form-label" for="motherEmail">Email Address</label>
                  </div>
                </div>
<!-- MOccupation -->
                <div class="col mb-4">
                  <select name="motherSchool" class="select form-control-sm ms-4" required>
                    <option value="1" disabled>Choose option</option>
                    <option value="Elementary Graduate">Elementary Graduate</option>
                    <option value="High School Graduate">High School Graduate</option>
                    <option value="College Graduate">College Graduate</option>
                    <option value="Vocational">Vocational</option>
                    <option value="Masteral or Doctorate Degree">Master's/Doctorate Degree</option>
                    <option value="Did not attend school">Did not attend school</option>
                  </select>
                  <label class="form-label ms-4 me-2" for="motherSchool">Highest Educational Attainment</label>
                </div>
<!-- MC  -->
                <div class="col mb-4">
                  <select name="motherJob" class="select form-control-sm ms-4" required>
                    <option value="1" disabled>Choose option</option>
                    <option value="Full time employee">Full time employee</option>
                    <option value="Part-time/Contractual Employee">Part-time/Contractual Employee</option>
                    <option value="Self-employed">Self-employed</option>
                    <option value="Currently not employed">Currently not employed</option>
                  </select>
                  <label class="form-label ms-4 me-2" for="motherJob">Employment Status</label>
                </div>

                <div class="col mb-4">
                  <div class="form-outline">
                    <input type="tel" name="motherNumber" class="form-control form-control-sm" required />
                    <label class="form-label" for="motherNumber">Contact Number</label>
                  </div>
                </div>
                </div>
                <!---------------- separation ------------------>
<div class="row mt-5">
    <div class="col-md-4 mb-5 btn disabled bg-secondary fw-bold text-light">Student Documents (Optional) </div>
    <div class="col"><hr style="border-top: 2px solid black;"></div>
</div>

  <div class="row">
    <div class="col mb-4">
        <div class="form-outline">
            <input type="file" name="reportCard" accept=".pdf, .jpg, .png, .docx" class="form-control form-control-sm" required />
            <label class="form-label" for="reportCard">Form 138 (Report card)</label>
        </div>
    </div>
    <div class="col mb-4">
        <div class="form-outline">
            <input type="file" name="goodMoral" accept=".pdf, .jpg, .png, .docx" class="form-control form-control-sm" required />
            <label class="form-label" for="goodMoral">Good Moral Certificate</label>
        </div>
    </div>
    <div class="col mb-4">
        <div class="form-outline">
            <input type="file" name="birthCert" accept=".pdf, .jpg, .png, .docx" class="form-control form-control-sm" required />
            <label class="form-label" for="birthCert">Birth Certificate (PSA)</label>
        </div>
    </div>
    <div class="col mb-4">
        <div class="form-outline">
            <input type="file" name="id_pic" accept=".jpg, .png" class="form-control form-control-sm" required />
            <label class="form-label" for="id_pic">ID picture (White Background)</label>
        </div>
    </div>
    <div class="col mb-4">
        <div class="form-outline">
            <input type="file" name="medCert" accept=".pdf, .jpg, .png, .docx" class="form-control form-control-sm" />
            <label class="form-label" for="medCert">Medical Certificate</label>
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
  <input type="hidden" name="save_add" value="true">
</div>

            </form>
<!---------------- form ------------------->

<!----------------Payment Option Modal----------------------->

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
            <input type="text" id="gradeLevel" name="gradeLevel" class="form-control w-50" readonly>
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


<!----------------Payment Option Modal----------------------->

          </div>
        </div>
      </div>
    </div>
  </div>
</section>


<script>


document.getElementById('dobInput').addEventListener('change', function() {
    // Get the selected date
    var selectedDate = new Date(this.value);
    
    // Get current date
    var currentDate = new Date();
    
    // Calculate age
    var age = currentDate.getFullYear() - selectedDate.getFullYear();
    var monthDiff = currentDate.getMonth() - selectedDate.getMonth();
    var dayDiff = currentDate.getDate() - selectedDate.getDate();
     
    // Adjust age if birthday hasn't occurred this year
    if (monthDiff < 0 || (monthDiff === 0 && dayDiff < 0)) {
        age--;
    }
    
    // Check if age is at least 5
    if (age < 5) {
        Swal.fire({
            icon: 'error',
            title: 'Age Requirement Not Met',
            text: 'Student must be at least 5 years old at the time of enrollment.',
            footer: 'Please check the birthdate'
        });
        
        // Clear the input
        this.value = '';
    }
});
// Get the form element
var form = document.querySelector('form');

// Function to validate form fields
function validateForm() {
    // Reset previous validation
    form.classList.remove('was-validated');

    // Check HTML5 form validation
    if (!form.checkValidity()) {
        // Prevent form submission
        event.preventDefault();
        event.stopPropagation();

        // Add validation class to show error styles
        form.classList.add('was-validated');

        // Collect invalid fields
        var invalidFields = form.querySelectorAll(':invalid');
        var errorMessage = 'Please fill in the following required fields:\n';

        invalidFields.forEach(function(field) {
            // Try to find the corresponding label
            var label = form.querySelector(`label[for="${field.id}"]`) || 
                        form.querySelector(`label[for="${field.name}"]`);
            
            if (label) {
                errorMessage += `- ${label.textContent}\n`;
            } else {
                errorMessage += `- ${field.name}\n`;
            }
        });

        // Show error using SweetAlert
        Swal.fire({
            icon: 'error',
            title: 'Incomplete Form',
            text: errorMessage,
            footer: 'Please check all required fields'
        });

        return false;
    }

    return true;
}

// Add event listener to submit button
document.getElementById('submitBtn').addEventListener('click', function(event) {
    // Prevent default form submission
    event.preventDefault();

    // Validate form
    if (validateForm()) {
        // If validation passes, show the confirmation modal
        var modal = new bootstrap.Modal(document.getElementById('confirmationModal'));
        
        // Get the selected grade level
        var selectedGrade = document.querySelector('select[name="grade"]').value;
        
        // Set the grade level in the modal input
        var gradeLevelInput = document.getElementById('gradeLevel');
        gradeLevelInput.value = selectedGrade; // Set the selected grade
        
        modal.show();
    }
});

// Add event listener to confirm submit button in modal
document.getElementById('confirmSubmit').addEventListener('click', function() {
    // Validate form again before final submission
    if (validateForm()) {
        // If validation passes, submit the form
        form.submit();
    }
});

// Optional: Add real-time validation as user types
form.addEventListener('input', function(event) {
    // Check validity of the specific input
    if (event.target.validity.valid) {
        event.target.classList.remove('is-invalid');
        event.target.classList.add('is-valid');
    } else {
        event.target.classList.remove('is-valid');
        event.target.classList.add('is-invalid');
    }
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


// Get the image element and the input for the grade level in the modal
var gradeImage = document.getElementById('gradeImage');
var gradeSelect = document.querySelector('select[name="grade"]');
var gradeLevelInput = document.getElementById('gradeLevel');

// Define images for each grade level
var images = {
  'Pre-Kinder': 'img/fee_k1_2.jpg',
  'Kinder': 'img/fee_k1_2.jpg',
  'Grade 1': 'img/fee_g1_2.jpg',
  'Grade 2': 'img/fee_g1_2.jpg',
  'Grade 3': 'img/fee_g3.jpg',
  'Grade 4': 'img/fee_g4_5.jpg',
  'Grade 5': 'img/fee_g4_5.jpg',
  'Grade 6': 'img/fee_g6.jpg',
  'Grade 7': 'img/fee_g7_9.jpg',
  'Grade 8': 'img/fee_g7_9.jpg',
  'Grade 9': 'img/fee_g7_9.jpg',
  'Grade 10': 'img/fee_g10.jpg'
};

// Add event listener to the submit button to set the grade level in the modal
document.getElementById('submitBtn').addEventListener('click', function(event) {
    // Prevent default form submission
    event.preventDefault();
 
    // Validate form
    if (validateForm()) {
        // If validation passes, show the confirmation modal
        var modal = new bootstrap.Modal(document.getElementById('confirmationModal'));

        // Get the selected grade level
        var selectedGrade = gradeSelect.value;

        // Set the grade level in the modal input
        gradeLevelInput.value = selectedGrade;

        // Change the image based on the selected grade level
        if (images[selectedGrade]) {
            gradeImage.src = images[selectedGrade];
            gradeImage.alt = selectedGrade + ' Image';
        } else {
            // Handle case where no image is found
            gradeImage.src = ''; // Optionally set a default image
            gradeImage.alt = 'No Image Available';
        }

        modal.show(); // Show the modal
    }
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


// Set max date to December 31, 2023
document.addEventListener('DOMContentLoaded', function() {
    var dobInput = document.getElementById('dobInput');
    dobInput.max = '2023-12-31';

    // Optional: Set minimum date if needed (e.g., for age restrictions)
    // For example, to ensure the person is at least 3 years old for school admission
    var minDate = new Date();
    minDate.setFullYear(minDate.getFullYear() - 70); // Maximum age
    var minDateStr = minDate.toISOString().split('T')[0];
    dobInput.min = minDateStr;


    
});

// dobInput.addEventListener('change', function() {
//     var selectedDate = new Date(this.value);
//     var currentYear = selectedDate.getFullYear();

//     if (currentYear >= 2024) {
//         Swal.fire({
//             icon: 'error',
//             title: 'Birthdate cannot be year 2024',
//             text: 'Please select a date before 2024'
//         });
//         this.value = ''; // Clear the input
//     }
// });

</script>
</body>
</html>
