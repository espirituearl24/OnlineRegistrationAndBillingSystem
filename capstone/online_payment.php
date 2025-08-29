<?php
session_start();
include 'connection.php';

// Check if the grade is passed in the URL
$selectedGrade = isset($_GET['grade']) ? str_replace('Grade ', '', $_GET['grade']) : '1';

$gradeTuition = "Grade " . $selectedGrade;

// Fetch full total and installment monthly for the selected grade
$tuitionQuery = "SELECT full_total, install_monthly FROM tuition WHERE grade_level = ?";
$stmt = $conn->prepare($tuitionQuery);
$stmt->bind_param("s", $gradeTuition);
$stmt->execute();
$result = $stmt->get_result();
$tuitionRow = $result->fetch_assoc();

$fullTotal = $tuitionRow ? $tuitionRow['full_total'] : 'N/A';
$installMonthly = $tuitionRow ? $tuitionRow['install_monthly'] : 'N/A';
$stmt->close();
// Define the image paths based on the selected grade level
$images = [
    'K1' => 'img/fee_k1_2.jpg',   // Kinder 1
    'K2' => 'img/fee_k1_2.jpg',   // Kinder 2
    '1'  => 'img/fee_g1_2.jpg',
    '2'  => 'img/fee_g1_2.jpg',
    '3'  => 'img/fee_g3.jpg',
    '4'  => 'img/fee_g4_5.jpg', 
    '5'  => 'img/fee_g4_5.jpg', 
    '6'  => 'img/fee_g6.jpg',
    '7'  => 'img/fee_g7_9.jpg',
    '8'  => 'img/fee_g7_9.jpg',
    '9'  => 'img/fee_g7_9.jpg',
    '10' => 'img/fee_g10.jpg'
];

// Set the image to display based on the selected grade
$gradeImage = isset($images[$selectedGrade]) ? $images[$selectedGrade] : $images['1'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Payment | GBA</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
          rel="stylesheet" 
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" 
          crossorigin="anonymous">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" 
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" 
            crossorigin="anonymous"></script>

   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <style>
        .centered-section {
            display: flex; 
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: auto;
        }
        .dropdown-container {
            display: flex;
            gap: 50px;
            justify-content: center;
        }
        .dropdown-container label {
            margin-right: 10px;
        }
        .dropdown-container select {
            width: 200px;
        }
        .proceed-btn {
            margin-top: 20px;
            display: flex;
            justify-content: center;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg " style="background-color:#0a2757;">
    <div class="container-fluid py-1">
        <!-- Logo -->
        <a class="navbar-brand fw-bold ps-3 text-warning" href="index.php">
            <img src="img/logo1.png" alt="Logo" width="40" height="40" class="d-inline-block align-text-center"> 
            Grace Baptist Academy
        </a>
        <!-- Logo End-->
    </div>
</nav>
<!-- Navbar End-->
 <section class="centered-section">
    <!-- Dropdowns Row -->
    <div class="dropdown-container mt-5">
        <!-- First Dropdown: Payment Mode -->
        <div>
            <label for="paymentMode">Choose your mode of payment</label>
            <select id="paymentMode" name="paymentMode" class="form-select" required>
                <option selected disabled>Select payment</option>
                <option value="gcash">Gcash</option>
                <option value="bank">Bank Transfer</option>
            </select>
        </div>

        <!-- Second Dropdown: Payment Terms -->
        <div>
            <label for="paymentTerms">Payment terms</label>
            <select id="paymentTerms" name="paymentTerms" class="form-select" required>
                <option selected disabled>Select terms</option>
                <option value="admission fee">Admission Fee Only</option>
                <option value="full">Full Payment</option>
                <option value="installment">Installment</option>

            </select>
        </div>
    </div>



    <!-- Fee Message and Image Below Dropdowns -->
    <div class="mt-4" id="feeMessage" style="display: none;">
        <h2>Admission Fee - P1,500</h2>
    </div>
    <div class="mt-4" id="gradeImageContainer">
    <!-- <p><?php echo"$gradeTuition";?></p> -->
    <div class="mt-4" id="fullTotalContainer" style="display: none;">
    <h3>Full Payment Total: ₱<?php echo number_format($fullTotal, 2); ?></h3>
</div>
<div class="mt-4" id="installmentContainer" style="display: none;">
    <h3>Monthly Installment: ₱<?php echo number_format($installMonthly, 2); ?></h3>
</div>
        <img id="gradeImage" src="<?= $gradeImage ?>" alt="Grade <?= $selectedGrade ?> Image" class="img-fluid">
    </div>

    <!-- Proceed Button -->
    <div class="proceed-btn mb-5">
        <button type="button" class="btn btn-info" id="proceedBtn">Continue Payment</button>
    </div>
</section>

<!-- Gcash Modal -->
<div class="modal fade" id="gcashModal" tabindex="-1" aria-labelledby="gcashModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="process_payment.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="paymentMode" value="gcash"> <!-- Hidden input for payment mode -->
                <input type="hidden" name="paymentTerms" id="paymentTermsHiddenGcash"> <!-- Hidden input for payment terms -->
                <div class="modal-header">
                    <h5 class="modal-title" id="gcashModalLabel">Gcash Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="fs-5">Step 1. Scan the QR code or type the mobile number below</p>
                    <img src="img/gcash_qr.jpg" alt="QR Code" class="img-fluid">
                    <p class="fs-5 mt-3">Step 2. Upload the screenshot/downloaded receipt (accepts png, jpg, jpeg)</p>
                    <input type="file" class="form-control" name="image" id="image" accept="image/*" required>
                    <p class="fs-5 mt-3">Step 3. Please type your name, reference number, and the amount of the Gcash Payment</p>
                    <div class="mb-3">
                        <label for="fullName" class="form-label">Full Name:</label>
                        <input type="text" class="form-control" name="fullName" id="fullName" placeholder="Enter your full name" required>
                    </div>
                    <div class="mb-3">
                        <label for="referenceNumber" class="form-label">Reference Number:</label>
                        <input type="text" class="form-control" name="referenceNumber" id="referenceNumber" placeholder="Enter Gcash Receipt Reference number" required>
                    </div>
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount:</label>
                        <input type="text" class="form-control" name="amount" id="amount" placeholder="Enter the amount you sent" required oninput="formatAmount(this)">
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Submit Payment</button>
                </div>
            </form>
        </div>
    </div>
</div> 

<!-- Bank Transfer Modal -->
<div class="modal fade" id="bankModal" tabindex="-1" aria-labelledby="bankModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="process_payment.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="paymentMode" value="bank"> <!-- Hidden input for payment mode -->
                <input type="hidden" name="paymentTerms" id="paymentTermsHiddenBank"> <!-- Hidden input for payment terms -->
                <div class="modal-header">
                    <h5 class="modal-title" id="bankModalLabel">Bank Transfer - Union Bank</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <img src="img/ub.jpg" alt="QR Code" class="img-fluid mb-3">
                    <p class="fs-5"><strong>:</strong> Grace Baptist Academy of Dasmarinas Inc </p>
                    <p class="fs-5 mt-3"><strong>Account Number: </strong> 7590568290</p>
                    <div class="mb-3">
                        <label for="fullNameBank" class="form-label">Full Name:</label>
                        <input type="text" class="form-control" name="fullName" id="fullNameBank" placeholder="Enter your full name" required>
                    </div>
                    <div class="mb-3">
                        <label for="referenceNumberBank" class="form-label">Reference Number:</label>
                        <input type="text" class="form-control" name="referenceNumber" id="referenceNumberBank" placeholder="Enter Reference number" required>
                    </div>
                    <div class="mb-3">
                        <label for="amountBank" class="form-label">Amount:</label>
                        <input type="text" class="form-control" name="amount" id="amountBank" placeholder="Enter the amount you sent" required oninput="formatAmount(this)">
                    </div>
                    <p class="fs-5 mt-3">Upload the screenshot/downloaded receipt:</p>
                    <input type="file" class="form-control" name="image" id="imageBank" accept="image/*" required>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Submit Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>




<!-- Script to handle image and modal display -->
<script>
function formatAmount(input) {
    // Remove non-digit characters
    let value = input.value.replace(/[^0-9]/g, '');

    // Format the number with commas
    value = parseFloat(value).toLocaleString('en-US');

    // Set the formatted value back to the input
    input.value = value;
}

// Function to set amount input based on payment terms
function setAmountInput(paymentTerms) {
    const fullTotal = <?php echo $fullTotal; ?>;
    const installMonthly = <?php echo $installMonthly; ?>;
    
    const gcashAmountInput = document.getElementById('amount');
    const bankAmountInput = document.getElementById('amountBank');

    let amountToSet = '';
    switch(paymentTerms) {
        case 'admission fee':
            amountToSet = '1,500.00';
            break;
        case 'full':
            amountToSet = fullTotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            break;
        case 'installment':
            amountToSet = installMonthly.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            break;
        default:
            amountToSet = '';
    }

    // Set the amount in both modal inputs
    gcashAmountInput.value = amountToSet;
    bankAmountInput.value = amountToSet;
}

const gradeImage = document.getElementById('gradeImage');
const paymentTermsSelect = document.getElementById('paymentTerms');
const feeMessage = document.getElementById('feeMessage');
const gradeImageContainer = document.getElementById('gradeImageContainer');

// Extract the base image name without extension
let baseImage = "<?= pathinfo($gradeImage, PATHINFO_FILENAME); ?>";

// Payment Terms Change Event Listener
paymentTermsSelect.addEventListener('change', function () {
    const selectedTerm = this.value;
    const feeMessage = document.getElementById('feeMessage');
    const fullTotalContainer = document.getElementById('fullTotalContainer');
    const installmentContainer = document.getElementById('installmentContainer');
    const gradeImageContainer = document.getElementById('gradeImageContainer');

    // Hide all containers first
    feeMessage.style.display = 'none';
    fullTotalContainer.style.display = 'none';
    installmentContainer.style.display = 'none';
    gradeImageContainer.style.display = 'block';

    // Handle image and message display
    if (selectedTerm === 'admission fee') {
        feeMessage.style.display = 'block';
        gradeImageContainer.style.display = 'none';
    } else {
        // Construct the new image path using base image name and selected payment term
        const newImageSrc = 'img/' + baseImage + '_' + selectedTerm + '.jpg';
        gradeImage.src = newImageSrc;

        // Show appropriate container based on selected term
        switch(selectedTerm) {
            case 'full':
                fullTotalContainer.style.display = 'block';
                break;
            case 'installment':
                installmentContainer.style.display = 'block';
                break;
        }
    }

    // Update hidden input fields for payment terms
    document.getElementById('paymentTermsHiddenGcash').value = selectedTerm;
    document.getElementById('paymentTermsHiddenBank').value = selectedTerm;
});

// Proceed Button Event Listener
document.getElementById('proceedBtn').addEventListener('click', function () {
    const paymentMode = document.getElementById('paymentMode').value;
    const paymentTerms = document.getElementById('paymentTerms').value;

    // Validate payment mode and terms
    if (!paymentMode || !paymentTerms) {
        Swal.fire({
            icon: 'warning',
            title: 'Incomplete Selection',
            text: 'Please select both payment mode and payment terms.'
        });
        return;
    }

    // Set the hidden input for payment terms in both modals
    document.getElementById('paymentTermsHiddenGcash').value = paymentTerms;
    document.getElementById('paymentTermsHiddenBank').value = paymentTerms;

    // Close any open modals before opening a new one
    const openModal = document.querySelector('.modal.show');
    if (openModal) {
        const bootstrapModal = bootstrap.Modal.getInstance(openModal);
        bootstrapModal.hide();
    }

    // Show appropriate modal
    if (paymentMode === 'gcash') {
        var gcashModal = new bootstrap.Modal(document.getElementById('gcashModal'));
        gcashModal.show();
    } else if (paymentMode === 'bank') {
        var bankModal = new bootstrap.Modal(document.getElementById('bankModal'));
        bankModal.show();
    }

    // Set the amount input
    setAmountInput(paymentTerms);
});
</script>


</body>
</html>
