<?php


session_start();

include 'dbconnection.php';

$user = $_SESSION['lastname'];
 
$userl = $_SESSION['lastname'];
$userf = $_SESSION['firstname'];

$adminctr = "SELECT * FROM admission";
$adminctr_run = $conn->query($adminctr);
$adminctr_exe = $adminctr_run->rowCount(); 

$enrollctr = "SELECT * FROM enroll";
$enrollctr_run = $conn->query($enrollctr);
$enrollctr_exe = $enrollctr_run->rowCount();

$total = $adminctr_exe+$enrollctr_exe;

// Count male and female students
$genderCounts = [
  'Male' => 0,
  'Female' => 0
];

$genderQuery = "SELECT gender, COUNT(*) as count FROM enroll GROUP BY gender";
$genderResult = $conn->query($genderQuery);

if ($genderResult) {
  while ($row = $genderResult->fetch(PDO::FETCH_ASSOC)) {
      if (array_key_exists($row['gender'], $genderCounts)) {
          $genderCounts[$row['gender']] = $row['count'];
      }
  }
}
$genderGradeQuery = "
    SELECT grade, gender, COUNT(*) as count 
    FROM enroll 
    WHERE grade IN ('Pre-school', 'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6', 'Grade 7', 'Grade 8', 'Grade 9', 'Grade 10') 
    GROUP BY grade, gender
";

$genderGradeResult = $conn->query($genderGradeQuery);

$genderGradeCounts = [];
while ($row = $genderGradeResult->fetch(PDO::FETCH_ASSOC)) {
    $genderGradeCounts[$row['grade']][$row['gender']] = $row['count'];
}

$grades = ['Pre-school', 'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6', 'Grade 7', 'Grade 8', 'Grade 9', 'Grade 10'];
$maleCounts = [];
$femaleCounts = [];

foreach ($grades as $grade) {
    $maleCounts[] = isset($genderGradeCounts[$grade]['Male']) ? $genderGradeCounts[$grade]['Male'] : 0;
    $femaleCounts[] = isset($genderGradeCounts[$grade]['Female']) ? $genderGradeCounts[$grade]['Female'] : 0;
}
// SQL Query to count the number of students per grade level
$gradeQuery = "SELECT grade, COUNT(*) as count FROM enroll WHERE grade IN ('Pre-school', 'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6') GROUP BY grade";
$gradeResult = $conn->query($gradeQuery);

$gradeCounts = [
    'Pre-school' => 0,
    'Grade 1' => 0,
    'Grade 2' => 0,
    'Grade 3' => 0,
    'Grade 4' => 0,
    'Grade 5' => 0,
    'Grade 6' => 0
];

// Process the data and store the counts
if ($gradeResult) {
    while ($row = $gradeResult->fetch(PDO::FETCH_ASSOC)) {
        if (array_key_exists($row['grade'], $gradeCounts)) {
            $gradeCounts[$row['grade']] = $row['count'];
        }
    }
}


// Fetch grade counts for grades 7-10
$grade7to10Query = "SELECT grade, COUNT(*) as count FROM enroll WHERE grade IN ('Grade 7', 'Grade 8', 'Grade 9', 'Grade 10') GROUP BY grade";
$grade7to10Result = $conn->query($grade7to10Query);

$grade7to10Counts = [
    'Grade 7' => 0,
    'Grade 8' => 0,
    'Grade 9' => 0,
    'Grade 10' => 0
];

// Process the data and store the counts
if ($grade7to10Result) {
    while ($row = $grade7to10Result->fetch(PDO::FETCH_ASSOC)) {
        if (array_key_exists($row['grade'], $grade7to10Counts)) {
            $grade7to10Counts[$row['grade']] = $row['count'];
        }
    }
}

$spedQuery = "
        SELECT COUNT(*) AS sped_count
        FROM enroll
        WHERE specialED != 'No'
";

$statement = $conn->prepare($spedQuery);
$statement->execute();

$row3 = $statement->fetch(PDO::FETCH_ASSOC);
$totalSPED = $row3['sped_count'];


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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
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
<a class="dropdown-item" href="changeppass.php?lastname=<?php echo $userl; ?>&firstname=<?php echo $userf; ?>">Change Password</a>
<a class="dropdown-item" href="logout.php" onclick="return confirm('Are you sure you want to logout?');">Logout</a>
</div>
</li>
</ul>
<div class="dropdown mobile-user-menu float-right"> 
<a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></a>
<div class="dropdown-menu dropdown-menu-right">
<a class="dropdown-item" href="#"> </a>
<a class="dropdown-item" href="#"> </a>
<a class="dropdown-item" href="changeppass.php?lastname=<?php echo $userl; ?>&firstname=<?php echo $userf; ?>">Change Password</a>
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
<span class="text-uppercase ms-2 mt-5">Principal</span>
</a>
</div>
<ul class="sidebar-ul">
<li class="menu-title"></li>
<li class="active">
<a href=""><img src="assets/img/sidebar/icon-1.png" alt="icon"><span>Dashboard</span></a>
</li>
<li class="submenu">
  <a href="#"><img src="assets/img/sidebar/icon-3.png" alt="icon"><span> Registrar </span> <span class="menu-arrow"></span></a>
    <ul class="list-unstyled" style="display: none;">
    <li><a href="p_list.php"><span>Admission Info</span></a></li>
    <li><a href="p_rstudent.php"><span>Students</span></a></li>
    <li><a href="p_account.php"><span>Accounts</span></a></li>
    <li><a href="p_event.php"><span></span>Events</a></li>
  </ul>
</li>

<li class="submenu">
  <a href="#"><img src="assets/img/sidebar/icon-3.png" alt="icon"><span> Accounting </span> <span class="menu-arrow"></span></a>
  <ul class="list-unstyled" style="display: none;">
    <!-- Payments dropdown inside Accounting -->
    <li class="submenu">
      <a href="javascript:void(0);"><img src="assets/img/sidebar/icon-11.png" alt="icon"><span> Payments </span> <span class="menu-arrow"></span></a>
      <ul class="list-unstyled" style="display: none;">
        <li><a href="p_enrolled_payments.php"><span>History</span></a></li>
        <li><a href="registrar_dues.php"><span>Dues</span></a></li>   
      </ul>
    </li>
  </ul>
</li>

<!-- <li class="">
  <a href="pt_payment.php"><img src="assets/img/sidebar/icon-11.png" alt="icon"> <span> Payments</span> <span class=""></span></a>
</li> -->

<!-- <li>
<a href="inbox.html"><img src="assets/img/sidebar/icon-6.png" alt="icon"> <span>--</span></a>
</li>
<li>
<a href="inbox.html"><img src="assets/img/sidebar/icon-7.png" alt="icon"> <span>--</span></a>
</li>
<li>
<a href="inbox.html"><img src="assets/img/sidebar/icon-8.png" alt="icon"> <span>--</span></a>
</li>
<li>
<a href="inbox.html"><img src="assets/img/sidebar/icon-9.png" alt="icon"><span> --</span></a>
</li>

<li class="submenu">
<a href="#"><img src="assets/img/sidebar/icon-12.png" alt="icon"> <span> Blog</span> <span class="menu-arrow"></span></a>
<ul class="list-unstyled" style="display: none;">
<li><a href="inbox.html"><span>Blog</span></a></li>
<li><a href="inbox.html"><span>Blog View</span></a></li>
<li><a href="inbox.html"><span>Add Blog</span></a></li>
<li><a href="inbox.html"><span>Edit Blog</span></a></li>
</ul>
</li>
<li class="submenu">
<a href="javascript:void(0);" class="noti-dot"><img src="assets/img/sidebar/icon-13.png" alt="icon"> <span>Management </span> <span class="menu-arrow"></span></a>
<ul style="display: none;">
<li class="submenu">
<a href="#"><span> Employees</span> <span class="menu-arrow"></span></a>
<ul class="list-unstyled" style="display: none;">
<li><a href="inbox.html"><span>All Employees</span></a></li>
<li><a href="inbox.html"><span>Holidays</span></a></li>
<li><a href="inbox.html"><span>Leave Requests</span> <span class="badge badge-pill bg-primary float-right">1</span></a></li>
<li><a href="inbox.html"><span>Attendance</span></a></li>
<li><a href="inbox.html"><span>Departments</span></a></li>
<li><a href="inbox.html"><span>Designations</span></a></li>
</ul>
</li>
<li>
<a href="#"><span>Activities</span></a>
</li>
<li>
<a href="inbox.html"><span>Users</span></a>
</li>
<li class="submenu">
<a href="#"><span> Reports </span> <span class="menu-arrow"></span></a>
<ul class="list-unstyled" style="display: none;">
<li><a href="inbox.html"> <span>Expense Report </span></a></li>
<li><a href="inbox.html"> <span>Invoice Report</span> </a></li>
</ul>
</li>
</ul>
</li>
<li>
<a href="inbox.html"><img src="assets/img/sidebar/icon-14.png" alt="icon"> <span>Change Password</span></a>
</li>
<li class="menu-title">UI Elements</li>
<li class="submenu">
<a href="#"><img src="assets/img/sidebar/icon-15.png" alt="icon"> <span> Components</span> <span class="menu-arrow"></span></a>
<ul class="list-unstyled" style="display: none;">
<li><a href="inbox.html"><span>UI Kit</span></a></li>
<li><a href="inbox.html"><span>Typography</span></a></li>
<li><a href="inbox.html"><span>Tabs</span></a></li>
</ul>
</li>
<li class="submenu">
<a href="#"><img src="assets/img/sidebar/icon-26.png" alt="icon"> <span> Elements</span> <span class="menu-arrow"></span></a>
<ul class="list-unstyled" style="display: none;">
<li><a href="inbox.html">Sweet Alerts</a></li>
<li><a href="inbox.html">Tooltip</a></li>
<li><a href="inbox.html">Popover</a></li>
<li><a href="inbox.html">Ribbon</a></li>
<li><a href="inbox.html">Clipboard</a></li>
<li><a href="inbox.html">Drag & Drop</a></li>
<li><a href="inbox.html">Range Slider</a></li>
<li><a href="inbox.html">Rating</a></li>
<li><a href="inbox.html">Toastr</a></li>
<li><a href="inbox.html">Text Editor</a></li>
<li><a href="inbox.html">Counter</a></li>
<li><a href="inbox.html">Scrollbar</a></li>
<li><a href="inbox.html">Spinner</a></li>
<li><a href="inbox.html">Notification</a></li>
<li><a href="inbox.html">Lightbox</a></li>
<li><a href="inbox.html">Sticky Note</a></li>
<li><a href="inbox.html">Timeline</a></li>
<li><a href="inbox.html">Horizontal Timeline</a></li>
<li><a href="inbox.html">Form Wizard</a></li>
</ul>
</li>
<li class="submenu">
<a href="#"><img src="assets/img/sidebar/icon-27.png" alt="icon"> <span> Chart</span> <span class="menu-arrow"></span></a>
<ul class="list-unstyled" style="display: none;">
<li><a href="inbox.html">Apex Charts</a></li>
<li><a href="inbox.html">Chart Js</a></li>
<li><a href="inbox.html">Morris Charts</a></li>
<li><a href="inbox.html">Flot Charts</a></li>
<li><a href="inbox.html">Peity Charts</a></li>
<li><a href="inbox.html">C3 Charts</a></li>
</ul>
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
<div class="col-md-6">
<h3 class="page-title mb-0">Dashboard</h3>
</div>
<div class="col-md-6">
<ul class="breadcrumb mb-0 p-0 float-right">
<li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Home</a></li>
<li class="breadcrumb-item"><span>Dashboard</span></li>
</ul>
</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<div class="col-md-12 col-sm-12 col-lg-12 col-xl-12 mb-2">
  <button id="download-pdf" class="btn btn-secondary">Download Dashboard</button>
  </div>
<div class="row print_dashboard">

    <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">

        <div class="dash-widget dash-widget5">
            <span class="float-left"><img src="assets/img/dash/dash-1.png" alt="" width="80"></span>
            <div class="dash-widget-info text-right">
                <span>Admissions</span>
                <h3><?php echo $adminctr_exe ?></h3>
            </div>
        </div>
    </div> 
    <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
        <div class="dash-widget dash-widget5">
            <div class="dash-widget-info text-left d-inline-block">
                <span>Enrolled</span>
                <h3><?php echo $enrollctr_exe ?></h3>
            </div>
            <span class="float-right"><img src="assets/img/dash/dash-2.png" width="80" alt=""></span>
        </div>
    </div>
    <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
        <div class="dash-widget dash-widget5">
            <span class="float-left"><img src="assets/img/dash/dash-3.png" alt="" width="80"></span>
            <div class="dash-widget-info text-right">
                <span>Total Sped</span>
                <h3><?php echo $totalSPED ?></h3>
            </div>
        </div>
    </div>

    


<div class="col-md-6 col-sm-6 col-lg-6 col-xl-6">
<canvas id="pieChart" width="400" height="400"></canvas>

</div>
<div class="col-md-6 col-sm-6 col-lg-6 col-xl-6">
    <canvas id="genderGradeChart" width="400" height="400"></canvas>
</div>
<div class="col-md-6 col-sm-6 col-lg-6 col-xl-6">
    <canvas id="gradeChart" width="400" height="400"></canvas>
</div>
<div class="col-md-6 col-sm-6 col-lg-6 col-xl-6">
    <canvas id="grade7to10Chart" width="400" height="400"></canvas>
</div>

</div>

<!------------------------------------CHART HERE------------------------------------------------->
<script>
    var ctx = document.getElementById('pieChart').getContext('2d');

    // Define data for the bar chart with PHP variables
    var data = {
        labels: ['Admissions', 'Enrolled'],
        datasets: [
            {
                label: 'Admissions', // Label for Admissions
                data: [<?php echo json_encode($adminctr_exe) ?>, 0], // Admissions data
                backgroundColor: '#7BD3EA', // Blue color for Admissions
                borderColor: '#36a2eb',
                borderWidth: 1
            },
            {
                label: 'Enrolled', // Label for Enrolled
                data: [0, <?php echo json_encode($enrollctr_exe) ?>], // Enrolled data
                backgroundColor: '#A1EEBD', // Yellow color for Enrolled
                borderColor: '#ffcd56',
                borderWidth: 1
            }
        ]
    };

    var options = {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        },
        plugins: {
            legend: {
                position: 'top',
            },
            title: {
                display: true,
                text: 'Admissions vs Enrolled'
            }
        }
    };

    var barChart = new Chart(ctx, {
        type: 'bar',
        data: data,
        options: options
    });
    
</script>

<script>
  var ctxGenderGrade = document.getElementById('genderGradeChart').getContext('2d');

// Define data for the gender per grade level chart
var genderGradeData = {
    labels: <?php echo json_encode($grades); ?>,
    datasets: [
        {
            label: 'Male',
            data: <?php echo json_encode($maleCounts); ?>,
            backgroundColor: '#36a2eb',
        },
        {
            label: 'Female',
            data: <?php echo json_encode($femaleCounts); ?>,
            backgroundColor: '#FF4E88',
        }
    ]
};

var genderGradeOptions = {
    responsive: true,
    maintainAspectRatio: false,
    scales: {
        y: {
          beginAtZero: true,  // Ensures the y-axis starts from 0
            min: 0,             // Explicitly sets the minimum value of y-axis to 0
            stepSize: 5,       // Sets the y-axis to increment by 10
            ticks: {
                callback: function(value) {
                    return Number.isInteger(value) ? value : '';  // Only display whole numbers
                }
        }
      }
    },
    plugins: {
        legend: {
            position: 'top',
        },
        title: {
            display: true,
            text: 'Gender Distribution per Grade Level'
        }
    }
};

var genderGradeChart = new Chart(ctxGenderGrade, {
    type: 'bar',
    data: genderGradeData,
    options: genderGradeOptions
});
</script>

<script>
    var ctxGrade = document.getElementById('gradeChart').getContext('2d');

    // Define data for the pie chart
    var gradeData = {
        labels: ['Pre-school', 'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6'],
        datasets: [{
            data: [
                <?php echo json_encode($gradeCounts['Pre-school']); ?>,
                <?php echo json_encode($gradeCounts['Grade 1']); ?>,
                <?php echo json_encode($gradeCounts['Grade 2']); ?>,
                <?php echo json_encode($gradeCounts['Grade 3']); ?>,
                <?php echo json_encode($gradeCounts['Grade 4']); ?>,
                <?php echo json_encode($gradeCounts['Grade 5']); ?>,
                <?php echo json_encode($gradeCounts['Grade 6']); ?>
            ],
            backgroundColor: [
                '#FFB6C1',  // Pre-school (light pink)
                '#FF6347',  // Grade 1 (tomato)
                '#98FB98',  // Grade 2 (pale green)
                '#7B68EE',  // Grade 3 (medium slate blue)
                '#FFD700',  // Grade 4 (gold)
                '#FF69B4',  // Grade 5 (hot pink)
                '#00BFFF'   // Grade 6 (deep sky blue)
            ],
            borderColor: '#fff',  // White border around slices
            borderWidth: 1
        }]
    };

    // Define options for the pie chart
    var gradeOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
            },
            title: {
                display: true,
                text: 'Grade Distribution (NKP-6)'
            }
        }
    };

    // Create the pie chart
    var gradeChart = new Chart(ctxGrade, {
        type: 'pie',
        data: gradeData,
        options: gradeOptions
    });
</script>

<script>
    var ctxGrade7to10 = document.getElementById('grade7to10Chart').getContext('2d');

    // Define data for the pie chart (Grades 7 to 10)
    var grade7to10Data = {
        labels: ['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10'],
        datasets: [{
            data: [
                <?php echo json_encode($grade7to10Counts['Grade 7']); ?>,
                <?php echo json_encode($grade7to10Counts['Grade 8']); ?>,
                <?php echo json_encode($grade7to10Counts['Grade 9']); ?>,
                <?php echo json_encode($grade7to10Counts['Grade 10']); ?>
            ],
            backgroundColor: [
                '#FF6347',  // Grade 7 (tomato red)
                '#FFD700',  // Grade 8 (gold)
                '#98FB98',  // Grade 9 (pale green)
                '#7B68EE'   // Grade 10 (medium slate blue)
            ],
            borderColor: '#fff',  // White border around slices
            borderWidth: 1
        }]
    };

    // Define options for the pie chart
    var grade7to10Options = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
            },
            title: {
                display: true,
                text: 'Grade Distribution (7-10)'
            }
        }
    };

    // Create the pie chart
    var grade7to10Chart = new Chart(ctxGrade7to10, {
        type: 'pie',
        data: grade7to10Data,
        options: grade7to10Options
    });
</script>


<script>
document.getElementById('download-pdf').addEventListener('click', function() {
    // Make sure to use the correct reference to jsPDF
    const { jsPDF } = window.jspdf;

    html2canvas(document.querySelector('.print_dashboard')).then(canvas => {
        const imgData = canvas.toDataURL('image/png');
        const pdf = new jsPDF();
        const imgWidth = 190; // Width of the image in PDF
        const pageHeight = pdf.internal.pageSize.height;
        const imgHeight = (canvas.height * imgWidth) / canvas.width;
        let heightLeft = imgHeight;

        let position = 0;

        pdf.addImage(imgData, 'PNG', 10, position, imgWidth, imgHeight);
        heightLeft -= pageHeight;

        while (heightLeft >= 0) {
            position = heightLeft - imgHeight;
            pdf.addPage();
            pdf.addImage(imgData, 'PNG', 10, position, imgWidth, imgHeight);
            heightLeft -= pageHeight;
        }

        pdf.save('dashboard.pdf');
    }).catch(function(error) {
        console.error('Error generating PDF:', error);
    });
});
</script>
<!-- <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
<div class="dash-widget dash-widget5">
<div class="dash-widget-info d-inline-block text-left">
<span>Total Earnings</span>
<h3>$20,000</h3>
</div>
<span class="float-right"><img src="assets/img/dash/dash-4.png" alt="" width="80"></span>
</div>
</div>
</div> -->
<!-- <div class="row">
<div class="col-lg-6 d-flex">
<div class="card flex-fill">
<div class="card-header">
<div class="row align-items-center">
<div class="col-auto">
<div class="page-title">
Students Survay
</div>
</div>
<div class="col text-right">
<div class=" mt-sm-0 mt-2">
<button class="btn btn-light" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-h"></i></button>
 <div class="dropdown-menu dropdown-menu-right">
<a class="dropdown-item" href="#">Action</a>
<div role="separator" class="dropdown-divider"></div>
<a class="dropdown-item" href="#">Another action</a>
<div role="separator" class="dropdown-divider"></div>
<a class="dropdown-item" href="#">Something else here</a>
</div>
</div>
</div>
</div>
</div>
<div class="card-body">
<div id="chart1"></div>
</div>
</div>
</div>
<div class="col-lg-6 d-flex">
<div class="card flex-fill">
<div class="card-header">
<div class="row align-items-center">
<div class="col-auto">
<div class="page-title">
Student Performance
</div>
</div>
<div class="col text-right">
<div class=" mt-sm-0 mt-2">
<button class="btn btn-light" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-h"></i></button>
<div class="dropdown-menu dropdown-menu-right">
<a class="dropdown-item" href="#">Action</a>
<div role="separator" class="dropdown-divider"></div>
<a class="dropdown-item" href="#">Another action</a>
<div role="separator" class="dropdown-divider"></div>
<a class="dropdown-item" href="#">Something else here</a>
</div>
</div>
</div>
</div>
</div>
<div class="card-body">
<div id="chart2"></div>
</div>
</div>
</div>
</div>
<div class="row">
<div class="col-lg-6 col-md-12 col-12 d-flex">
<div class="card flex-fill">
<div class="card-header">
<div class="row align-items-center">
<div class="col-auto">
<div class="page-title">
Events
</div>
</div>
<div class="col text-right">
<div class=" mt-sm-0 mt-2">
<button class="btn btn-light" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-h"></i></button>
<div class="dropdown-menu dropdown-menu-right">
<a class="dropdown-item" href="#">Action</a>
<div role="separator" class="dropdown-divider"></div>
<a class="dropdown-item" href="#">Another action</a>
<div role="separator" class="dropdown-divider"></div>
 <a class="dropdown-item" href="#">Something else here</a>
</div>
</div>
</div>
</div>
</div>
<div class="card-body">
<div id="calendar" class=" overflow-hidden"></div>
</div>
</div>
</div>
<div class="col-lg-6 col-md-12 col-12 d-flex">
<div class="card flex-fill">
<div class="card-header">
<div class="row align-items-center">
<div class="col-auto">
<div class="page-title">
Total Member
</div>
</div>
<div class="col text-right">
<div class=" mt-sm-0 mt-2">
<button class="btn btn-light" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-h"></i></button>
<div class="dropdown-menu dropdown-menu-right">
<a class="dropdown-item" href="#">Action</a>
<div role="separator" class="dropdown-divider"></div>
<a class="dropdown-item" href="#">Another action</a>
<div role="separator" class="dropdown-divider"></div>
<a class="dropdown-item" href="#">Something else here</a>
</div>
</div>
</div>
</div>
</div>
<div class="card-body d-flex align-items-center justify-content-center overflow-hidden">
<div id="chart3"> </div>
</div>
</div>
</div>
</div>
<div class="row">
<div class="col-lg-6 col-md-12 col-12 d-flex">
<div class="card flex-fill">
<div class="card-header">
<div class="row align-items-center">
<div class="col-auto">
<div class="page-title">
Income Monthwise
</div>
</div>
<div class="col text-right">
<div class=" mt-sm-0 mt-2">
<button class="btn btn-light" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-h"></i></button>
<div class="dropdown-menu dropdown-menu-right">
<a class="dropdown-item" href="#">Action</a>
<div role="separator" class="dropdown-divider"></div>
<a class="dropdown-item" href="#">Another action</a>
<div role="separator" class="dropdown-divider"></div>
<a class="dropdown-item" href="#">Something else here</a>
</div>
</div>
</div>
</div>
</div>
<div class="card-body">
<div id="chart4"></div>
</div>
</div>
</div>
<div class="col-lg-6 d-flex">
<div class="card flex-fill">
<div class="card-header">
<div class="row align-items-center">
<div class="col-auto">
<div class="page-title">
Exam List
</div>
</div>
<div class="col text-right">
<div class=" mt-sm-0 mt-2">
<button class="btn btn-light" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-h"></i></button>
<div class="dropdown-menu dropdown-menu-right">
<a class="dropdown-item" href="#">Action</a>
<div role="separator" class="dropdown-divider"></div>
<a class="dropdown-item" href="#">Another action</a>
<div role="separator" class="dropdown-divider"></div>
<a class="dropdown-item" href="#">Something else here</a>
</div>
</div>
</div>
</div>
</div>
<div class="card-body">
<div class="row">
<div class="col-md-12">
<div class="table-responsive">
<table class="table custom-table">
<thead class="thead-light">
<tr>
<th>Exam Name </th>
<th>Subject</th>
<th>Class</th>
<th>Section</th>
<th>Time</th>
<th>Date</th>
<th class="text-right">Action</th>
</tr>
</thead>
<tbody>
<tr>
<td>
<a href="#" class="avatar bg-green">C</a>
</td>
<td>English</td>
<td>5</td>
<td>B</td>
<td>10.00am</td>
<td>20/1/2019</td>
<td class="text-right">
<a href="#" class="btn btn-primary btn-sm mb-1">
<i class="far fa-edit"></i>
</a>
<button type="submit" data-toggle="modal" data-target="#delete_employee" class="btn btn-danger btn-sm mb-1">
<i class="far fa-trash-alt"></i>
</button>
</td>
</tr>
<tr>
<td>
<a href="#" class="avatar bg-purple">C</a>
</td>
<td>English</td>
<td>4</td>
<td>A</td>
<td>10.00am</td>
<td>2/1/2019</td>
<td class="text-right">
<a href="#" class="btn btn-primary btn-sm mb-1">
<i class="far fa-edit"></i>
</a>
<button type="submit" data-toggle="modal" data-target="#delete_employee" class="btn btn-danger btn-sm mb-1">
<i class="far fa-trash-alt"></i>
</button>
</td>
</tr>
<tr>
<td>
<a href="#" class="avatar bg-green">C</a>
</td>
<td>Maths</td>
<td>6</td>
<td>B</td>
<td>10.00am</td>
<td>2/1/2019</td>
<td class="text-right">
<a href="#" class="btn btn-primary btn-sm mb-1">
<i class="far fa-edit"></i>
</a>
<button type="submit" data-toggle="modal" data-target="#delete_employee" class="btn btn-danger btn-sm mb-1">
<i class="far fa-trash-alt"></i>
</button>
</td>
</tr>
<tr>
<td>
<a href="#" class="avatar bg-dark">C</a>
</td>
<td>Science</td>
<td>3</td>
<td>B</td>
<td>10.00am</td>
<td>20/1/2019</td>
<td class="text-right">
<a href="#" class="btn btn-primary btn-sm mb-1">
<i class="far fa-edit"></i>
</a>
<button type="submit" data-toggle="modal" data-target="#delete_employee" class="btn btn-danger btn-sm mb-1">
<i class="far fa-trash-alt"></i>
</button>
</td>
</tr>
<tr>
<td>
<a href="#" class="avatar bg-green">C</a>
</td>
<td>Maths</td>
<td>6</td>
<td>B</td>
<td>10.00am</td>
<td>20/1/2019</td>
<td class="text-right">
<a href="#" class="btn btn-primary btn-sm mb-1">
<i class="far fa-edit"></i>
</a>
<button type="submit" data-toggle="modal" data-target="#delete_employee" class="btn btn-danger btn-sm mb-1">
<i class="far fa-trash-alt"></i>
</button>
</td>
</tr>
<tr>
<td>
<a href="#" class="avatar bg-dark">C</a>
</td>
<td>English</td>
<td>7</td>
<td>B</td>
<td>10.00am</td>
<td>20/1/2019</td>
<td class="text-right">
<a href="#" class="btn btn-primary btn-sm mb-1">
<i class="far fa-edit"></i>
</a>
<button type="submit" data-toggle="modal" data-target="#delete_employee" class="btn btn-danger btn-sm mb-1">
<i class="far fa-trash-alt"></i>
</button>
</td>
</tr>
<tr>
<td>
<a href="#" class="avatar bg-purple">C</a>
</td>
<td>Science</td>
<td>5</td>
<td>B</td>
<td>10.00am</td>
<td>20/1/2019</td>
<td class="text-right">
<a href="#" class="btn btn-primary btn-sm mb-1">
<i class="far fa-edit"></i>
</a>
<button type="submit" data-toggle="modal" data-target="#delete_employee" class="btn btn-danger btn-sm mb-1">
<i class="far fa-trash-alt"></i>
</button>
</td>
</tr>
</tbody>
</table>
</div>
</div>
</div>  
</div>
</div>
</div>
</div> -->


<!----------------------- Admission Enrolled and Archive Table Here ------------------------>


<!-- <div class="row">
<div class="col-lg-12">
<div class="card">
<div class="card-header">
<div class="row align-items-center">
<div class="col-sm-6">
 <div class="page-title">
Admissions
</div>
</div>
<div class="col-sm-6 text-sm-right">
<div class=" mt-sm-0 mt-2">
<a href="export_admission.php"><button class="btn btn-outline-primary mr-2"><img src="assets/img/excel.png" alt=""><span class="ml-2">Excel</span></button></a>
<button class="btn btn-outline-danger mr-2"><img src="assets/img/pdf.png" alt="" height="18"><span class="ml-2">PDF</span></button>
<button class="btn btn-light" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-h"></i></button>
<div class="dropdown-menu dropdown-menu-right">
<a class="dropdown-item" href="#">Action</a>
<div role="separator" class="dropdown-divider"></div>
<a class="dropdown-item" href="#">Another action</a>
<div role="separator" class="dropdown-divider"></div>
<a class="dropdown-item" href="#">Something else here</a>
</div>
</div>
</div>
</div>
</div>
<div class="card-body">
<div class="table-responsive">
<table class="table custom-table">
<thead class="thead-light">
<tr>
<th>First Name</th>
<th>Last Name</th>
<th>Birthday</th>
<th>Gender</th>
<th>Email</th>
<th>Phone Number</th>
<th>Grade</th>
<th class="text-right">Action</th>
</tr>
</thead> -->
<!------------------------------View of Admission--------------------------------------------------------------->
<!-- <tbody>
  <?php

    $query= "SELECT * FROM admission";
    $statement = $conn->prepare($query);
    $statement->execute();

    $result = $statement->fetchAll();
    if($result){
      foreach($result as $row)
      {
        ?>

          <tr>
            <td><?= $row['fname']; ?></td>
            <td><?= $row['lname']; ?></td>
            <td><?= $row['birthday']; ?></td>
            <td><?= $row['gender']; ?></td>
            <td><?= $row['email']; ?></td>
            <td><?= $row['phonenumber']; ?></td>
            <td><?= $row['grade']; ?></td>
            <td>
              <form action="process.php" method="POST">
              <button class="btn text-success" type="submit" onclick="return confirm('Are you sure you want to confirm this?');" name="save_enroll" value="<?=  $row['id'];  ?>"><i class="bi bi-check-square-fill"></i></button>
              </form>
            </td>
           
            
          <tr>

        <?php
      }
    }
    else{
      ?>

        <tr>

          <td colspan="7">No Record Found</td>

        </tr>

      <?php
    }

  ?>
</tbody>
</table>
</div>
</div>
</div>
</div>
</div>



<div class="row">
<div class="col-12">
<div class="card">
<div class="card-header">
<div class="row align-items-center">
<div class="col-sm-6">
<div class="page-title">
Enrolled
</div>
</div>
<div class="col-sm-6 text-sm-right">
<div class=" mt-sm-0 mt-2">
<a href="export_enroll.php"><button class="btn btn-outline-primary mr-2" name="excel"><img src="assets/img/excel.png" alt=""><span class="ml-2">Excel</span></button></a>
<button class="btn btn-outline-danger mr-2"><img src="assets/img/pdf.png" alt="" height="18"><span class="ml-2">PDF</span></button>
<button class="btn btn-light" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-h"></i></button>
<div class="dropdown-menu dropdown-menu-right">
<a class="dropdown-item" href="#">Action</a>
<div role="separator" class="dropdown-divider"></div>
<a class="dropdown-item" href="#">Another action</a>
<div role="separator" class="dropdown-divider"></div>
<a class="dropdown-item" href="#">Something else here</a>
</div>
</div>
</div>
</div>
</div>
<div class="card-body">
<div class="row">
<div class="col-lg-12 col-md-12 col-sm-12 col-12">
<div class="table-responsive">
<table class="table custom-table"> -->

  <!-----------------View of Enrollees--------------------->

<!-- <thead class="thead-light">
<tr>
<th>First Name</th>
<th>Last Name</th>
<th>Birthday</th>
<th>Gender</th>
<th>Email</th>
<th>Phone Number</th>
<th>Grade</th>
<th class="text-right">Action</th>
</tr>
</thead>
<tbody>
  <?php

    $query2 = "SELECT * FROM enroll";
    $statement2 = $conn->prepare($query2);
    $statement2->execute();

    $result2 = $statement2->fetchAll();
    if($result2)
    {
      foreach($result2 as $row2)
      {
        ?>

          <tr>
            <td><?= $row2['fname']; ?></td>
            <td><?= $row2['lname']; ?></td>
            <td><?= $row2['birthday']; ?></td>
            <td><?= $row2['gender']; ?></td>
            <td><?= $row2['email']; ?></td>
            <td><?= $row2['phonenumber']; ?></td>
            <td><?= $row2['grade']; ?></td>
            <td>
              <a href="enroll_update.php?id=<?= $row2['id'] ?>"  class="btn btn-primary btn-sm mb-1">
              <i class="far fa-edit"></i>
              </a>
            <form action="process.php" method="POST">
              <button type="submit" class="btn btn-danger btn-sm mb-1" onclick="return confirm('Are you sure you want to archive this data?');" name="archive" value="<?= $row2['id'] ?>">
              <i class="far fa-trash-alt"></i>
              </button>
            </form>
            </td>
           
            
          <tr>

        <?php
      }
    }
    else{
      ?>

        <tr>

          <td colspan="7">No Record Found</td>

        </tr>

      <?php
    }
  
  ?>

</tbody>
</table>
</div>
</div>
</div>
</div>
</div>
</div>
</div> -->

<!------------- ARCHIVES --------------------------------------->

<!-- <div class="row">
<div class="col-12">
<div class="card">
<div class="card-header">
<div class="row align-items-center">
<div class="col-sm-6">
<div class="page-title">
Archives
</div>
</div>
<div class="col-sm-6 text-sm-right">
<div class=" mt-sm-0 mt-2">
<a href="export_archive.php"><button class="btn btn-outline-primary mr-2" name="excel"><img src="assets/img/excel.png" alt=""><span class="ml-2">Excel</span></button></a>
<button class="btn btn-outline-danger mr-2"><img src="assets/img/pdf.png" alt="" height="18"><span class="ml-2">PDF</span></button>
<button class="btn btn-light" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-h"></i></button>
<div class="dropdown-menu dropdown-menu-right">
<a class="dropdown-item" href="#">Action</a>
<div role="separator" class="dropdown-divider"></div>
<a class="dropdown-item" href="#">Another action</a>
<div role="separator" class="dropdown-divider"></div>
<a class="dropdown-item" href="#">Something else here</a>
</div>
</div>
</div>
</div>
</div>
<div class="card-body">
<div class="row">
<div class="col-lg-12 col-md-12 col-sm-12 col-12">
<div class="table-responsive">
<table class="table custom-table"> -->

  <!-----------------View of Archive--------------------->

<!-- <thead class="thead-light">
<tr>
<th>First Name</th>
<th>Last Name</th>
<th>Birthday</th>
<th>Gender</th>
<th>Email</th>
<th>Phone Number</th>
<th>Grade</th>
<th class="text-right">Action</th>
</tr>
</thead>
<tbody>
  <?php

    $query3 = "SELECT * FROM archive";
    $statement3 = $conn->prepare($query3);
    $statement3->execute();

    $result3 = $statement3->fetchAll();
    if($result3)
    {
      foreach($result3 as $row3)
      {
        ?>

          <tr>
            <td><?= $row3['fname']; ?></td>
            <td><?= $row3['lname']; ?></td>
            <td><?= $row3['birthday']; ?></td>
            <td><?= $row3['gender']; ?></td>
            <td><?= $row3['email']; ?></td>
            <td><?= $row3['phonenumber']; ?></td>
            <td><?= $row3['Grade']; ?></td>
            <td>
          <form action="process.php" method="POST">
            <button type="submit" class="btn btn-primary btn-sm mb-1" onclick="return confirm('Are you sure you want to recover this data?');" name="return" value="<?= $row3['id'] ?>">
              <i class="far fa-edit"></i>
            </button>
          </form>
            
            <a href="enroll_update.php?id=<?= $row2['id'] ?>"  class="btn btn-danger btn-sm mb-1">
              <i class="far fa-trash-alt"></i>
            </a>
            </td>
           
            
          <tr>

        <?php
      }
    }
    else{
      ?>

        <tr>

          <td colspan="7">No Record Found</td>

        </tr>

      <?php
    }
  
  ?>

</tbody>
</table>
</div>
</div>
</div>
</div>
</div>
</div>
</div> -->


<!----------------------- Admission Enrolled and Archive Table END Here ------------------------>



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