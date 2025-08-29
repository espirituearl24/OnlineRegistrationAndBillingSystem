<?php
require_once('TCPDF-main/tcpdf.php');

// Connect to your database
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "capstone"; 

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to count the total number of enrollees
$totalQuery = "SELECT COUNT(*) as total FROM enroll";
$totalResult = $conn->query($totalQuery);
$totalEnrollees = 0;

if ($totalResult && $row = $totalResult->fetch_assoc()) {
    $totalEnrollees = $row['total'];
}

// Query to count the number of enrollees per grade level
$gradeQuery = "SELECT grade, COUNT(*) as count FROM enroll GROUP BY grade";
$gradeResult = $conn->query($gradeQuery);

// Create a new PDF document
$pdf = new TCPDF();

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Admin');
$pdf->SetTitle('Enrollee Statistics');
$pdf->SetSubject('Count of Enrolled Students');
$pdf->SetKeywords('TCPDF, PDF, enrollees, statistics, grades');

// Set default header data
$pdf->SetHeaderData('', 0, 'Enrollee Statistics', '');

// Set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// Set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// Set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// Set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Set font
$pdf->SetFont('helvetica', '', 12);

// Add a page
$pdf->AddPage();

// Create the HTML content for the PDF
$html = '<h2>Enrollee Statistics</h2>';
$html .= '<p><strong>Total Number of Enrollees:</strong> ' . $totalEnrollees . '</p>';
$html .= '<h3>Number of Enrollees per Grade Level</h3>';
$html .= '<table border="1" cellspacing="3" cellpadding="4">
            <thead>
                <tr>
                    <th>Grade Level</th>
                    <th>Number of Enrollees</th>
                </tr>
            </thead>
            <tbody>';

// Add data about the number of enrollees per grade
if ($gradeResult && $gradeResult->num_rows > 0) {
    while ($row = $gradeResult->fetch_assoc()) {
        $html .= '<tr>
                    <td>' . $row['grade'] . '</td>
                    <td>' . $row['count'] . '</td>
                  </tr>';
    }
} else {
    $html .= '<tr><td colspan="2">No data available</td></tr>';
}

$html .= '</tbody></table>';

// Output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');

// Close and output PDF document
$pdf->Output('enrollee_statistics.pdf', 'D'); // D means download

// Close the database connection
$conn->close();


?>