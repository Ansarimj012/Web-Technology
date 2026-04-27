<?php
session_start();
include_once "config.php";

if(isset($_SESSION['unique_id'])){

    $logout_id = $_SESSION['unique_id'];

    $status = "Offline now";

    mysqli_query($conn,
        "UPDATE users 
         SET status='{$status}' 
         WHERE unique_id='{$logout_id}'"
    );

    session_unset();
    session_destroy();
}

/* Always go to login page */
header("Location: ../index.php");
exit();
?>