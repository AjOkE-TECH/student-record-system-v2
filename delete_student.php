<?php
session_start();

if(!isset($_SESSION['admin'])){
    header("Location: login");
    exit();
}

include "config/database.php";

if(isset($_GET['id'])){

    $id = $_GET['id'];

    $delete = mysqli_query($conn, "DELETE FROM students WHERE id='$id'");

    if($delete){
        header("Location: view_students");
        exit();
    }else{
        echo "Failed to delete student.";
    }
}
?>