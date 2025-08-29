<?php

session_start();

require 'dbconnection.php';

$user = $_SESSION['lastname'];

// Fetch all team members from the database for the dropdown
try {
    $query = "SELECT * FROM team_members";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $teamMembers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching team members: " . $e->getMessage());
}

// Handle form submission for updating a team member
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];  // Get team member ID from form
    $name = $_POST['name'];
    $position = $_POST['position'];
    $description = $_POST['description'];
    $image_path = '';

    try {
        // Fetch the current image path of the team member
        $query = "SELECT image_path FROM team_members WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->execute([':id' => $id]);
        $member = $stmt->fetch(PDO::FETCH_ASSOC);
        $current_image_path = $member['image_path'];

        // Handle image upload
        if (!empty($_FILES['image']['name'])) {
            // If a new image is uploaded, delete the old one if it exists
            if (!empty($current_image_path) && file_exists($current_image_path)) {
                unlink($current_image_path);  // Delete the old image
            }

            $target_dir = "upload/";
            $target_file = $target_dir . basename($_FILES['image']['name']);
            $image_file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Check for valid image file types
            if (in_array($image_file_type, ['jpg', 'jpeg', 'png', 'gif'])) {
                if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                    $image_path = $target_file;  // Set the new image path
                } else {
                    die("Failed to upload file.");
                }
            } else {
                die("Invalid file type.");
            }
        } else {
            // If no new image is uploaded, use the old image
            $image_path = $current_image_path;
        }

        // Update the team member's information
        if ($id) {
            if ($image_path) {
                $query = "UPDATE team_members SET name = :name, position = :position, description = :description, image_path = :image_path WHERE id = :id";
                $params = [
                    ':name' => $name,
                    ':position' => $position,
                    ':description' => $description,
                    ':image_path' => $image_path,
                    ':id' => $id
                ];
            } else {
                $query = "UPDATE team_members SET name = :name, position = :position, description = :description WHERE id = :id";
                $params = [
                    ':name' => $name,
                    ':position' => $position,
                    ':description' => $description,
                    ':id' => $id
                ];
            }

            $stmt = $conn->prepare($query);
            $stmt->execute($params);

            header("Location: useradmin.php");
            exit;
        }
    } catch (PDOException $e) {
        die("Error updating team member: " . $e->getMessage());
    }
}

// Fetch data for the selected team member if any
$selectedMember = null;
if (isset($_GET['id'])) {
    try {
        $id = $_GET['id'];
        $query = "SELECT * FROM team_members WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->execute([':id' => $id]);
        $selectedMember = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Error fetching selected team member: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>GBA | Admin</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

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
<a class="dropdown-item" href="changepass.php?lastname=<?php echo $userl; ?>&firstname=<?php echo $userf; ?>">Change Password</a>
<a class="dropdown-item" href="logout.php" onclick="return confirm('Are you sure you want to logout?');">Logout</a>
</div>
</li>
</ul>
<div class="dropdown mobile-user-menu float-right"> 
<a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></a>
<div class="dropdown-menu dropdown-menu-right">
<a class="dropdown-item" href="#"> </a>
<a class="dropdown-item" href="#"> </a>
<a class="dropdown-item" href="changepass.php?lastname=<?php echo $userl; ?>&firstname=<?php echo $userf; ?>">Change Password</a>
<a class="dropdown-item" href="nbox.html" >Logout</a>
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
<span class="text-uppercase ms-2 mt-5">Admin</span>
</a>
</div>
<ul class="sidebar-ul">
<li class="menu-title"></li>
<li class="">
<a href="useradmin.php"><img src="assets/img/sidebar/icon-1.png" alt="icon"><span>Dashboard</span></a>
</li>
<li class="active">
  <a href="edit_team.php"><img src="assets/img/sidebar/icon-2.png" alt="icon"> <span> Edit Officials</span> <span class=""></span></a>
</li>

<li class="">
  <a href="emp_accounts.php"><img src="assets/img/sidebar/icon-4.png" alt="icon"> <span>Accounts</span> <span class=""></span></a>
</li>



</ul>
</div>
</div>
</div>
<!---------------SIDE BAR END---------------->

<div class="content container mt-4 d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="row justify-content-center w-100">
        <div class="col-md-10 col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="card-title mb-0">Edit School Officials in Homepage</h4>
                </div>
                <div class="card-body">
                    <!-- Dropdown to select team member -->
                    <form method="GET" action="edit_team.php" class="mb-4">
                        <div class="form-group row">
                            <label for="id" class="col-sm-3 col-form-label">Select School Offical:</label>
                            <div class="col-sm-9">
                                <select name="id" id="id" class="form-select" required>
                                    <option value="">-- Select School Offical --</option>
                                    <?php foreach ($teamMembers as $member): ?>
                                        <option value="<?php echo $member['id']; ?>" 
                                            <?php echo (isset($_GET['id']) && $_GET['id'] == $member['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($member['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-sm-12 text-center mt-3">
                                <button type="submit" class="btn btn-primary">Edit</button>
                            </div>
                        </div>
                    </form>

                    <!-- Form to edit selected team member -->
                    <?php if ($selectedMember): ?>
                        <h5 class="mb-3">Edit Team Member: <?php echo htmlspecialchars($selectedMember['name']); ?></h5>
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?php echo $selectedMember['id']; ?>">

                            <div class="form-group row mb-3">
                                <label for="name" class="col-sm-3 col-form-label">Name:</label>
                                <div class="col-sm-9">
                                    <input type="text" name="name" id="name" class="form-control"
                                           value="<?php echo htmlspecialchars($selectedMember['name']); ?>" required>
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label for="position" class="col-sm-3 col-form-label">Position:</label>
                                <div class="col-sm-9">
                                    <input type="text" name="position" id="position" class="form-control"
                                           value="<?php echo htmlspecialchars($selectedMember['position']); ?>" required>
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label for="description" class="col-sm-3 col-form-label">Description:</label>
                                <div class="col-sm-9">
                                    <textarea name="description" id="description" class="form-control" rows="4" required><?php echo htmlspecialchars($selectedMember['description']); ?></textarea>
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label for="image" class="col-sm-3 col-form-label">Image:</label>
                                <div class="col-sm-9">
                                    <input type="file" name="image" id="image" class="form-control">
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-9 offset-sm-3">
                                    <button type="submit" class="btn btn-success w-100">Save Changes</button>
                                </div>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-warning" role="alert">
                            Select a school official from the dropdown to edit their details.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>








<div class="notification-box">
<div class="msg-sidebar notifications msg-noti">
<div class="topnav-dropdown-header">
<span>Messages</span>
</div>
<div class="drop-scroll msg-list-scroll">
<ul class="list-box">
<li>
<a href="#">
<div class="list-item">
<div class="list-left">
<span class="avatar">R</span>
</div>
<div class="list-body">
<span class="message-author">Richard Miles </span>
<span class="message-time">12:28 AM</span>
<div class="clearfix"></div>
<span class="message-content">Lorem ipsum dolor sit amet, consectetur adipiscing</span>
</div>
</div>
</a>
</li>
<li>
<a href="#">
<div class="list-item new-message">
<div class="list-left">
<span class="avatar">J</span>
</div>
<div class="list-body">
<span class="message-author">Ruth C. Gault</span>
<span class="message-time">1 Aug</span>
<div class="clearfix"></div>
<span class="message-content">Lorem ipsum dolor sit amet, consectetur adipiscing</span>
</div>
</div>
</a>
</li>
<li>
<a href="#">
<div class="list-item">
<div class="list-left">
<span class="avatar">T</span>
</div>
<div class="list-body">
<span class="message-author"> Tarah Shropshire </span>
<span class="message-time">12:28 AM</span>
<div class="clearfix"></div>
<span class="message-content">Lorem ipsum dolor sit amet, consectetur adipiscing</span>
</div>
</div>
</a>
</li>
<li>
<a href="#">
<div class="list-item">
<div class="list-left">
<span class="avatar">M</span>
</div>
<div class="list-body">
<span class="message-author">Mike Litorus</span>
<span class="message-time">12:28 AM</span>
<div class="clearfix"></div>
<span class="message-content">Lorem ipsum dolor sit amet, consectetur adipiscing</span>
</div>
</div>
</a>
</li>
<li>
<a href="#">
<div class="list-item">
<div class="list-left">
<span class="avatar">C</span>
</div>
<div class="list-body">
<span class="message-author"> Catherine Manseau </span>
<span class="message-time">12:28 AM</span>
<div class="clearfix"></div>
<span class="message-content">Lorem ipsum dolor sit amet, consectetur adipiscing</span>
</div>
</div>
</a>
</li>
<li>
<a href="#">
<div class="list-item">
<div class="list-left">
<span class="avatar">D</span>
</div>
<div class="list-body">
<span class="message-author"> Domenic Houston </span>
<span class="message-time">12:28 AM</span>
<div class="clearfix"></div>
<span class="message-content">Lorem ipsum dolor sit amet, consectetur adipiscing</span>
</div>
</div>
</a>
</li>
<li>
<a href="#">
<div class="list-item">
<div class="list-left">
<span class="avatar">B</span>
</div>
<div class="list-body">
<span class="message-author"> Buster Wigton </span>
<span class="message-time">12:28 AM</span>
<div class="clearfix"></div>
<span class="message-content">Lorem ipsum dolor sit amet, consectetur adipiscing</span>
</div>
</div>
</a>
</li>
<li>
<a href="#">
<div class="list-item">
<div class="list-left">
<span class="avatar">R</span>
</div>
<div class="list-body">
<span class="message-author"> Rolland Webber </span>
<span class="message-time">12:28 AM</span>
<div class="clearfix"></div>
<span class="message-content">Lorem ipsum dolor sit amet, consectetur adipiscing</span>
</div>
</div>
</a>
</li>
<li>
<a href="#">
<div class="list-item">
<div class="list-left">
<span class="avatar">C</span>
</div>
<div class="list-body">
<span class="message-author"> Claire Mapes </span>
<span class="message-time">12:28 AM</span>
<div class="clearfix"></div>
<span class="message-content">Lorem ipsum dolor sit amet, consectetur adipiscing</span>
</div>
</div>
</a>
</li>
<li>
<a href="#">
<div class="list-item">
<div class="list-left">
<span class="avatar">M</span>
</div>
<div class="list-body">
<span class="message-author">Melita Faucher</span>
<span class="message-time">12:28 AM</span>
<div class="clearfix"></div>
<span class="message-content">Lorem ipsum dolor sit amet, consectetur adipiscing</span>
</div>
</div>
</a>
</li>
<li>
<a href="#">
<div class="list-item">
<div class="list-left">
<span class="avatar">J</span>
</div>
<div class="list-body">
<span class="message-author">Jeffery Lalor</span>
<span class="message-time">12:28 AM</span>
<div class="clearfix"></div>
<span class="message-content">Lorem ipsum dolor sit amet, consectetur adipiscing</span>
</div>
</div>
</a>
</li>
<li>
<a href="#">
<div class="list-item">
<div class="list-left">
<span class="avatar">L</span>
</div>
<div class="list-body">
<span class="message-author">Loren Gatlin</span>
<span class="message-time">12:28 AM</span>
<div class="clearfix"></div>
<span class="message-content">Lorem ipsum dolor sit amet, consectetur adipiscing</span>
</div>
</div>
</a>
</li>
<li>
<a href="#">
<div class="list-item">
<div class="list-left">
<span class="avatar">T</span>
</div>
<div class="list-body">
<span class="message-author">Tarah Shropshire</span>
<span class="message-time">12:28 AM</span>
<div class="clearfix"></div>
<span class="message-content">Lorem ipsum dolor sit amet, consectetur adipiscing</span>
</div>
</div>
</a>
</li>
</ul>
</div>
<div class="topnav-dropdown-footer">
<a href="#">See all messages</a>
</div>
</div>
</div>
</div>
</div>

</div>


<script src="assets/js/jquery-3.6.0.min.js"></script>

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

