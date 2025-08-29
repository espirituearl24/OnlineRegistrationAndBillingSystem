<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Processing</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<?php
// Include your database connection using PDO
include('dbconnection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form inputs
    $fullName = $_POST['fullName'];
    $referenceNumber = $_POST['referenceNumber'];
    $amount = str_replace(',', '', $_POST['amount']);
    $paymentMode = $_POST['paymentMode']; // Get the payment mode
    $paymentTerms = $_POST['paymentTerms']; // Get the payment terms
    $admissionId = $_POST['admission_id']; // Get the admission_id from the form

    // Validate that 'amount' is numeric
    if (!is_numeric($amount)) {
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid amount',
                    text: 'The amount must be a valid numeric value.'
                }).then(() => {
                    window.history.back(); // Go back to the previous page
                });
              </script>";
        exit();
    }

    // Convert 'amount' to a float
    $amount = floatval($amount);

    // Handle image upload
    $image = $_FILES['image'];
    $imageName = $image['name'];
    $imageTmpName = $image['tmp_name'];
    $imageSize = $image['size'];
    $imageError = $image['error'];
    $imageType = $image['type'];

    // Validate image upload
    $allowedExtensions = ['jpg', 'jpeg', 'png'];
    $imageExt = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));

    if (in_array($imageExt, $allowedExtensions) && $imageError === 0 && $imageSize < 5000000) {
        // Set a unique name for the image and move it to the upload folder
        $newImageName = uniqid('', true) . '.' . $imageExt;
        $imageDestination = 'receipts/' . $newImageName;

        if (move_uploaded_file($imageTmpName, $imageDestination)) {
            try {
                // Start a transaction
                $conn->beginTransaction();

                // Now insert into the payments table with the foreign key from admission
                $sql = "INSERT INTO payments (admission_id, fullName, referenceNumber, amount, receiptImage, payment_mode, payment_terms) 
                        VALUES (:admission_id, :fullName, :referenceNumber, :amount, :receiptImage, :paymentMode, :paymentTerms)";
                $stmt = $conn->prepare($sql);

                // Bind parameters for payments
                $stmt->bindValue(':admission_id', $admissionId); // Bind the admission_id from the form
                $stmt->bindValue(':fullName', $fullName);
                $stmt->bindValue(':referenceNumber', $referenceNumber);
                $stmt->bindValue(':amount', $amount, PDO::PARAM_STR); // Bind 'amount' as a string but treat it as a float
                $stmt->bindValue(':receiptImage', $newImageName);
                $stmt->bindValue(':paymentMode', $paymentMode); // Bind payment mode
                $stmt->bindValue(':paymentTerms', $paymentTerms); // Bind payment terms

                // Execute the payment statement
                if ($stmt->execute()) {
                    // Commit the transaction
                    $conn->commit();

                    // Check if paymentTerms is 'installment' or 'full'
                    if ($paymentTerms === 'installment' || $paymentTerms === 'full') {
                        // Update the status in the payments table
                        $updateStatusSql = "UPDATE payments SET status = 'Enrolled' WHERE admission_id = :admission_id";
                        $updateStmt = $conn->prepare($updateStatusSql);
                        $updateStmt->bindValue(':admission_id', $admissionId); // Bind the admission_id from the form

                        // Execute the update statement
                        if ($updateStmt->execute ()) {
                            // Optionally handle success for status update
                        } else {
                            // Handle error for status update
                            echo "Error updating status: " . implode(" | ", $updateStmt->errorInfo());
                        }
                    }

                    // Success message with SweetAlert and redirect to index.php
                    echo "<script>
                            Swal.fire({
                                icon: 'success',
                                title: 'Payment Successful',
                                text: 'Your payment has been recorded successfully.',
                                showConfirmButton: true,
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = 'index.php'; // Redirect to index.php
                                }
                            });
                          </script>";
                } else {
                    // Rollback the transaction on failure
                    $conn->rollBack();
                    echo "Error: " . implode(" | ", $stmt->errorInfo());
                }
            } catch (Exception $e) {
                // Rollback the transaction if something goes wrong
                $conn->rollBack();
                echo "Transaction failed: " . $e->getMessage();
            }
        } else {
            echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Upload Error',
                        text: 'Error uploading the file.'
                    });
                  </script>";
        }
    } else {
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid file',
                    text: 'Invalid file type or size. Please upload a valid image.'
                });
              </script>";
    }
}
?>
</body>
</html>