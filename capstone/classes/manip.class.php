<?php

class Manip extends Dbexample{
//DISPLAY
    function displayStudents(){
        $sql = "SELECT s.student_ID, s.s_lname, s.s_fname, sec.sec_name, l.level_name, c.course_name From student s left join 
        section sec on s.sec_ID=sec.sec_ID join level l on s.level_ID=l.level_ID join course c on s.course_ID=
        c.course_ID";
            $statement = $this->connect()->query($sql);
            while($row = $statement->fetch()){
                echo "ID: " . $row['student_ID'] . "<br>";
                echo "Last name: " . $row['s_lname'] . "<br>";
                echo "First name: " . $row['s_fname'] . "<br>";
                echo "Section: " . $row['sec_name'] . "<br>";
                echo "Level: " . $row['level_name'] . "<br>";
                echo "Course: " . $row['course_name'] . "<br><br>";
            }
    }

//INSERT LEVEL
protected function insertStudent($level_name){
    $sql = "SELECT * FROM level";
    $statement = $this->connect()->prepare($sql);
    $statement->execute([$level_name]);
    $row = $statement->fetch();
        if($row = $statement->rowCount() > 0){
            echo "level name already exist." . "<br><br>";
        }
        else{
            $sql = "INSERT INTO level(level_name) VALUE (?)";
            $statement = $this->connect()->prepare($sql);
            $statement->execute([$level_name]);
            $row = $statement->fetchAll();
            echo"Record saved.";
            header('Location:http://localhost/MVC/index.php');
        }
}


public function select(){
    echo 'Level';
$sql = "SELECT level_name FROM level";
$statement=$this->connect()->prepare($sql);
	echo'<select>';
	while($row=$statement->fetch_assoc()){
	echo '<option value="'.$row['level_ID'].'"> '.$row['level_name'].' </option>';
	}

echo '</select>';
}

public function dropd(){
    
}

}


?>