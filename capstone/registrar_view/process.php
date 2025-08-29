<?php

session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

include('dbconnection.php');

//LIPAT ng Enroll si Admission
if(isset($_POST['save_enroll']))
{
    $id = $_POST['save_enroll'];
    $sy = date('Y');
    $padded_id = str_pad($id, 3, '0', STR_PAD_LEFT);
    $user_id = "GBA-". $sy . "-" .$padded_id;

    $query = "INSERT INTO `enroll`(`id`, `firstname`, `lastname`, `middlename`, `LRN`, `birthday`, `PSA`, `religion`, `gender`, `phonenumber`, `emailaddress`, `homeaddress`, `grade`, `fatherName`, `fatherNumber`, `motherName`, `motherNumber`) SELECT `id`, `firstname`, `lastname`, `middlename`, `LRN`, `birthday`, `PSA`, `religion`, `gender`, `phonenumber`, `emailaddress`, `homeaddress`, `grade`, `fatherName`, `fatherNumber`, `motherName`, `motherNumber` FROM `admission` WHERE id = '$id'";
    $statement = $conn->prepare($query);
    $statement->execute();

    //Gawa unique ID
    $sql2 = "UPDATE enroll SET student_id = '$user_id' WHERE id = '$id'";
    $stm = $conn->prepare($sql2); 
    $stm->execute();

    //Update status in payment table to 'Enrolled' using id
    $updatePaymentStatus = "UPDATE payments SET status = 'Enrolled' WHERE admission_id = '$id'";
    $stmtUpdatePayment = $conn->prepare($updatePaymentStatus);
    $stmtUpdatePayment->execute();

    // Fetch student's email from admission table
$emailQuery = "SELECT emailaddress FROM admission WHERE id = '$id'";
$emailStatement = $conn->prepare($emailQuery);
$emailStatement->execute();
$emailResult = $emailStatement->fetch(PDO::FETCH_ASSOC);

if ($emailResult) {
    $studentEmail = $emailResult['emailaddress'];

    // Send email notification using PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'gba.admissions@gmail.com';
        $mail->Password = 'ylvn aiva nbcy jjut'; // Use app-specific password
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->setFrom('gba.admissions@gmail.com', 'Admissions Office');
        $mail->addAddress($studentEmail); // Use student's email from admission table

        $mail->isHTML(true);
        $mail->Subject = 'Admission Accepted at Grace Baptist Academy';
        $mail->Body = 'Dear Student,<br><br>Congratulations! Your admission has been accepted. To complete your enrollment, please provide the required documents.<br><br>Best regards,<br>Grace Baptist Academy Admissions Office';

        $mail->send();

        // Set success message
        $_SESSION['error'] = "Student Added to Enrollees!";
        $_SESSION['status'] = "success";
        $_SESSION['title'] = "Success!";
    } catch (Exception $e) {
        // Set error message for failed email
        $_SESSION['error'] = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        $_SESSION['status'] = "error";
        $_SESSION['title'] = "Email Error!";
    }
} else {
    // Set error message if email not found
    $_SESSION['error'] = "Email address not found for the student.";
    $_SESSION['status'] = "error";
    $_SESSION['title'] = "Error!";
}

    // Delete from admission table
    $sql = "DELETE FROM admission WHERE id='$id'";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    

    header('Location: list_due.php');
    exit();
}

if(isset($_POST['save_add']))
{
    $id = $_POST['save_add'];

    $query = "INSERT INTO `enroll`(`id`, `student_id`, `firstname`, `lastname`, `middlename`, `LRN`, `birthday`, `PSA`, `religion`, `gender`, `phonenumber`, `emailaddress`, `homeaddress`, `grade`, `fatherName`, `fatherNumber`, `motherName`, `motherNumber`) SELECT `id`, `student_id`, `firstname`, `lastname`, `middlename`, `LRN`, `birthday`, `PSA`, `religion`, `gender`, `phonenumber`, `emailaddress`, `homeaddress`, `grade`, `fatherName`, `fatherNumber`, `motherName`, `motherNumber` FROM `old_admission` WHERE id = '$id'";
    $statement = $conn->prepare($query);
    $statement->execute();

    

    //Update status in payment table to 'Enrolled' using id
    $updatePaymentStatus = "UPDATE payments SET status = 'Enrolled' WHERE admission_id = '$id'";
    $stmtUpdatePayment = $conn->prepare($updatePaymentStatus);
    $stmtUpdatePayment->execute();

    // Fetch student's email from admission table
$emailQuery = "SELECT emailaddress FROM old_admission WHERE id = '$id'";
$emailStatement = $conn->prepare($emailQuery);
$emailStatement->execute();
$emailResult = $emailStatement->fetch(PDO::FETCH_ASSOC);

if ($emailResult) {
    $studentEmail = $emailResult['emailaddress'];

    // Send email notification using PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'gba.admissions@gmail.com';
        $mail->Password = 'ylvn aiva nbcy jjut'; // Use app-specific password
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->setFrom('gba.admissions@gmail.com', 'Admissions Office');
        $mail->addAddress($studentEmail); // Use student's email from admission table

        $mail->isHTML(true);
        $mail->Subject = 'Admission Accepted at Grace Baptist Academy';
        $mail->Body = 'Dear Student,<br><br>Congratulations! Your admission has been accepted. To complete your enrollment, please provide the required documents.<br><br>Here is your admission ID: ' . $id . '. If you choose to pay over the counter, provide your admission ID to the accounting staff at Grace Baptist Academy.<br><br>Best regards,<br>Grace Baptist Academy Admissions Office';

        $mail->send();

        // Set success message
        $_SESSION['error'] = "Student Added to Enrollees!";
        $_SESSION['status'] = "success";
        $_SESSION['title'] = "Success!";
    } catch (Exception $e) {
        // Set error message for failed email
        $_SESSION['error'] = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        $_SESSION['status'] = "error";
        $_SESSION['title'] = "Email Error!";
    }
} else {
    // Set error message if email not found
    $_SESSION['error'] = "Email address not found for the student.";
    $_SESSION['status'] = "error";
    $_SESSION['title'] = "Error!";
}

    // Delete from admission table
    $sql = "DELETE FROM old_admission WHERE id='$id'";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    

    header('Location: list_due.php');
    exit();
}





//Archive admission
if(isset($_POST['archive_addmission'])){
    $id = $_POST['archive_addmission'];

    $sql = "INSERT INTO `archive_admission`(`id`, `firstname`, `lastname`, `middlename`, `LRN`, `birthday`, `PSA`, `religion`, `gender`, `phonenumber`, `emailaddress`, `homeaddress`, `specialED`, `grade`, `withLRN`, `lastGradelevel`, `lastSY`, `lastSchool`, `schoolAddress`, `schoolType`, `fatherName`, `fatherSchool`, `fatherJob`, `fatherNumber`, `motherName`, `motherSchool`, `motherJob`, `motherNumber`, `currentdate`) SELECT `id`, `firstname`, `lastname`, `middlename`, `LRN`, `birthday`, `PSA`, `religion`, `gender`, `phonenumber`, `emailaddress`, `homeaddress`, `specialED`, `grade`, `withLRN`, `lastGradelevel`, `lastSY`, `lastSchool`, `schoolAddress`, `schoolType`, `fatherName`, `fatherSchool`, `fatherJob`, `fatherNumber`, `motherName`, `motherSchool`, `motherJob`, `motherNumber`, NOW() FROM `admission` WHERE id = '$id' ";
    $statement = $conn->prepare($sql);
    $statement->execute();

    $query = "DELETE FROM admission WHERE id = '$id' ";
    $stmt = $conn->prepare($query);
    $stmt->execute();

    $_SESSION['error'] = "Data Archived!";
    $_SESSION['status'] = "success";
    $_SESSION['title'] = "Success!";

    header('Location: list_due.php');
    exit();
}

//Recover admission
if(isset($_POST['recover_admission'])){
    $id = $_POST['recover_admission'];

    $sql = "INSERT INTO `admission`(`id`, `firstname`, `lastname`, `middlename`, `LRN`, `birthday`, `PSA`, `religion`, `gender`, `phonenumber`, `emailaddress`, `homeaddress`, `specialED`, `grade`, `withLRN`, `lastGradelevel`, `lastSY`, `lastSchool`, `schoolAddress`, `schoolType`, `fatherName`, `fatherSchool`, `fatherJob`, `fatherNumber`, `motherName`, `motherSchool`, `motherJob`, `motherNumber`, `currentdate`) SELECT `id`, `firstname`, `lastname`, `middlename`, `LRN`, `birthday`, `PSA`, `religion`, `gender`, `phonenumber`, `emailaddress`, `homeaddress`, `specialED`, `grade`, `withLRN`, `lastGradelevel`, `lastSY`, `lastSchool`, `schoolAddress`, `schoolType`, `fatherName`, `fatherSchool`, `fatherJob`, `fatherNumber`, `motherName`, `motherSchool`, `motherJob`, `motherNumber`, NOW() FROM `archive_admission` WHERE id = '$id' ";
    $statement = $conn->prepare($sql);
    $statement->execute();

    $query = "DELETE FROM archive_admission WHERE id = '$id' ";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    

    $_SESSION['error'] = "Data Recovered!";
    $_SESSION['status'] = "success";
    $_SESSION['title'] = "Success!";

    header('Location: tarchive_admission.php');
    exit();
}

// ADD Account
if (isset($_POST['account_add'])) {
    $id = $_POST['account_add'];

    // Fetch student_id, firstname, lastname, and birthday from enroll table
    $sql = "SELECT student_id, firstname, lastname, birthday FROM enroll WHERE id = :id";
    $stm = $conn->prepare($sql);
    $stm->bindParam(':id', $id);
    $stm->execute();

    $user = $stm->fetch(PDO::FETCH_ASSOC);
    $username = $user['student_id'];
    $birthday = $user['birthday']; // Get the birthday to use as password

    // Check if the account already exists
    $query = "SELECT * FROM users WHERE username = :username";
    $statement = $conn->prepare($query);
    $statement->bindParam(':username', $username);
    $statement->execute();

    if ($statement->rowCount() > 0) {
        $_SESSION['error'] = "Account Already Exist!";
        $_SESSION['status'] = "warning";
        $_SESSION['title'] = "Notice!";
        header('Location: tstudent.php');
        exit();
    } else {
        // Insert the new user with the same ID as in the enroll table and use birthday as password
        $query = "INSERT INTO `users`(`id`, `username`, `password`, `firstname`, `lastname`) 
                  VALUES (:id, :username, :password, :firstname, :lastname)";
        $statement = $conn->prepare($query);
        $statement->execute([
            ':id' => $id,
            ':username' => $username,
            ':password' => $birthday, // Use birthday as the password
            ':firstname' => $user['firstname'],
            ':lastname' => $user['lastname']
        ]);

        $_SESSION['error'] = "Account Added!";
        $_SESSION['status'] = "success";
        $_SESSION['title'] = "Success!";
    }

    header('Location: tstudent.php');
    exit();
}
//Reset Password
if(isset($_POST['reset_pass'])){
    $id = $_POST['reset_pass'];

    $sqlfirst = "SELECT `firstname` FROM users WHERE `id` = '$id' ";
    $stm = $conn->prepare($sqlfirst);
    $stm->execute();
    $fname = $stm->fetch(PDO::FETCH_ASSOC);
    $firstname = $fname['firstname'];


    $sqllast = "SELECT `lastname` FROM users WHERE `id` = '$id'";
    $stm2 = $conn->prepare($sqllast);
    $stm2->execute();
    $lname = $stm2->fetch(PDO::FETCH_ASSOC);
    $lastname = $lname['lastname'];

    $sqlbday = "SELECT `birthday` FROM enroll WHERE `firstname` = '$firstname' AND `lastname` = '$lastname'";
    $result = $conn->prepare($sqlbday);
    $result->execute();
    $birth = $result->fetch(PDO::FETCH_ASSOC);
    $bday = $birth['birthday'];

    $sql = "UPDATE `users` SET `password` = '$bday' WHERE `id` = '$id' ";
    $statement = $conn->prepare($sql);
    $statement->execute();

    $_SESSION['error'] = "Password Successfully Reset!";
    $_SESSION['status'] = "success";
    $_SESSION['title'] = "Success!";

    header('Location: account_table.php');
    exit();
}



//UPDATE ENROLL
if(isset($_POST['update_enroll']))
{
    $id = $_POST['id'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $middlename = $_POST['middleName'];
    $LRN = $_POST['LRN'];
    $dob = date('Y-m-d', strtotime($_POST['dob']));
    $PSA = $_POST['PSA'];
    $religion = $_POST['religion'];
    $gender = $_POST['gender'];
    $phoneNumber = $_POST['phoneNumber'];
    $emailAddress = $_POST['emailAddress'];
    $homeAddress = $_POST['homeAddress'];
    $grade = $_POST['grade'];
    $fatherName = $_POST['fatherName'];
    $fatherNumber = $_POST['fatherNumber'];
    $motherName = $_POST['motherName'];
    $motherNumber = $_POST['motherNumber'];

    try{

    $query = "UPDATE `enroll` SET `firstname`='$firstName',`lastname`='$lastName',`middlename`='$middlename',`LRN`='$LRN',`birthday`='$dob',`PSA`='$PSA',`religion`='$religion',`gender`='$gender',`phonenumber`='$phoneNumber',`emailaddress`='$emailAddress',`homeaddress`='$homeAddress',`grade`='$grade',`fatherName`='$fatherName',`fatherNumber`='$fatherNumber',`motherName`='$motherName',`motherNumber`='$motherNumber' WHERE id = '$id'";
        
    $statement = $conn->prepare($query);
    $query_execute = $statement->execute();

    $_SESSION['error'] = "Data Updated!";
    $_SESSION['status'] = "success";
    $_SESSION['title'] = "Success!";

    header('Location: tstudent.php');
    exit();
    }catch(PDOException $e){
        $_SESSION['error'] = "Data not Updated!";
        $_SESSION['status'] = "error";
        $_SESSION['title'] = "Notice!";

        header('Location: admin.php');
        exit();
    }
}

//ARCHIVE ENROLL
if(isset($_POST['archive']))
{
    $id = $_POST['archive'];

    $query = "INSERT INTO `archive`(`id`, `student_id`, `firstname`, `lastname`, `middlename`, `LRN`, `birthday`, `PSA`, `religion`, `gender`, `phonenumber`, `emailaddress`, `homeaddress`, `grade`, `fatherName`, `fatherNumber`, `motherName`, `motherNumber`, `currentdate`) SELECT `id`, `student_id`, `firstname`, `lastname`, `middlename`, `LRN`, `birthday`, `PSA`, `religion`, `gender`, `phonenumber`, `emailaddress`, `homeaddress`, `grade`, `fatherName`, `fatherNumber`, `motherName`, `motherNumber`, NOW() FROM `enroll` WHERE id = '$id'";
    $statement = $conn->prepare($query);
    $statement->execute();

    $sql = "DELETE FROM enroll WHERE id='$id'";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $_SESSION['error'] = "Data Archived!";
    $_SESSION['status'] = "success";
    $_SESSION['title'] = "Success!";

    header('Location: tstudent.php');
    exit();
}

//RETURN DATA to ENROLL from ARCHIVE

if(isset($_POST['return']))
{

    $id = $_POST['return'];

    $query = "INSERT INTO `enroll`(`id`, `student_id`, `firstname`, `lastname`, `middlename`, `LRN`, `birthday`, `PSA`, `religion`, `gender`, `phonenumber`, `emailaddress`, `homeaddress`, `grade`, `fatherName`, `fatherNumber`, `motherName`, `motherNumber`) SELECT `id`, `student_id`, `firstname`, `lastname`, `middlename`, `LRN`, `birthday`, `PSA`, `religion`, `gender`, `phonenumber`, `emailaddress`, `homeaddress`, `grade`, `fatherName`, `fatherNumber`, `motherName`, `motherNumber` FROM `archive` WHERE id = '$id'";
    $statement = $conn->prepare($query);
    $statement->execute();

    $sql = "DELETE FROM archive WHERE id='$id'";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $_SESSION['error'] = "Data Recovered!";
    $_SESSION['status'] = "success";
    $_SESSION['title'] = "Success!";

    header('Location: tarchive_estudent.php');
    exit();


}

//payment 










?>