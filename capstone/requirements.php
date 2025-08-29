<?php

session_start();
include 'connection.php';
$alert = "";

if(isset($_POST['btnLogin'])){
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    if(!empty($username) && !empty($password)){
        $checkLogin = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username' ");

        if(mysqli_num_rows($checkLogin) > 0){
            $row = mysqli_fetch_assoc($checkLogin);
            $storedPassword = $row['password'];

            if(password_verify($password, $storedPassword) || $password === $storedPassword){
                $_SESSION['user'] = $row['username'];
                $_SESSION['type'] = $row['type'];
                $_SESSION['firstname'] = $row['firstname'];
                $_SESSION['lastname'] = $row['lastname'];
                $_SESSION['id'] = $row['id'];
                header('location:login_directory.php');
            }else{
                
              
             $_SESSION['error'] = "Incorrect Password!";
             header('location:index.php');
             exit();
            }
        }else{
            
            $_SESSION['error'] = "No such user in our database!";
             header('location:index.php');
             exit();
 
        }
    }else{
        
        $_SESSION['error'] = "Please Input your Username and Password!";
        header('location:index.php');
        exit();
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

    <!-- Scroll up -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<!-- Bootstrap End-->


<link href="https://maxcdn.bootstrapcdn.com/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">

<link href="css/style.css" rel="stylesheet">

<link rel="icon" href="img/logo1.png">

<!--------------------------- Link End ----------------------------->

    <style class="">
        .navcolor {
            background-color: yellow;
        }
        body {
                overflow-x: hidden; /* Disable vertical scroll */
            }

    /*Card*/
    .code-card {
                width: 300px;
                background-color: #f0f0f0;
                border-radius: 10px;
                padding: 20px;
                text-align: center;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            }

            .icon-circle {
                width: 100px;
                height: 100px;

                border-radius: 50%;
                display: flex;
                justify-content: center;
                align-items: center;
                margin: 0 auto 20px;
            }

            .icon-circle img {
                width: 80px; /* Adjust image size as needed */
                height: 80px; /* Adjust image size as needed */
                border-radius: 50%;
                object-fit: cover;
            }

            .btn1 {
                background-color: #fcba03;
                padding: 10px 20px;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                transition: background-color 0.3s ease;
            }

            .btn1:hover {
                background-color: #007bff;
                color: #ffffff;
            }
    /*Card*/

            .parallax {
                
                background-image: url('img/par1.png');
                height: 100%; 
                background-attachment: fixed;
                background-position: center;
                background-repeat: no-repeat;
                background-size: cover;

                
                background-color:red;
                
            }

    /*CARD HOVER*/

            .card1 {
                transition: all 0.3s;
            }
            .card1:hover{
                transform: scale(1.15);
            }

    /*scroll up button*/
            .myBtn {
                display: none;
                position: fixed;
                bottom: 20px;
                right: 30px;
                z-index: 99;
                font-size: 18px;
                border: none;
                outline: none;
                background-color: rgba(22,22,26,0.18);
                color: orange;
                cursor: pointer;
                padding: 7px;
                border-radius: 50%;;
                }

                .myBtn:hover {
                background-color: rgba(22,22,26,0.3);
                }

                
            .fix{
                position: absolute;
            }

            #scrollUpBtn {
                position: fixed;
                bottom: 20px;
                right: 20px;
                display: none;
            }   
    </style>

</head>
<body class = "">
<!-- Navbar -->
<nav class="navbar navbar-expand-lg " style="background-color:#0a2757;">
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

        <div class="collapse navbar-collapse " id="navbarSupportedContent">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0 pe-3 ">
            <li class="nav-item px-3">
                <a class="nav-link active text-light me-1" aria-current="page" href="index.php">Home</a>
            </li>
            <li class="nav-item px-3">
                <a class="nav-link active text-light me-1" aria-current="page" href="#contact">Contact</a>
            </li>
            <li class="nav-item px-3">
                <a class="nav-link active text-light me-1" aria-current="page" href="#aboutus">About Us</a>
            </li>
            <li class="nav-item px-3 dropdown">
                <a class="nav-link active text-light me-1 dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">Admission</a>                  
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <li><a class="dropdown-item" href="admission.php">New Student</a></li>
                    <li><a class="dropdown-item" href="admission1.php">Old Student</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#">Requirements</a></li>
                </ul>
            </li>
            <li class="nav-item px-3">
                <a class="nav-link active text-light me-1" aria-current="page" href="#" data-bs-toggle="modal" data-bs-target="#exampleModal">Account</a>
                <!-- trigger modal when account is clicked -->

                    <!-- Modal -->
                    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="exampleModalLabel">
                                <img src="img/logo1.png" alt="Logo" width="40" height="40" class="d-inline-block align-text-center"> <!-- This is top now center -->
                                    Grace Baptist Academy
                                </h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                                <div class="modal-body">

                                    <!-- Login form -->
                                    <form class="form" action="<?php echo $_SERVER['PHP_SELF'];?> " method="POST">
                                        <div class="ms-2 mb-2">Username</div>
                                        <input type="text" class="form-control " name="username" autocomplete="off">
                                        <div class="ms-2 mb-2 mt-2">Password</div>
                                        <input type="password" class="form-control" name="password" autocomplete="new-password">
                                        <div class="text-end mt-3"><i class=""> </i></div>
                                        <div class="text-center my-3"><button type="submit" name="btnLogin" class= "btn btn-primary text-center mt-2 w-100" >Login</button></div> 
                                    </form>
                                    <!-- Login form End-->
                                </div>
                            </div>
                        </div>
                    </div>
            </li>
        </ul>

        </div>
    </div>
    </nav>
<!-- Navbar  End-->


<!-------------- Section 3 CARD--------------->

<div class="container mt-5">
                    <!-- Accordion -->
                    <div class="accordion accordion-flush" id="accordionFlushExample">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                                New / Transferee Students Requirements
                            </button>
                            </h2>
                            <div id="flush-collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                                <div class="accordion-body"> <code>STEP 1.</code> Check for eligibility and required documents for enrollment.<br><br>

                                        <code>STEP 2.</code> List of  documents need to prepare:<br>
                                            Certificate of Transfer (Original & Photocopied)<br>
                                            Certificate o Grades (Original & Photocopied)<br>
                                            Good Moral Certificate (Original with Dry Seal & Photocopied)<br>
                                            2pcs. Photocopied Birth Certificate (PSA)<br>
                                            4pcs. 2x2 Picture (White background with name tag)<br>
                                            2pcs. 1x1 Picture (White background)<br>
                                            1pc. Long Brown Envelope<br><br>

                                        <code>STEP 3.</code> Fill-out Online Admissions Form.<br><br>

                                        <code>STEP 4.</code> Pay the reservation fee using any of the payment methods convenient to you. You can also pay in full 
                                                        or in installments. Once paid,  it is non-refundable and non-transferable but is deductible from the 
                                                        FINAL Tuition  payment.<br><br>

                                            Complete Step 3 by uploading your proof of payment. It may take 2-3 business days to receive the email.<br><br>

                                        <code>STEP 5.</code> Enroll your child. Pay the tuition and fees in school and bring all the documents stated in step2.<br><br>

                                        Another option is to make payments through any of the accepted online payment methods available. Be sure to upload your proof of payment 
                                        before clicking the submit button. However, you will still need to visit the school for document submission.<br><br>

                                        Your child's  Username and Temporary Password will be sent to your registered email once proof payment and documents have been submitted.
                                        It is possible that this email will be found in your Junk/Spam Mail instead of your Inbox.<br><br>

                                        <code>STEP 6.</code> Purchase Uniforms<br>
                                                    During your onsite visit, you may order and pay for books and uniforms.<br><br>
                                                    

                                        Release/Schedule of pick-up of uniforms will be made on the school's social media platform. It is necessary to show proof of payment
                                        or school-issued Official Receipts to claim the textbooks and uniforms.<br><br>

                                        <code>ATTENTION:</code> Students who are unable to provide the additional required documents shall be provisionally enrolled until all documents are submitted
                                         within the first quarter of the school year. <br>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseTwo" aria-expanded="false" aria-controls="flush-collapseTwo">
                                Old Student Requirements
                            </button>
                            </h2>
                            <div id="flush-collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                            <div class="accordion-body">

                            <code>STEP 1.</code> Fill-out the Online admissions form for old/continuing students.<br><br>

                            <code>STEP 2.</code> Pay the reservation fee using any of the payment methods convenient to you. You can also pay in full 
                                            or in installments. Once paid,  it is non-refundable and non-transferable but is deductible from the 
                                            FINAL Tuition  payment.<br><br>

                                Complete Step 2 by uploading your proof of payment. It may take 2-3 business days to receive the email.<br><br>

                            <code>STEP 3.</code> Enroll your child. Pay the tuition and fees in school.<br><br>

                                Another option is to make payments through any of the accepted online payment methods available. Be sure to upload
                                your proof of payment before clicking the submit button. However, you will still need to visit the school for
                                document submission.<br><br>

                                Your child's  Username and Temporary Password will be sent to your registered email once proof payment and documents
                                 have been submitted. It is possible that this email will be found in your Junk/Spam Mail instead of your Inbox.<br><br>

                            <code>STEP 4.</code> Purchase Uniforms<br>
                                            During your onsite visit, you may order and pay for uniforms.<br><br>

                                Release/Schedule of pick-up of books and uniforms will be made on the school's social media platform.
                                It is necessary to show proof of payment or school-issued Official Receipts to claim the textbooks and uniforms.<br><br>
                            </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseThree" aria-expanded="false" aria-controls="flush-collapseThree">
                                Eligibility for Admission
                            </button>
                            </h2>
                            <div id="flush-collapseThree" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                            <div class="accordion-body">
                                
                                <code>PRE-KINDER 1</code> <br>
                                            Must be 3 years old by September 30 the academic year<br>
                                            Should be potty trained prior to when school starts (for face to face classes only)<br><br>

                                <code>PRE-KINDER 2</code><br>
                                             Must be at least 4 years old by September 30 of the academic year.<br><br>

                                <code>KINDERGARTEN</code><br>
                                                Must be at least 5 years old by September 30 of the academic year.<br><br>

                                <code>GRADE 1</code> <br>
                                                Must be a completer of Kindergarten<br>
                                                Should be at least 6 years old by September 30 of the academic year<br><br>

                                <code>GRADE </code> <br>
                                                Must be a completer of Grade 6.<br><br>
                            </div>
                            </div>
                        </div>
                        </div>
                <!-- Accordion -->

</div>
    <br><br><br>


<!-- Section 3 End-->

 
<!-- footer -->
    <div class="py-4 text-center text-light " style="background-color:#0a2757;" style="font-family:Helvetica;" id="contact">
        <div class="mb-3">
            <a href="index.php"><img src="img/logo1.png" alt="Logo" width="60" height="60" class="d-inline-block align-text-center "></a>
        </div>

    <div class="container row thin-text mx-auto mt-3 fade-in1">
        <div class="col text-end me-3 ">
            <span class="fw-bold text-warning ">GET IN TOUCH</span><br>
            Block 1 Lot 1-6 Brgy. Sta. Fe Extension <br>City of Dasmarinas Cavite, <br><br>

            Mob. No: (+63) 918 9901 868<br>
            gracebaptistacademy2017@yahoo.com
        </div>

        <div class="col text-start ms-3 fade-in1">
            <span class="fw-bold text-warning ">STAY CONNECTED</span><br>
            <a href="https://www.facebook.com/gracebaptistacademy" target="_blank"> 
                <img src="img/fb.png" alt="Logo" width="30" height="30" class="d-inline-block align-text-center mt-3">
            </a>
            <a href="#"> 
                <img src="img/ig.png" alt="Logo" width="30" height="30" class="d-inline-block align-text-center mt-3 ms-3">
            </a>
        </div>
        
    </div>

    <div class="thin-text mt-5">
        Copyright © 2024 GBA. All rights reserved.
    </div> 

    <!--SCROLL UP BUTTON-->
    <button id="scrollUpBtn" class="btn myBtn">
        <img src="img/up2.png" width="40" height="40">
    </button>


    </div>
<!-- footer End -->


<!-- JavaScript Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" 
integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" 
crossorigin="anonymous"></script>

<!---jquery-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js" 
integrity="sha512-aVKKRRi/Q/YV+4mjoKBsE4x3H+BkegoM/em46NNlCqNTmUYADjBbeNefNxYV7giUp0VxICtqdrbqU7iVaeZNXA==" 
crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<!----------------Sweet Alert---------------------->

<script src="assets/js/sweetalert.min.js"></script>
<?php
    if(isset($_SESSION['error'])){
        ?>
        <script>
        swal({
            title: "Error!",
            text: "<?php echo $_SESSION['error'];   ?>",
            icon: "error",
            button: "Ok",
          });
        </script>
          <?php
          unset($_SESSION['error']);
    }
?>

<!---------------- For scroll up ---------------->
<script>
  document.addEventListener("DOMContentLoaded", function() {
    var scrollUpBtn = document.getElementById("scrollUpBtn");

    window.onscroll = function() {scrollFunction()};

    function scrollFunction() {
      if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
        scrollUpBtn.style.display = "block";
      } else {
        scrollUpBtn.style.display = "none";
      }
    }

    scrollUpBtn.addEventListener("click", function() {
      document.body.scrollTop = 0;
      document.documentElement.scrollTop = 0;
    });
  });
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>




</body>
</html>