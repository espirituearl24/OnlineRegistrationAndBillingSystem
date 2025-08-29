<?php


session_start();

include 'dbconnection.php';

$userl = $_SESSION['lastname'];
$userf = $_SESSION['firstname'];

// SQL Query to count the number of rows in the admission table
$admissionCountQuery = "SELECT COUNT(*) AS admission_count FROM admission";
$admissionCountResult = $conn->query($admissionCountQuery);

$admissionCount = 0; // Default value
if ($admissionCountResult) {
    $row = $admissionCountResult->fetch(PDO::FETCH_ASSOC);
    $admissionCount = $row['admission_count'] ?? 0; // Get admission count or default to 0
}
 
// SQL Query to sum the total payments
$totalPaymentsQuery = "SELECT SUM(amount) AS total_amount FROM payments";
$totalPaymentsResult = $conn->query($totalPaymentsQuery);

$totalPayments = 0; // Default value
if ($totalPaymentsResult) {
    $row = $totalPaymentsResult->fetch(PDO::FETCH_ASSOC);
    $totalPayments = $row['total_amount'] ?? 0; // Get total amount or default to 0
}

// New query to fetch total payments by month and year
// New query to fetch total payments by month and year
$monthlyPaymentsQuery = "
    SELECT 
        MONTH(created_at) AS month, 
        YEAR(created_at) AS year, 
        SUM(amount) AS total_monthly_amount 
    FROM payments 
    GROUP BY YEAR(created_at), MONTH(created_at) 
    ORDER BY year, month
";
$monthlyPaymentsResult = $conn->query($monthlyPaymentsQuery);

$monthlyPayments = [];
$availableYears = [];

if ($monthlyPaymentsResult) {
    while ($row = $monthlyPaymentsResult->fetch(PDO::FETCH_ASSOC)) {
        $monthlyPayments[$row['year']][$row['month']] = $row['total_monthly_amount'];
        
        // Collect unique years
        if (!in_array($row['year'], $availableYears)) {
            $availableYears[] = $row['year'];
        }
    }
}

// Sort available years in descending order
rsort($availableYears);


// SQL Query to count the number of full payments
$fullPaymentsQuery = "SELECT COUNT(*) AS full_count FROM payments WHERE payment_terms = 'full'";
$fullPaymentsResult = $conn->query($fullPaymentsQuery);

$fullPaymentsCount = 0; // Default value
if ($fullPaymentsResult) {
    $row = $fullPaymentsResult->fetch(PDO::FETCH_ASSOC);
    $fullPaymentsCount = $row['full_count'] ?? 0; // Get full count or default to 0
}

// SQL Query to count the total number of students
$totalStudentsQuery = "SELECT COUNT(*) AS total_students FROM enroll";
$totalStudentsResult = $conn->query($totalStudentsQuery);

$totalStudentsCount = 0; // Default value
if ($totalStudentsResult) {
    $row = $totalStudentsResult->fetch(PDO::FETCH_ASSOC);
    $totalStudentsCount = $row['total_students'] ?? 0; // Get total count or default to 0
}

// SQL Query to count the number of payments for each payment term
$paymentTermsQuery = "SELECT payment_terms, COUNT(*) as count FROM payments GROUP BY payment_terms";
$paymentTermsResult = $conn->query($paymentTermsQuery);

$paymentTermsCounts = [];
if ($paymentTermsResult) {
    while ($row = $paymentTermsResult->fetch(PDO::FETCH_ASSOC)) {
        $paymentTermsCounts[$row['payment_terms']] = $row['count'];
    }
}
// SQL Query to count the number of payments for each payment mode
$paymentModeQuery = "SELECT payment_mode, COUNT(*) as count FROM payments GROUP BY payment_mode";
$paymentModeResult = $conn->query($paymentModeQuery);

$paymentModeCounts = [];
if ($paymentModeResult) {
    while ($row = $paymentModeResult->fetch(PDO::FETCH_ASSOC)) {
        $paymentModeCounts[$row['payment_mode']] = $row['count'];
    }
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
<title>GBA | Accountant</title>
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

  <style>
        .dash-widget { /* Style widgets consistently */
            display: flex;
            align-items: center;
            padding: 10px;
            background-color: #f4f6f9;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .chart-container {
            height: 400px; /* Set a fixed height for charts to align properly */
            width: 100%;
        }
    </style>

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
<a href="registrar.php" class="mobile-logo d-md-block d-lg-none d-block"><img src="img/logo1.png" alt="" width="30" height="30"></a>
</li>
</ul>

<ul class="nav user-menu float-right">

 <li class="nav-item dropdown has-arrow">
<a href="#" class=" nav-link user-link" data-toggle="dropdown">
<span class=" ">
      
</span>
<span> <?php echo $userl ?> </span> 
</a>
<div class="dropdown-menu">
<a class="dropdown-item" href="#"> </a>
<a class="dropdown-item" href="#"> </a>
<a class="dropdown-item" href="changerpass.php?lastname=<?php echo $userl; ?>&firstname=<?php echo $userf; ?>">Change Password</a>

<a class="dropdown-item" href="../logout.php" onclick="return confirm('Are you sure you want to logout?');">Logout</a>
</div>
</li>
</ul>
<div class="dropdown mobile-user-menu float-right"> 
<a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></a>
<div class="dropdown-menu dropdown-menu-right">
<a class="dropdown-item" href="#"> </a>
<a class="dropdown-item" href="#"> </a>
<a class="dropdown-item" href="changerpass.php?lastname=<?php echo $userl; ?>&firstname=<?php echo $userf; ?>">Change Password</a>

<a class="dropdown-item" href="../index.php" >Logout</a>
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
<span class="text-uppercase ms-2 mt-5">Accountant</span>
</a>
</div>
<ul class="sidebar-ul">
<li class="menu-title"></li> 
<li class="active">
<a href="registrar.php"><img src="assets/img/sidebar/icon-1.png" alt="icon"><span>Dashboard</span></a>
</li> 
<li class="submenu">
  <a href="javascript:void(0);"><img src="assets/img/sidebar/icon-2.png" alt="icon"> <span> Admission</span>&nbsp;<span class="badge rounded-pill text-bg-danger"> <?php echo $admissionCount; ?></span> <span class="menu-arrow"></span></a>
  <ul class="list-unstyled" style="display: none;">
  <li><a href="list_due.php"><span>New Admissions</span>&nbsp;<span class="badge rounded-pill text-bg-danger"><?php echo $admissionCount; ?></span></a></li>

  <li><a href="old_admission_table.php"><span>Readmission</span></a></li>
  </ul>
</li>
<!-- <li class="">
  <a href="enrolled_payments.php"><img src="assets/img/sidebar/icon-10.png" alt="icon"> <span> Students</span> <span class=""></span></a>
</li> -->
<li class="submenu">
  <a href="javascript:void(0);"><img src="assets/img/sidebar/icon-11.png" alt="icon"><span> Payments </span> <span class="menu-arrow"></span></a>
    <ul class="list-unstyled" style="display: none;">
    <li><a  href="otc_registrar.php"><span>Over the Counter</span></a></li>
    <li><a href="enrolled_payments.php"><span>History</span></a></li>
    <li><a href="registrar_dues.php"><span>Dues</span></a></li>
  </ul>
</li>
<li class="submenu">
  <a href="javascript:void(0);"><img src="assets/img/sidebar/icon-3.png" alt="icon"><span> Archive </span> <span class="menu-arrow"></span></a>
    <ul class="list-unstyled" style="display: none;">
    <li><a href="tarchive_admission.php"><span>Admission</span></a></li>
    <li><a href="tarchive_estudent.php"><span>Student</span></a></li>

  </ul>
</li>



<li><a href="events_registrar.php"><img src="assets/img/sidebar/icon-17.png" alt="icon"><span>Events</span></a></li>

<!-- <li class="">
  <a href="email.php"><img src="assets/img/sidebar/icon-4.png" alt="icon"> <span>Email</span> <span class=""></span></a>
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

    <div class="col-md-6 col-sm-6 col-lg-6 col-xl-4">

    <div class="dash-widget dash-widget5">
        
        <span class="float-left"><img src="assets/img/dash/dash-1.png" alt="" width="80"></span>
        <div class="mt-2">
                <select id="monthSelect" class="form-select form-select-sm">
                    <option value="0">All Months</option>
                    <?php 
                    $months = [
                        1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 
                        5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 
                        9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
                    ];
                    foreach ($months as $monthNum => $monthName): ?>
                        <option value="<?php echo $monthNum; ?>"><?php echo $monthName; ?></option>
                    <?php endforeach; ?>
                </select>
                <select id="yearSelect" class="form-select form-select-sm mt-2">
                    <?php 
                    // Assuming $availableYears is populated earlier in the PHP script
                    foreach ($availableYears as $year): ?>
                        <option value="<?php echo $year; ?>"><?php echo $year; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        <div class="dash-widget-info text-right">        

            <span>Total Payments</span>
            <h3 id="totalPaymentsAmount">₱<?php echo number_format($totalPayments, 2); ?></h3>

        </div>
    </div>
    </div> 
    <div class="col-md-6 col-sm-6 col-lg-6 col-xl-4">
        <div class="dash-widget dash-widget5">
            <div class="dash-widget-info text-left d-inline-block">
                <span>Full Paid Tuitions</span>
                <h3><?php echo $fullPaymentsCount; ?></h3> <!-- Display the full payments count -->
                </div>
            <span class="float-right"><img src="assets/img/dash/dash-2.png" width="80" alt=""></span>
        </div>
    </div>
    <div class="col-md-6 col-sm-6 col-lg-6 col-xl-4">
        <div class="dash-widget dash-widget5">
            <span class="float-left"><img src="assets/img/dash/dash-3.png" alt="" width="80"></span>
            <div class="dash-widget-info text-right">
                <span>Total Students</span>
                <h3><?php echo $totalStudentsCount; ?></h3> <!-- Display the total students count -->
                </div>
        </div>
    </div>


    <div class="col-md-6 col-sm-6 col-lg-6 col-xl-6">
    <canvas id="paymentTermsChart" width="400" height="400"></canvas>
</div>
<div class="col-md-6 col-sm-6 col-lg-6 col-xl-6">
    <canvas id="paymentModeChart" width="400" height="400"></canvas>
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
    var ctxPaymentTerms = document.getElementById('paymentTermsChart').getContext('2d');

    // Prepare the data for the payment terms chart
    var paymentTermsCounts = <?php echo json_encode($paymentTermsCounts); ?>;

    // Define the datasets for each payment term
    var paymentTermsData = {
        labels: ['Installment', 'Full', 'Admission Fee'], // Define the payment terms
        datasets: [
            {
                label: 'Installment Payments', // Separate label for Installment
                data: [<?php echo json_encode($paymentTermsCounts['installment'] ?? 0); ?>, 0, 0], // Installment data
                backgroundColor: '#36a2eb', // Blue color for Installment
                borderColor: '#36a2eb',
                borderWidth: 1
            },
            {
                label: 'Full Payments', // Separate label for Full Payments
                data: [0, <?php echo json_encode($paymentTermsCounts['full'] ?? 0); ?>, 0], // Full Payments data
                backgroundColor: '#FF4E88', // Pink color for Full Payments
                borderColor: '#FF4E88',
                borderWidth: 1
            },
            {
                label: 'Admission Fees', // Separate label for Admission Fees
                data: [0, 0, <?php echo json_encode($paymentTermsCounts['admission fee'] ?? 0); ?>], // Admission Fees data
                backgroundColor: '#FFCE56', // Yellow color for Admission Fees
                borderColor: '#FFCE56',
                borderWidth: 1
            }
        ]
    };

    var paymentTermsOptions = {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true, // Ensures the y-axis starts from 0
                min: 0, // Explicitly sets the minimum value of y-axis to 0
                stepSize: 5, // Sets the y-axis to increment by 5
                ticks: {
                    callback: function(value) {
                        return Number.isInteger(value) ? value : ''; // Only display whole numbers
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
                text: 'Payment Terms Distribution'
            }
        }
    };

    var paymentTermsChart = new Chart(ctxPaymentTerms, {
        type: 'bar',
        data: paymentTermsData,
        options: paymentTermsOptions
    });
</script>

<script>
    var ctxPaymentMode = document.getElementById('paymentModeChart').getContext('2d');

    // Prepare the data for the payment mode chart
    var paymentModeCounts = <?php echo json_encode($paymentModeCounts); ?>;

    // Define the datasets for each payment mode
    var paymentModeData = {
        labels: ['GCash', 'Bank', 'OTC'], // Define the payment modes
        datasets: [
            {
                label: 'GCash Payments', // Separate label for GCash
                data: [<?php echo json_encode($paymentModeCounts['gcash'] ?? 0); ?>, 0, 0], // GCash data
                backgroundColor: '#36a2eb', // Blue color for GCash
                borderColor: '#36a2eb',
                borderWidth: 1
            },
            {
                label: 'Bank Payments', // Separate label for Bank
                data: [0, <?php echo json_encode($paymentModeCounts['bank'] ?? 0); ?>, 0], // Bank data
                backgroundColor: '#FF4E88', // Pink color for Bank
                borderColor: '#FF4E88',
                borderWidth: 1
            },
            {
                label: 'OTC Payments', // Separate label for OTC
                data: [0, 0, <?php echo json_encode($paymentModeCounts['OTC'] ?? 0); ?>], // OTC data
                backgroundColor: '#FFCE56', // Yellow color for OTC
                borderColor: '#FFCE56',
                borderWidth: 1
            }
        ]
    };

    var paymentModeOptions = {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true, // Ensures the y-axis starts from 0
                min: 0, // Explicitly sets the minimum value of y-axis to 0
                stepSize: 5, // Sets the y-axis to increment by 5
                ticks: {
                    callback: function(value) {
                        return Number.isInteger(value) ? value : ''; // Only display whole numbers
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
                text: 'Payment Mode Distribution'
            }
        }
    };

    var paymentModeChart = new Chart(ctxPaymentMode, {
        type: 'bar',
        data: paymentModeData,
        options: paymentModeOptions
    });
</script>
<!-- <script>
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
</script> -->


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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php
    if (isset($_SESSION['notification'])) {
        $notification = $_SESSION['notification'];
        echo "
        <script>
        Swal.fire({
            icon: '{$notification['type']}',
            title: '{$notification['message']}',
            showConfirmButton: false,
            timer: 3000
        });
        </script>
        ";
        unset($_SESSION['notification']); // Clear the notification after displaying
    }
    ?>


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

<script>
    // Prepare the monthly payments data
    const monthlyPayments = <?php echo json_encode($monthlyPayments); ?>;

    // Add event listeners to the dropdowns
    document.getElementById('monthSelect').addEventListener('change', updateTotalPayments);
    document.getElementById('yearSelect').addEventListener('change', updateTotalPayments);

    function updateTotalPayments() {
        const selectedYear = document.getElementById('yearSelect').value;
        const selectedMonth = document.getElementById('monthSelect').value;

        let totalAmount = 0;
        
        if (selectedMonth === "0") {
            // If all months are selected, sum up all months for the selected year
            if (monthlyPayments[selectedYear]) {
                Object.values(monthlyPayments[selectedYear]).forEach(amount => {
                    totalAmount += parseFloat(amount);
                });
            }
        } else {
            // If a specific month is selected
            if (monthlyPayments[selectedYear] && monthlyPayments[selectedYear][selectedMonth]) {
                totalAmount = parseFloat(monthlyPayments[selectedYear][selectedMonth]);
            }
        }

        // Update the displayed amount
        document.getElementById('totalPaymentsAmount').textContent = 
            '₱' + totalAmount.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }
</script>
</body>

</html>