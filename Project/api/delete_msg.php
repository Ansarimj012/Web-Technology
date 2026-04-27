<?php
session_start();
include_once "config.php";

if(!isset($_SESSION['unique_id'])){
    exit();
}

if(isset($_POST['msg_id'])){

    $msg_id = mysqli_real_escape_string($conn, $_POST['msg_id']);
    $user_id = $_SESSION['unique_id'];

    $sql = "DELETE FROM messages 
            WHERE msg_id = '{$msg_id}' 
            AND outgoing_msg_id = '{$user_id}'";

    mysqli_query($conn, $sql);

    echo "success";
}
?>