<?php

    session_start();

    include 'dbconnection.php';

    if(isset($_SESSION['user']))
    {
        if ($_SESSION['type'] == 0)
        {
            echo $_SESSION['lastname'] . " this is a student";
            echo $_SESSION['id'] . "this is a student";
            header('location:student1.php');
        }

        if ($_SESSION['type'] == 1)
        {
            echo $_SESSION['lastname'] . " this is a admin";
            header('location:admin.php');
        }
 
        if ($_SESSION['type'] == 2)
        {
            echo $_SESSION['lastname'] . " principal";
            header('location:principal.php');
        }

        if ($_SESSION['type'] == 3)
        {
            echo $_SESSION['lastname'] . " registar";
            header('location:registrar_view/registrar.php');
        }
        if ($_SESSION['type'] == 4)
        {
            echo $_SESSION['lastname'] . " admin";
            header('location:useradmin.php');
        }
    }

?>