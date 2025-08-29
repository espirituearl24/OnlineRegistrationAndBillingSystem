<?php

session_start();
include 'connection.php';
$alert = "";

if(isset($_POST['save_admission'])){
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $dob = date('Y-m-d', strtotime($_POST['dob']));
    $gender = $_POST['gender'];
    $emailAddress = $_POST['emailAddress'];
    $phoneNumber = $_POST['phoneNumber'];
    $grade = $_POST['grade'];

    if(!empty($firstName)){
        if(!empty($lastName)){
            if(!empty($dob)){
                if(!empty($emailAddress)){
                    if(!empty($phoneNumber)){
                        $insertRecord = mysqli_query($conn, "INSERT INTO `admission`(`id`, `fname`, `lname`, `birthday`, `gender`, `email`, `phonenumber`, `grade`) VALUES ('','$firstName','$lastName','$dob','$gender','$emailAddress','$phoneNumber','$grade')");

                        if($insertRecord){
                            header('Location: index.php');
                            exit();
                        }
                        else{
                            $alert="Admission Failed";
                        }
                    }
                    else{
                        $alert = "Please Input your Phone Number";
                    }
                }
                else{
                    $alert = "Please Input your Email";
                }
            }
            else{
                $alert = "Please Select your Birthday";
            }
        }
        else{
            $alert = "Please Input your Last Name";
        }
    }
    else{
        $alert = "Please Input your First Name";
    }
}





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
            <h3 class="">ADMISSIONS PROCEDURE</h3>
            <p class="mb-4 pb-2 pb-md-0 mb-md-5">Please indicate N/A if no applicable answer</p>


<!---------------- separation ------------------>
      <div class="row">
        <div class="col-md-3 mb-5 btn disabled bg-secondary fw-bold text-light">Student Information</div>
        <div class="col"><hr style="border-top: 2px solid black;"></div>
      </div>
<!---------------- form ------------------->
            <form action="admission.php" method="POST">
<!-------------- Column 1 -------------->

<h6 class="mt-2 fst-italic text-danger"><?php echo $alert; ?></h6>
              <div class="row">

<!-- firstname -->
                <div class="col mb-4">
                  <div class="form-outline">
                    <input type="text" name="firstName" class="form-control form-control-sm" />
                    <label class="form-label" for="firstName">First Name</label>
                  </div>
                </div>
<!-- lastname -->
                <div class="col mb-4">
                  <div class="form-outline">
                    <input type="text" name="lastName" class="form-control form-control-sm" />
                    <label class="form-label" for="lastName">Last Name</label>
                  </div>
                </div>
<!-- middle -->
                <div class="col mb-4">
                  <div class="form-outline">
                    <input type="text" name="lastName" class="form-control form-control-sm" />
                    <label class="form-label" for="lastName">Middle Name</label>
                  </div>
                </div>
<!-- LRN  -->
                <div class="col mb-4">
                  <div class="form-outline">
                    <input type="text" name="lastName" class="form-control form-control-sm" data-toggle="tooltip" data-placement="bottom" title="Type N/A if no applicable answer" />
                    <label class="form-label" for="lastName">LRN no.</label>
                  </div>
                </div>
<!-- bday -->
                <div class="col mb-4 d-flex align-items-center">
                  <div class="form-outline datepicker w-100">
                    <input type="date" class="form-control form-control-sm" name="dob" />
                    <label for="bod" class="form-label">Birthday</label>
                  </div>
                </div>
              </div>
<!-------------- Column 2 -------------->


              <div class="row">

                <div class="col mb-4">
                    <div class="form-outline">
                      <input type="text" name="lastName" class="form-control form-control-sm" data-toggle="tooltip" data-placement="bottom" title="If applicable upon enrollment" />
                      <label class="form-label" for="lastName" >PSA Birth Certificate No.</label>
                    </div>
                  </div>

                  <div class="col mb-4">
                    <div class="form-outline">
                      <input type="text" name="lastName" class="form-control form-control-sm" />
                      <label class="form-label" for="lastName">Religion</label>
                    </div>
                  </div>

                  <div class="col mb-3">
                    <label class="form-label ms-1 me-2" for="sexSelect">Sex</label>
                    <select name="grade" class="select form-control-sm" id="sexSelect">
                      <option value="1" disabled selected>Choose option</option>
                      <option value="Female">Female</option>
                      <option value="Male">Male</option>
                      
                    </select>
                  </div>

                  <div class="col mb-4 pb-2">
                    <div class="form-outline">
                      <input type="tel" name="phoneNumber" class="form-control form-control-sm" />
                      <label class="form-label" for="phoneNumber">Phone Number</label>
                    </div>
                  </div>
                  
                  <div class="col mb-4 pb-2">
                   <div class="form-outline">
                      <input type="email" name="emailAddress" class="form-control form-control-sm" />
                      <label class="form-label" for="emailAddress">Email Address</label>
                    </div>
                  </div>

                </div>

              <div class="row mb-4">
                  <div class="col-sm-6">
                    <div class="form-outline">
                      <input type="email" name="emailAddress" class="form-control form-control-sm"/>
                      <label class="form-label" for="emailAddress">Permanent Home Address</label>
                    </div>
                  </div>

                  <div class="col-sm-6">
                    <div class="form-outline">
                      <input type="email" name="emailAddress" class="form-control form-control-sm" data-toggle="tooltip" data-pslacement="bottom" title="If yes, please specify"/>
                      <label class="form-label" for="emailAddress">Does the learner have special education needs?</label>
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
                <label class="form-label me-2" for="emailAddress">Grade level to enroll</label>
                  <select name="grade" class="select form-control-sm">
                    <option value="1" disabled selected>Choose option</option>
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
                <label class="form-label ms-4 me-2" for="emailAddress">With LRN</label>
                  <select name="grade" class="select form-control-sm">
                    <option value="1" disabled selected>Choose option</option>
                    <option value="Pre-school">Yes</option>
                    <option value="Kinder">No</option>
                  </select>
                </div>

                  <div class="col-sm-5">
                    <div class="form-outline">
                      <input type="email" name="emailAddress" class="form-control form-control-sm"/>
                      <label class="form-label" for="emailAddress">Last Grade Level Completed</label>
                    </div>
                  </div>

              </div>


              <div class="row mb-4">
                <div class="col">
                  <div class="form-outline">
                      <input type="email" name="emailAddress" class="form-control form-control-sm"/>
                      <label class="form-label" for="emailAddress">Last School Year completed</label>
                  </div>
                </div>

                <div class="col">
                  <div class="form-outline">
                      <input type="email" name="emailAddress" class="form-control form-control-sm"/>
                      <label class="form-label" for="emailAddress">Last School Attended</label>
                  </div>
                </div>

                <div class="col">
                  <div class="form-outline">
                      <input type="email" name="emailAddress" class="form-control form-control-sm"/>
                      <label class="form-label" for="emailAddress">School Address</label>
                  </div>
                </div>


                <div class="col">
                <label class="form-label ms-4 me-2" for="emailAddress">School Type</label>
                  <select name="grade" class="select form-control-sm">
                    <option value="1" disabled selected>Choose option</option>
                    <option value="Pre-school">Private</option>
                    <option value="Kinder">Public</option>
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
                    <input type="text" name="firstName" class="form-control form-control-sm" />
                    <label class="form-label" for="firstName">Father's Full Name</label>
                  </div>
                </div>
<!-- FOccupation -->
                <div class="col mb-4">
                  <select name="grade" class="select form-control-sm ms-4">
                    <option value="1" disabled selected>Choose option</option>
                    <option value="Pre-school">Elementary Graduate</option>
                    <option value="Kinder">High School Graduate</option>
                    <option value="Kinder">College Graduate</option>
                    <option value="Kinder">Vocational</option>
                    <option value="Kinder">Master's/Doctorate Degree</option>
                    <option value="Kinder">Did not attend school</option>
                  </select>
                  <label class="form-label ms-4 me-2" for="emailAddress">Highest Educational Attainment</label>
                </div>
<!-- FC  -->
                <div class="col mb-4">
                  <select name="grade" class="select form-control-sm ms-4">
                    <option value="1" disabled selected>Choose option</option>
                    <option value="Pre-school">Full time employee</option>
                    <option value="Kinder">Part-time/Contractual Employee</option>
                    <option value="Kinder">Self-employed</option>
                    <option value="Kinder">Currently not employed</option>
                  </select>
                  <label class="form-label ms-4 me-2" for="emailAddress">Employment Status</label>
                </div>

                <div class="col mb-4">
                  <div class="form-outline">
                    <input type="text" name="firstName" class="form-control form-control-sm" />
                    <label class="form-label" for="firstName">Contact Number</label>
                  </div>
                </div>


                <div class="row">

<!-- Mother -->
                <div class="col mb-4">
                  <div class="form-outline">
                    <input type="text" name="firstName" class="form-control form-control-sm" />
                    <label class="form-label" for="firstName">Mother's Full Name</label>
                  </div>
                </div>
<!-- MOccupation -->
                <div class="col mb-4">
                  <select name="grade" class="select form-control-sm ms-4">
                    <option value="1" disabled selected>Choose option</option>
                    <option value="Pre-school">Elementary Graduate</option>
                    <option value="Kinder">High School Graduate</option>
                    <option value="Kinder">College Graduate</option>
                    <option value="Kinder">Vocational</option>
                    <option value="Kinder">Master's/Doctorate Degree</option>
                    <option value="Kinder">Did not attend school</option>
                  </select>
                  <label class="form-label ms-4 me-2" for="emailAddress">Highest Educational Attainment</label>
                </div>
<!-- MC  -->
                <div class="col mb-4">
                  <select name="grade" class="select form-control-sm ms-4">
                    <option value="1" disabled selected>Choose option</option>
                    <option value="Pre-school">Full time employee</option>
                    <option value="Kinder">Part-time/Contractual Employee</option>
                    <option value="Kinder">Self-employed</option>
                    <option value="Kinder">Currently not employed</option>
                  </select>
                  <label class="form-label ms-4 me-2" for="emailAddress">Employment Status</label>
                </div>

                <div class="col mb-4">
                  <div class="form-outline">
                    <input type="text" name="firstName" class="form-control form-control-sm" />
                    <label class="form-label" for="firstName">Contact Number</label>
                  </div>
                </div>
                </div>

          <div class="row">
            
                  <div class="col mt-2 pt-1">
                    <a class="ms-2 mb-4 px-4 btn btn-secondary btn-md" name="save_admission" href="index.php">Cancel</a>
                  </div>
                  <div class="col text-end  ms-5 mt-2 pt-1">
                    <input class="mb-4 px-4 btn btn-primary btn-md" name="save_admission" type="submit" value="Submit" />
                  </div>
            </form>
<!---------------- form ------------------->
          </div>
        </div>
      </div>
    </div>
  </div>
</section>


<script>
    // Activate tooltip for all elements with data-toggle="tooltip"
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
</body>
</html>
