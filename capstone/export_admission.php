<?php
    session_start();
    include('connection.php');
    $output = '';

      $query = "SELECT * FROM admission";
      $result = mysqli_query($conn,$query);

      if(mysqli_num_rows($result) > 0)
      {
        $output .= '
        
            <table class="table" bordered = "1">
                <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Middle Name</th>
                    <th>LRN Number</th>
                    <th>Birthday</th>
                    <th>PSA Number</th>
                    <th>Religion</th>
                    <th>Gender</th>
                    <th>Phone Number</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Special Education</th>
                    <th>Grade Level</th>
                    <th>With LRN</th>
                    <th>Last Grade Level</th>
                    <th>Last School Year</th>
                    <th>Last School</th>
                    <th>School Address</th>
                    <th>School Type</th>
                    <th>Father Name</th>
                    <th>Father Educational Background</th>
                    <th>Father occupation</th>
                    <th>Father Number</th>
                    <th>Mother Name</th>
                    <th>Mother Educational Background</th>
                    <th>Mother Occupation</th>
                    <th>Mother Number</th>
                </tr>

        ';

        while($row = mysqli_fetch_array($result))
        {
            $output .= '
            
            <tr>
                <td>'. $row["firstname"]. '</td>
                <td>'. $row["lastname"]. '</td>
                <td>'. $row["middlename"]. '</td>
                <td>'. $row["LRN"]. '</td>
                <td>'. $row["birthday"]. '</td>
                <td>'. $row["PSA"]. '</td>
                <td>'. $row["religion"]. '</td>
                <td>'. $row["gender"]. '</td>
                <td>'. $row["phonenumber"]. '</td>
                <td>'. $row["emailaddress"]. '</td>
                <td>'. $row["homeaddress"]. '</td>
                <td>'. $row["specialED"]. '</td>
                <td>'. $row["grade"]. '</td>
                <td>'. $row["withLRN"]. '</td>
                <td>'. $row["lastGradelevel"]. '</td>
                <td>'. $row["lastSY"]. '</td>
                <td>'. $row["lastSchool"]. '</td>
                <td>'. $row["schoolAddress"]. '</td>
                <td>'. $row["schoolType"]. '</td>
                <td>'. $row["fatherName"]. '</td>
                <td>'. $row["fatherSchool"]. '</td>
                <td>'. $row["fatherJob"]. '</td>
                <td>'. $row["fatherNumber"]. '</td>
                <td>'. $row["motherName"]. '</td>
                <td>'. $row["motherSchool"]. '</td>
                <td>'. $row["motherJob"]. '</td>
                <td>'. $row["motherNumber"]. '</td>
            </tr>
            
            ';
        }
        $output .='</table>';
        header("Content-Type: application/xls");
        header("content-Disposition: attachment; Filename = admissions.xls");
        echo $output;
      }
?>