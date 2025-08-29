<?php
// Enable error reporting at the very top of the script
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Ensure no output before JSON
ob_start();

include 'connection.php';

// Capture any PHP errors
$php_errors = [];

// Custom error handler to capture PHP errors
function customErrorHandler($errno, $errstr, $errfile, $errline) {
    global $php_errors;
    $php_errors[] = [
        'type' => $errno,
        'message' => $errstr,
        'file' => $errfile,
        'line' => $errline
    ];
    return false; // Let PHP handle the error additionally
}
set_error_handler('customErrorHandler');

// Prepare the response
$response = ['amount' => null, 'debug' => []];

try {
    // Validate and sanitize input
    $grade = isset($_POST['grade']) ? trim($_POST['grade']) : '';
    $paymentTerms = isset($_POST['paymentTerms']) ? trim($_POST['paymentTerms']) : '';

    // Validate inputs
    if (empty($grade) || empty($paymentTerms)) {
        throw new Exception('Invalid input: Grade or Payment Terms is empty');
    }

    // Ensure the grade is formatted as 'Grade X'
    if (!preg_match('/^Grade\s/', $grade)) {
        $grade = 'Grade ' . $grade;
    }

    // Log input parameters
    $response['debug']['input_grade'] = $grade;
 
    $response['debug']['input_payment_terms'] = $paymentTerms;

    // Prepare SQL query to fetch the appropriate amount
    $query = "SELECT 
        CASE 
            WHEN ? = 'full' THEN full_total
            WHEN ? = 'installment' THEN install_monthly
            WHEN ? = 'admission fee' THEN 1500 
            ELSE NULL 
        END AS amount 
    FROM tuition 
    WHERE grade_level = ?";

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        $response['debug']['sql_error'] = $conn->errorInfo();
        throw new Exception('SQL error: ' . implode(', ', $response['debug']['sql_error']));
    }

    $stmt->bindParam(1, $paymentTerms);
    $stmt->bindParam(2, $paymentTerms);
    $stmt->bindParam(3, $paymentTerms);
    $stmt->bindParam(4, $grade);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Log query result
    $response['debug']['query_result'] = $result;

    if ($result && $result['amount'] !== null) {
        $response['amount'] = floatval($result['amount']);
    } else {
        $response['debug']['error'] = 'No matching record found';
    }

    // Add any captured PHP errors to debug info
    if (!empty($php_errors)) {
        $response['debug']['php_errors'] = $php_errors;
    }

} catch (Exception $e) {
    // Capture any exceptions
    $response['debug']['exception'] = [
        'message' => $e->getMessage(),
        'code' => $e->getCode(),
        'trace' => $e->getTraceAsString()
    ];
} catch (PDOException $e) {
    // Capture PDO specific exceptions
    $response['debug']['pdo_error'] = [
        'message' => $e->getMessage(),
        'code' => $e->getCode(),
        'trace' => $e->getTraceAsString()
    ];
}

// Clear any accidental output
ob_end_clean();

// Ensure clean JSON output
header('Content-Type: application/json');
echo json_encode($response, JSON_PRETTY_PRINT);
exit;