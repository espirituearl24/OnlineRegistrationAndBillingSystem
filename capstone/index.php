<?php
ob_start(); // Start output buffering
session_start();
include 'connection.php';
$alert = "";

$flashMessage = isset($_SESSION['flash_message']) ? $_SESSION['flash_message'] : null;

// Clear the flash message immediately after retrieving it
if ($flashMessage) {
    unset($_SESSION['flash_message']);
}

$query = "SELECT * FROM team_members";
$result = $conn->query($query);

// Check if the query was successful
if (!$result) {
    die("Query failed: " . $conn->error);
}

// Fetch all rows into an associative array
$teamMembers = $result->fetch_all(MYSQLI_ASSOC);

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home | GBA</title>


    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
    rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" 
    crossorigin="anonymous">

    	<!-- CSS only -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" 
	integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" 
	crossorigin="anonymous">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" 
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" 
    crossorigin="anonymous"></script>

    <!-- Scroll up -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<!-- Bootstrap End-->


<link href="https://maxcdn.bootstrapcdn.com/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">

<link href="css/style.css" rel="stylesheet">
<script src="assets/js/script.js"></script>

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

    /* Styles for the fading effect */
            .fade-in {
            opacity: 0;
            transform: translateY(20px); /* Initially translate downwards */
            transition: opacity 1s ease-in-out, transform 1s ease-in-out;
            }

            .fade-in.active {
            opacity: 1;
            transform: translateY(0); /* Translate to original position */
            }

            .fade-in1 {
            opacity: 0;
            transform: translateY(20px); /* Initially translate downwards */
            transition: opacity 0.3s ease-in-out, transform 0.3s ease-in-out;
            }

            .fade-in1.active {
            opacity: 1;
            transform: translateY(0); /* Translate to original position */
            }

            /* Adjust the dropdown menu position */
            .dropdown-hover .dropdown-menu {
                margin-top: 0;
            }

            /* Show the dropdown menu when hovering over the button */
            .dropdown-hover:hover .dropdown-menu {
                display: block;
            }

            /* Ensure the dropdown menu disappears when not hovered over */
            .dropdown-hover:not(:hover) .dropdown-menu {
                display: none;
            }

            .image-container {
            position: relative;
            width: 100%;
            height: 0;
            padding-top: 56.25%; /* 16:9 aspect ratio (height / width * 100) */
            overflow: hidden;
            }
            
            .image-container img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            }

            .picture {
            max-width: 100%; /* Ensure the image doesn't exceed its container */
            height: auto; /* Maintain aspect ratio */
            display: block; /* Ensure the image behaves as a block element */
            }
             
            .thin-text {
            font-weight: 300; /* or 'lighter' */
            }

    </style>




</head>



<body class = "">

<?php 
if (isset($_POST['btnOldLogin'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (!empty($username) && !empty($password)) {
        $checkLogin = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");

        if (mysqli_num_rows($checkLogin) > 0) {
            $row = mysqli_fetch_assoc($checkLogin);
            $storedPassword = $row['password'];

            if (password_verify($password, $storedPassword) || $password === $storedPassword) {
                // Set session variables
                $_SESSION['user'] = $row['username'];
                $_SESSION['type'] = $row['type'];
                $_SESSION['id'] = $row['id']; // User ID

                // Fetch the user's grade level based on their ID
                $id = $_SESSION['id'];

                // Fetch the grade level from the enroll table
                $sql = "SELECT `grade` FROM enroll WHERE `id` = ?";
                $statement = $conn->prepare($sql);
                $statement->bind_param("i", $id); // "i" indicates the type is integer
                $statement->execute();
                $result = $statement->get_result();
                $userData = $result->fetch_assoc();

                if ($userData) {
                    $grade = $userData['grade'];

                    // Fetch Payments
                    $sql3 = "SELECT * FROM payments WHERE `admission_id` = ?";
                    $statement3 = $conn->prepare($sql3);
                    $statement3->bind_param("i", $id); // Bind the user ID
                    $statement3->execute();
                    $result3 = $statement3->get_result();
                    $paymentData = $result3->fetch_assoc();

                    // Initialize total amount and total paid
                    $totalAmount = 0;
                    $totalPaid = 0;

                    if ($paymentData) {
                        // Check payment terms
                        if ($paymentData['payment_terms'] === 'installment') {
                            // Fetch Tuition based on Grade Level
                            $sql4 = "SELECT `install_total`, `install_monthly` FROM tuition WHERE `grade_level` = ?";
                            $statement4 = $conn->prepare($sql4);
                            $statement4->bind_param("s", $grade); // Assuming grade is a string
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
                    $statement7->bind_param("i", $id); // Bind the user ID
                    $statement7->execute();
                    $result7 = $statement7->get_result();

                    // Sum up all payments made
                    while ($row = $result7->fetch_assoc()) {
                        $totalPaid += (float)$row['amount'];
                    }

                    // Calculate remaining balance
                    $remainingBalance = $totalAmount - $totalPaid;

// Calculate remaining balance
$remainingBalance = $totalAmount - $totalPaid;

if ($remainingBalance <= 0) {
    // Set session variable to show alert for outstanding balance 
    // Set session variable to show alert for no outstanding balance
    $_SESSION['no_balance_alert'] = [
        'title' => "Enrollment Notification",
        'text' => "No Outstanding Balance, you can now enroll.",
        'icon' => "success"
    ];
    header('location: admission1.php'); // Redirect to the alert page
    exit(); 
} elseif ($remainingBalance >= 0) {

       $_SESSION['alert'] = [
        'title' => "Outstanding Balance",
        'text' => "You have an outstanding balance, please settle it first.",
        'icon' => "warning"
    ];
    header('location: admission1.php'); // Redirect to the alert page
    exit();
} else {
                        header('location: login_directory.php');
                        exit();
                    }
                } else {
                    $_SESSION['error'] = "User  enrollment data not found!";
                    header('location: index.php');
                    exit();
                }
            } else {
                $_SESSION['error'] = "Incorrect Password!";
                header('location: index.php');
                exit();
            }
        } else {
            $_SESSION['error'] = "No such user in our database!";
            header('location: index.php');
            exit();
        }
    } else {
        $_SESSION['error'] = "Please Input your Username and Password!";
        header('location: index.php');
        exit();
    }
}
?>

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
            <li class="nav-item px-3 dropdown dropdown-hover">
                <a class="nav-link active text-light me-1 dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">Admission</a>                  
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <li><a class="dropdown-item" href="admission.php">New Student</a></li>
                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#OldModal">Old Student</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#requirements">Requirements</a></li>
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

                                        <!-- Modal -->
                                        <div class="modal fade" id="OldModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                                        <div class="ms-2 mb-2">Student Number</div>
                                        <input type="text" class="form-control " name="username" autocomplete="off">
                                        <div class="ms-2 mb-2 mt-2">Password</div>
                                        <input type="password" class="form-control" name="password" autocomplete="new-password">
                                        <div class="text-end mt-3"><i class=""> </i></div>
                                        <div class="text-center my-3"><button type="submit" name="btnOldLogin" class= "btn btn-primary text-center mt-2 w-100" >Login</button></div> 
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

<!-- Carousel -->
<div id="carouselExampleCaptions" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">
    <div class="carousel-indicators">
        <?php
        $i = 0;
        $activeSet = false;
        $result = $conn->query("SELECT * FROM carousel_images WHERE show_in_carousel = 1");

        while ($row = $result->fetch_assoc()):

            
        ?>
            <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="<?php echo $i; ?>"
                class="<?php echo $i === 0 ? 'active' : ''; ?>" aria-current="<?php echo $i === 0 ? 'true' : 'false'; ?>"
                aria-label="Slide <?php echo $i + 1; ?>"></button>
        <?php $i++; endwhile; ?>
    </div>

    <div class="carousel-inner">
        <?php
        $result->data_seek(0); // Reset pointer to loop again
        $i = 0;
        while ($row = $result->fetch_assoc()):
        ?>
            <div class="carousel-item <?php echo $i === 0 ? 'active' : ''; ?>">
                <img src="<?php echo htmlspecialchars($row['image_path']); ?>" class="d-block w-100" alt="...">
                
            </div>
        <?php $i++; endwhile; ?>
    </div>

    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>



<!-- Carousel End -->


<div class="py-5" >
    <div class="row fade-in" id="aboutus">
        <div class="col my-2 ms-5 ps-5 pt-5 mt-5 " >
            <div class="display-3 fw-bold "> 
                Grace Baptist Academy of Dasmariñas Inc.
            </div>
            
            <div class="mt-4 me-5 ">
                Grace Baptist Academy (GBA) is an educational ministry of Grace Baptist Church – Dasmariñas
                founded by Mrs. Constancia Bermejo in 1993 located at Sta. Fe Extension, City of Dasmariñas
                Cavite, Philippines. It is a Bible-based Curriculum private institution that offers educational
                levels from Pre-School to Junior High School. 
            </div>
            
            <p class="card-text ">
				<div class=""><a class= "btn btn-outline-primary fw-bold text-center" href="https://www.facebook.com/gracebaptistacademy" target="_blank">GO TO THE FACEBOOK PAGE</a></div>
			</p>

        </div>
        <div class="col my-2 ">
            <img src="img/logo1.png" class="d-block w-100" alt="...">
        </div>
    </div>
</div>

<!-------------------Parallax---------------------->
<div class="parallax"> <!---Code is in the head tag---->
    <div class="container py-5">
        <div class="title pt-5 pb-2 text-center fw-bold text-white">
            <h1 class="fw-bold pt-3 fade-in1">Proverbs 22:6<span class="logocolor"></span></h1>
        </div>

        <div class="text-center pb-5 text-white fade-in1" style="font-size: 20px;">
            <strong>Train up a child in the way he should go, and when he is old he will not depart from it.</strong>
        </div>

        <div class="row my-5 ms-4">
            <?php foreach ($teamMembers as $member): ?>
                <div class="col mt-2">
                    <div class="card card1" style="width: 18rem;">
                        <!-- Display image dynamically based on image_path from database -->
                        <img src="<?php echo htmlspecialchars($member['image_path']); ?>" class="card-img-top" alt="Team Member Image">
                        <div class="card-body text-center">
                            <h5 class="card-title fw-bold"><?php echo htmlspecialchars($member['name']); ?></h5>
                            <p class="card-text">
                                <span class="fst-italic"><?php echo htmlspecialchars($member['position']); ?></span><br>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div><br>

        <div class="mb-5">
        </div>
    </div>
</div>

<!---------- Section 1 End ----------->


<br> <!----JUMP ABOUT US--->
<!-------------Section 2--------------> 

<div class="text-center">
    <div class="row p-5">
        <div class="col m-5 code-card fade-in1" >
            <div class="icon-circle ">
                <img src="assets/img/sidebar/icon-2-F.png" alt="Icon"> <!-- Replace 'your-image.jpg' with your image path -->
            </div>
            <a class="btn1 fw-bold text-decoration-none" href="admission.php">New Student</a>
        </div>

        <div class="col m-5 code-card fade-in1" >
            <div class="icon-circle">
                <img src="assets/img/sidebar/icon-2-F.png" alt="Icon"> <!-- Replace 'your-image.jpg' with your image path -->
            </div>
            <a class="btn1 fw-bold text-decoration-none " href="admission1.php">Old Student</a>
        </div>

        <div class="col m-5 code-card fade-in1" >
            <div class="icon-circle">
                <img src="assets/img/sidebar/icon-3-F.png" alt="Icon"> <!-- Replace 'your-image.jpg' with your image path -->
            </div>
            <a type="button" class="btn1 fw-bold text-decoration-none" data-bs-toggle="modal" data-bs-target="#requirements">Requirements</a>
        </div>
    </div>
</div>

       <!-- Modal -->
       <div class="modal fade" id="requirements" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Requirements</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

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
                                            Form 138 (Report card)<br> 
                                            Good Moral Certificate<br>
                                            Good Moral Certificate (Original with Dry Seal)<br>
                                            2pcs. Photocopy of Birth Certificate (PSA)<br>
                                            Medical Certificate<br>
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
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                    </div>
                </div>
                </div>

<!-------------- Section 3 CARD--------------->

<div class="container">
    <div class="">
        <h1 class="display-3 text-center mb-4 fade-in1"><b class="">Overview</h1>
    </div>
    <div class="text-center">
        <p href="" class="text-light disable w-10 fw-bold mt-3 py-3 px-4 display-6 mx-auto fade-in1" style="width:50%; background-color:#0a2757;">Mission&Vision</p>
    </div>

</div>
    <br><br><br>


  <div class="container">
    <div class="row ">
      <div class="col-md-6">
        <img src="img/mardunong3.jpg" alt="Placeholder Picture" class="picture">
      </div>
      <div class="col-md-6 pt-4" style="background-color:#0a2757;">
            <div class="text-center pt-5">
                
                <p class="bg-warning disable w-10 fw-bold mt-3 py-3 px-4 display-6 mx-auto fade-in1" style="width:50%;">
                    Mission
                </p>

                <p class="text-light px-5 pt-3 fade-in1" > 
                    GBA's primary mission is to share God's  truths with children through education. 
                    It aims to equip  children with academic excellence founded on Biblical principles.
                </p>

            </div>
      </div>
    </div>
  </div>

  <div class="container">
    <div class="row">
      <div class="col-md-6" style="background-color:#0a2757;">
            <div class="text-center pt-5">
                
                <p class="bg-warning disable w-10 fw-bold mt-3 py-3 px-4 display-6 mx-auto fade-in1" style="width:50%;">
                    Vision
                </p>

                <p class="text-light px-5 pt-3 fade-in1" > 
                We envision a school that will be a safe place for learners to learn about God's truths. 
                We endeavor to train learners to become Godly professionals who would become productive 
                members of the society and the nation.
                </p>

            </div>
      </div>

      <div class="col-md-6">
        <img src="img/vision.jpg" alt="Placeholder Picture" class="picture">
      </div>

    </div>
  </div>
<!-- Section 3 End-->


<div id ="aboutus"></div><br><br><br><br> <!----JUMP ABOUT US--->

<div class="container">
    <div class="">
        <h1 class="display-3 text-center mb-4 fade-in1"><b class="">F</b>aith &bull; <b>C</b>haracter &bull; <b>E</b>xcellence</h1>
    </div>
    <div class="text-center">
        <p href="" class="bg-warning disable w-10 fw-bold mt-3 py-3 px-4 display-6 mx-auto fade-in1" style="width:50%;">#GBACoreValues</p>
    </div>

</div>
    <br><br><br>

<br>


	<div id= "menu"></div>

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

<?php
    // if(isset($_SESSION['error'])){
    //     ?>
    //       <?php
    //       unset($_SESSION['error']);
    // }
?>

<!---------------- For scroll up ---------------->
<script>

document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'success',
            title: 'Payment Successful',
            text: '<?php echo htmlspecialchars($flashMessage['message']); ?>',
            showConfirmButton: true
        });
    });


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

<!-------------- For transition ----------------->
<script>
  document.addEventListener("DOMContentLoaded", function() {
    window.addEventListener('scroll', checkFadeContent);

    function checkFadeContent() {
      var fadeContent = document.querySelector('.fade-in');
      fadeContent.classList.add('active');
    }
  });
</script>

<!-------------- For transition1 ----------------->
<script>
  document.addEventListener("DOMContentLoaded", function() {
    window.addEventListener('scroll', checkFadeElements);

    function checkFadeElements() {
      var fadeElements = document.querySelectorAll('.fade-in1');
      fadeElements.forEach(function(element) {
        if (isElementInViewport(element)) {
          element.classList.add('active');
        }
      });
    }

    function isElementInViewport(el) {
      var rect = el.getBoundingClientRect();
      return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.right <= (window.innerWidth || document.documentElement.clientWidth)
      );
    }
  });
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>


<?php


if(isset($_POST['btnLogin'])){
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    if(!empty($username) && !empty($password)){
        $checkLogin = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username' ");

        if(mysqli_num_rows($checkLogin) > 0){
            $row = mysqli_fetch_assoc($checkLogin);
            $storedPassword = $row['password'];

            if (password_verify($password, $storedPassword) || $password === $storedPassword) {
                $_SESSION['user'] = $row['username'];
                $_SESSION['type'] = $row['type'];
                $_SESSION['firstname'] = $row['firstname'];
                $_SESSION['lastname'] = $row['lastname'];
                $_SESSION['id'] = $row['id'];
                echo "<script>
                        Swal.fire({
                            icon: 'success',
                            title: 'Redirecting you to the Dashboard!',
                            text: 'wait a moment',
                        }).then(function() {
                            window.location.href = 'login_directory.php';
                        });
                      </script>";
            } else {
                echo "<script>
                        Swal.fire({
                            icon: 'error',
                            title: 'Incorrect Password!',
                            text: 'Check if the capslock is on!',
                        })
                      </script>";
                exit();
            }
        }else{
            echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'No Account Record!',
                        text: 'Contact GBA Admission office if you have an online account!',
                    })
                  </script>";
            exit();
 
        }
    }else{
        $alert = "Please fill out all fields!";
    }
}
?>


</body>
</html>