<?php
    session_start();
    include('connection.php');
    $output = '';

      $query = "SELECT * FROM enroll";
      $result = mysqli_query($conn,$query);

      if(mysqli_num_rows($result) > 0)
      {
        $output .= '
        
            <table class="table" bordered = "1">
                <tr>
                <th>Student ID</th>
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
                <th>Grade Level</th>
                <th>Father Name</th>
                <th>Father Number</th>
                <th>Mother Name</th>
                <th>Mother Number</th>
                </tr>

        ';

        while($row = mysqli_fetch_array($result))
        {
            $output .= '
            
            <tr>
                <td>'. $row["student_id"]. '</td>
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
                <td>'. $row["grade"]. '</td>
                <td>'. $row["fatherName"]. '</td>
                <td>'. $row["fatherNumber"]. '</td>
                <td>'. $row["motherName"]. '</td>
                <td>'. $row["motherNumber"]. '</td>
            </tr>
            
            ';
        }
        $output .='</table>';
        header("Content-Type: application/xls");
        header("content-Disposition: attachment; Filename = enrolled.xls");
        echo $output;
      }
?>