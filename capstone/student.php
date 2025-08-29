<?php


session_start();

include 'dbconnection.php';

$userl = $_SESSION['lastname'];
$userf = $_SESSION['firstname'];


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome, Student</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
    rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" 
    crossorigin="anonymous">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" 
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" 
    crossorigin="anonymous"></script>
<!-- Bootstrap End-->


</head>
<body>
    <nav class="navbar navbar-expand-lg" style="background-color:#0d254d;">
    <div class="container-fluid">

    <!-- Logo -->
        <a class="navbar-brand ps-3 text-light " href="#">
        <b class=""><?php echo $userl . ",";   ?></b> <?php echo $userf; ?>
        </a>
    <!-- Logo End-->

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
        </button>
        </div>
    </div>
    </nav>
<!-- Navbar  End-->

<div class="container-fluid" >
    <div class="row flex-nowrap">
        <div class="col-auto col-md-3 col-xl-2 px-sm-2 px-0" >
            <div class="d-flex flex-column align-items-center align-items-sm-start px-3 pt-2 text-white min-vh-100" style="background-color:#0b2d66; width: 230px; margin-left:-8px;">
                <img src="img/logo1.png" style="width:130px;height:130px; margin-left:30px; margin-bottom:25px; margin-top:20px;">
                <ul class="nav nav-pills flex-column mb-sm-auto mb-0 align-items-center align-items-sm-start" style="margin-left:40px;" id="menu" >
                      <li>
                        <a href="#submenu1" data-bs-toggle="collapse" class="nav-link px-0 align-middle px-0 text-light">
                            <i class="fa fa-file"></i> <span class="ms-1 d-none d-sm-inline" style="font-size:15px;">Profile</span> 
                        </a>
                    </li>
                    <li>
                        <a href="#submenu1" data-bs-toggle="collapse" class="nav-link px-0 align-middle px-0 text-light">
                            <i class="fa fa-file"></i> <span class="ms-1 d-none d-sm-inline" style="font-size:15px;">Payment List</span> 
                        </a>
                    </li>
                    <li>
                        <a href="#submenu2" data-bs-toggle="collapse" class="nav-link px-0 align-middle text-light">
                            <i class='fa fa-user-circle-o'></i> <span class="ms-1 d-none d-sm-inline" style="font-size:15px;">History</span> 
                        </a>
                    </li>
                    <li>
                        <a href="admin_stud.php" class="nav-link px-0 align-middle text-light">
                        <i class='fa fa-fw fa-group'></i> <span class="ms-1 d-none d-sm-inline" style="font-size:15px;">Due Dates</span></a>
                    </li>
                </ul>
                <hr>
                <div class="dropdown pb-4">
                    <a href="index.php" onclick="return confirm('Are you sure you want to logout?');" class="d-flex align-items-center text-white text-decoration-none " style=" margin-top:-50px;" id="dropdownUser1" >
                        <img src="img/icon.png" alt="hugenerd" width="25" height="25" class="rounded-circle" style=" margin-bottom:-75px; margin-left:30px;">
                        <span class="d-none d-sm-inline mx-2" style="font-size:15px; margin-bottom:-75px; ">Sign out</span>
                    </a>
                </div>
            </div>
        </div>
<div class="container-fluid"></div>


</body>
</html>