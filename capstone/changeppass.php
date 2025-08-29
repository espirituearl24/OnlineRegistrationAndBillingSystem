<?php

  session_start();
  include 'connection.php';
  $alert = "";

  if(isset($_SESSION['lastname']) && isset($_SESSION['firstname'])){
    $userl = $_SESSION['lastname'];
    $userf = $_SESSION['firstname'];
  }

  if(isset($_POST['btn_changepass'])){
    $oldpass = $_POST['op'];
    $newpass = $_POST['np'];
    $confirmpass = $_POST['cp'];

    if(!empty($oldpass) && !empty($newpass) && !empty($confirmpass)){


      $checkAcc = mysqli_query($conn, "SELECT * FROM users WHERE firstname = '$userf' AND lastname = '$userl' ");
      if(mysqli_num_rows($checkAcc) > 0){
        $user = mysqli_fetch_assoc($checkAcc);
        $storedPassword = $user['password'];

        if($oldpass == $storedPassword || password_verify($oldpass, $storedPassword)){
          
          if($newpass == $confirmpass){

            $newHashedPassword = password_hash($newpass, PASSWORD_DEFAULT);

            $updatePass = mysqli_query($conn, "UPDATE users SET password = '$newHashedPassword' WHERE firstname = '$userf' AND lastname = '$userl' ");
            if($updatePass){
              $_SESSION['error'] = "Password Changed!";
              $_SESSION['status'] = "success";
              $_SESSION['title'] = "Success!";
              header('Location: principal.php');
              exit();
            }else{
              $_SESSION['error'] = "Password not Changed Try Again!";
              $_SESSION['status'] = "error";
              $_SESSION['title'] = "Notice!";
              header('Location: principal.php');
              exit();
            }
          }else{
            $alert = "Password don't match.";
          }
        }else{
          $alert = "Your password is incorrect.";
        }
      }else{
        $alert = "Account don't exist.";
      }
    }else{
      $alert = "Please fill out all the fields.";
    }
  }



?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <style>
    body {
        background-color: white;
    }

</style>

</head>
<body>
    

<section>
  <div class="container py-5 h-100 mt-5">
    <div class="row d-flex justify-content-center align-items-center h-100 ">
      <div class="col-12 col-md-6 col-lg-5 col-xl-4 "> <!-- Adjusted to smaller form size -->
        <div class="card position-relative border border-white">
          <!-- Sample Image at the Top -->
          <img src="img/logo1.png" alt="Sample Image" class="position-absolute top-0 start-50 translate-middle-x mx-1" style="width: 115px; height: 115px; object-fit: cover; border-radius: 50%; border: 2px solid black;  margin-top: -50px; z-index: 9999;">

          <div class="card-body p-4 text-start text-white border border-dark border-2" style="background-color: #296aeb; padding-top: 80px; border-radius:2rem;">
            <div class="mx-4">
              <p class="text-danger mb-5"></p>

              <form action="changeppass.php" method="POST">
                <div class="form-outline mb-3">
                  <label class="form-label" for="oldPass">Old Password</label>
                  <input type="password" name="op" id="oldPass" class="form-control form-control-md border border-dark border-2 rounded-pill" /> <!-- Smaller input -->
                  
                </div>

                <div class="form-outline mb-3">
                  <label class="form-label" for="newPass">New Password</label>
                  <input type="password" name="np" id="newPass" class="form-control form-control-md border border-dark border-2 rounded-pill" /> <!-- Smaller input -->
                  
                </div>

                <div class="form-outline mb-3">
                  <label class="form-label" for="conFirmPass">Confirm Password</label>
                  <input type="password" name="cp" id="conFirmPass" class="form-control form-control-md border border-dark border-2 rounded-pill" /> <!-- Smaller input -->
                  
                </div>

                <button class="btn  btn-md px-4 text-white " type="submit" name="btn_changepass" style="background-color: #091523;">Change Password</button> <!-- Smaller button -->
              </form>

              <button onclick="location.href='principal.php'" class="btn btn-info mt-2 p-2">
                Cancel
              </button>

              <p class="text-danger mb-5"><?php echo $alert; ?></p>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>







<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>
</html>