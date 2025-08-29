<?php
include('dbconnection.php');
$alert = "";

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home | GBA</title>

<!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
    rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" 
    crossorigin="anonymous">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" 
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" 
    crossorigin="anonymous"></script>
<!-- Bootstrap End-->

<style class="">
    .navcolor {
        background-color: yellow;
    }
</style>

</head>
<body class = "">
<!-- Navbar -->
    <nav class="navbar navbar-expand-lg" style="background-color:#0a2757;">
    <div class="container-fluid py-1">
        <main>
            <!-- Logo -->
                <a class="navbar-brand fw-bold ps-3 text-warning" href="#">
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
            <h3 class="">Edit Student Information</h3>
            <p class="mb-4 pb-2 pb-md-0 mb-md-5">Make sure every form is answered</p>


<!---------------- separation ------------------>
      <div class="row">
        <div class="col-md-3 mb-5 btn disabled bg-secondary fw-bold text-light">Student Information</div>
        <div class="col"><hr style="border-top: 2px solid black;"></div>
      </div>

      <?php
              
          if(isset($_GET['id'])){

              $enroll_id = $_GET['id'];

              $query = "SELECT * FROM enroll WHERE id = '$enroll_id'";
              $statement = $conn->prepare($query);
              $statement->execute();
    
              $result = $statement->fetch(PDO::FETCH_ASSOC);

          }
              
              
      ?>
<!---------------- form ------------------->
            <form action="process.php" method="POST">
              <input type="hidden" name="id" value="<?=  $result['id'];  ?>"/>
<!-------------- Column 1 -------------->

              <h6 class="mt-2 fst-italic text-danger"><?php echo $alert; ?></h6>
              <div class="row">

<!-- firstname -->
                <div class="col mb-4">
                  <div class="form-outline">
                    <input type="text" name="firstName" value="<?=  $result['firstname'];  ?>" class="form-control form-control-sm" />
                    <label class="form-label" for="firstName">First Name</label>
                  </div>
                </div>
<!-- lastname -->
                <div class="col mb-4">
                  <div class="form-outline">
                    <input type="text" name="lastName" value="<?=  $result['lastname'];  ?>" class="form-control form-control-sm" />
                    <label class="form-label" for="lastName">Last Name</label>
                  </div>
                </div>
<!-- middle -->
                <div class="col mb-4">
                  <div class="form-outline">
                    <input type="text" name="middleName" value="<?=  $result['middlename'];  ?>" class="form-control form-control-sm" />
                    <label class="form-label" for="middleName">Middle Name</label>
                  </div>
                </div>
<!-- LRN  -->
                <div class="col mb-4">
                  <div class="form-outline">
                    <input type="text" name="LRN" value="<?=  $result['LRN'];  ?>" class="form-control form-control-sm" data-toggle="tooltip" data-placement="bottom" title="Type N/A if no applicable answer" />
                    <label class="form-label" for="LRN">LRN no.</label>
                  </div>
                </div>
<!-- bday -->
                <div class="col mb-4 d-flex align-items-center">
                  <div class="form-outline datepicker w-100">
                    <input type="date" class="form-control form-control-sm" name="dob" value="<?=  $result['birthday'];  ?>"/>
                    <label for="dob" class="form-label">Birthday</label>
                  </div>
                </div>
              </div>
<!-------------- Column 2 -------------->


              <div class="row">

                <div class="col mb-4">
                    <div class="form-outline">
                      <input type="text" name="PSA" value="<?=  $result['PSA'];  ?>" class="form-control form-control-sm" data-toggle="tooltip" data-placement="bottom" title="If applicable upon enrollment" />
                      <label class="form-label" for="PSA">PSA Birth Certificate No.</label>
                    </div>
                  </div>

                  <div class="col mb-4">
                    <div class="form-outline">
                      <input type="text" name="religion" value="<?=  $result['religion'];  ?>" class="form-control form-control-sm" />
                      <label class="form-label" for="religion">Religion</label>
                    </div>
                  </div>

                  <div class="col mb-3">
                    <label class="form-label ms-1 me-2" >Sex</label>
                    <select name="gender" value="<?=  $result['gender'];  ?>" class="select form-control-sm">
                      <option value="1" disabled>Choose option</option>
                      <option value="Female">Female</option>
                      <option value="Male">Male</option>
                      
                    </select>
                  </div>

                  <div class="col mb-4 pb-2">
                    <div class="form-outline">
                      <input type="tel" name="phoneNumber" value="<?=  $result['phonenumber'];  ?>" class="form-control form-control-sm" />
                      <label class="form-label" for="phoneNumber">Phone Number</label>
                    </div>
                  </div>
                  
                  <div class="col mb-4 pb-2">
                   <div class="form-outline">
                      <input type="email" name="emailAddress" value="<?=  $result['emailaddress'];  ?>" class="form-control form-control-sm" />
                      <label class="form-label" for="emailAddress">Email Address</label>
                    </div>
                  </div>

                </div>

              <div class="row mb-4">
                  <div class="col-sm-6">
                    <div class="form-outline">
                      <input type="text" name="homeAddress" value="<?=  $result['homeaddress'];  ?>" class="form-control form-control-sm"/>
                      <label class="form-label" for="homeAddress">Permanent Home Address</label>
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
                  <select name="grade" value="<?=  $result['grade'];  ?>" class="select form-control-sm">
                    <option value="1" disabled>Choose option</option>
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
    <select name="withLRN" class="select form-control-sm">
        <option value="1" disabled selected>Choose option</option>
        <option value="Yes" <?= !empty($result['LRN']) ? 'selected' : ''; ?>>Yes</option>
        <option value="No" <?= empty($result['LRN']) ? 'selected' : ''; ?>>No</option>
    </select>
</div>

                  <div class="col-sm-5">
                  <div class="form-outline">
        <input type="text" name="lastGradelevel" value="<?= htmlspecialchars($result['lastGradelevel']); ?>" class="form-control form-control-sm"/>
        <label class="form-label" for="lastGradelevel">Last Grade Level Completed</label>
    </div>
                  </div>

              </div>


              <div class="row mb-4">
              <div class="col">
    <div class="form-outline">
        <input type="text" name="lastSY" value="<?= htmlspecialchars($result['lastSY']); ?>" class="form-control form-control-sm"/>
        <label class="form-label" for="lastSY">Last School Year completed</label>
    </div>
</div>

                <div class="col">
                  <div class="form-outline">
                      <input type="text" name="lastSchool" value="<?= htmlspecialchars($result['lastSchool']); ?>" class="form-control form-control-sm"/>
                      <label class="form-label" for="lastSchool">Last School Attended</label>
                  </div>
                </div>

                <div class="col">
                  <div class="form-outline">
                      <input type="text" name="schoolAddress" value="<?= htmlspecialchars($result['schoolAddress']); ?>" class="form-control form-control-sm"/>
                      <label class="form-label" for="schoolAddress">School Address</label>
                  </div>
                </div>


                <div class="col">
    <label class="form-label ms-4 me-2">School Type</label>
    <select name="schoolType" class="select form-control-sm">
        <option value="1" disabled selected>Choose option</option>
        <option value="Private" <?= isset($result['schoolType']) && $result['schoolType'] === 'Private' ? 'selected' : ''; ?>>Private</option>
        <option value="Public" <?= isset($result['schoolType']) && $result['schoolType'] === 'Public' ? 'selected' : ''; ?>>Public</option>
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
                    <input type="text" name="fatherName" value="<?=  $result['fatherName'];  ?>" class="form-control form-control-sm" />
                    <label class="form-label" for="fatherName">Father's Full Name</label>
                  </div>
                </div>
<!-- FOccupation -->
<div class="col mb-4">
                  <select name="fatherSchool" class="select form-control-sm ms-4">
                    <option value="1" disabled>Choose option</option>
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
                  <div class="form-outline">
                    <input type="tel" name="fatherNumber" value="<?=  $result['fatherNumber'];  ?>" class="form-control form-control-sm" />
                    <label class="form-label" for="fatherNumber">Contact Number</label>
                  </div>
                </div>


                <div class="row">

<!-- Mother -->
                <div class="col mb-4">
                  <div class="form-outline">
                    <input type="text" name="motherName" value="<?=  $result['motherName'];  ?>" class="form-control form-control-sm" />
                    <label class="form-label" for="motherName">Mother's Full Name</label>
                  </div>
                </div>
<!-- MOccupation -->
<div class="col mb-4">
                  <select name="motherSchool" class="select form-control-sm ms-4">
                    <option value="1" disabled selected>Choose option</option>
                    <option value="Pre-school">Elementary Graduate</option>
                    <option value="Kinder">High School Graduate</option>
                    <option value="Kinder">College Graduate</option>
                    <option value="Kinder">Vocational</option>
                    <option value="Kinder">Master's/Doctorate Degree</option>
                    <option value="Kinder">Did not attend school</option>
                  </select>
                  <label class="form-label ms-4 me-2" for="motherSchool">Highest Educational Attainment</label>
                </div>
        
<!-- MC  -->
                <div class="col mb-4">
                  <div class="form-outline">
                    <input type="tel" name="motherNumber" value="<?=  $result['motherNumber'];  ?>" class="form-control form-control-sm" />
                    <label class="form-label" for="motherNumber">Contact Number</label>
                  </div>
                </div>
                </div>

          <div class="row">
            
                  <div class="col mt-2 pt-1">
                    <a class="ms-2 mb-4 px-4 btn btn-secondary btn-md" href="tstudent.php">Cancel</a>
                  </div>
                  <div class="col text-end  ms-5 mt-2 pt-1">
                    <input class="mb-4 px-4 btn btn-primary btn-md" name="update_enroll" type="submit" value="Update" />
                  </div>
            </form>
<!---------------- form ------------------->
          </div>
        </div>
      </div>
    </div>
  </div>
</section>


</body>
</html>
