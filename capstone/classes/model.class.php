<?php

class Model extends Dbexample{

//All Level
    protected function accessLevel(){
        $sql = "CALL displayLevel()";
        $statement = $this->connect()->prepare($sql);
        $statement->execute([]);
        $row = $statement->fetchAll();

        return $row;
    }
  
//INSERT LEVEL
protected function insertLevel($level_ID, $level_name){
    $sql = "CALL ifLevelexist(?)";
    $statement = $this->connect()->prepare($sql);
    $statement->execute([$level_name]);
    $row = $statement->fetch();
        if($row = $statement->rowCount() > 0){
            echo "level name already exist." . "<br><br>";
        }
        else{
            $sql = " CALL insertLevel (?, ?)";
            $statement = $this->connect()->prepare($sql);
            $statement->execute([$level_ID, $level_name]);
            $row = $statement->fetchAll();
            echo"Record saved.";
            header('Location:http://localhost/Edited2/edited/admin_level.php');
        }
}

//UPDATE LEVEL
protected function updateLevel($level_name, $level_ID){ 
    $sql = "CALL updateLevel(?, ?)"; //NO AND JUST ","
    $statement = $this->connect()->prepare($sql);
    $statement->execute([$level_name, $level_ID]);
    $row = $statement->fetchAll();
    echo"Record updated.";
    header('Location:http://localhost/Edited2/edited/admin_level.php');
}

//DELETE LEVEL

protected function deleteLevel($level_ID){ 
    $sql = "CALL deleteLevel(?)"; 
    $statement = $this->connect()->prepare($sql);
    $statement->execute([$level_ID]);
    $row = $statement->fetchAll();
    echo"Record deleted.";
    header('Location:http://localhost/Edited2/edited/admin_level.php');
}

}
?>