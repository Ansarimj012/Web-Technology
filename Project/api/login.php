<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . "/config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!empty($email) && !empty($password)) {

        $sql = mysqli_query($conn, "SELECT * FROM users WHERE email='{$email}'");

        if (!$sql) {
            die("SQL Error: " . mysqli_error($conn));
        }

        if (mysqli_num_rows($sql) > 0) {

            $row = mysqli_fetch_assoc($sql);

            $valid = false;

            if (password_verify($password, $row['password'])) {
                $valid = true;
            } elseif (strlen($row['password']) == 32 && md5($password) == $row['password']) {

                $new_hash = password_hash($password, PASSWORD_DEFAULT);

                mysqli_query($conn, "UPDATE users SET password='$new_hash' WHERE unique_id='{$row['unique_id']}'");

                $valid = true;
            }

            if ($valid) {

                $status = "Active now";

                $sql2 = mysqli_query($conn, "UPDATE users SET status='$status' WHERE unique_id='{$row['unique_id']}'");

                if ($sql2) {
                    $_SESSION['unique_id'] = $row['unique_id'];
                    $_SESSION['alertSuccess'] = "Welcome back!";
                    header("Location: ../users.php");
                    exit();
                } else {
                    $_SESSION['alertError'] = "Something went wrong!";
                    header("Location: ../auth/auth.php?auth=login");
                    exit();
                }

            } else {
                $_SESSION['alertError'] = "Email or Password is incorrect!";
                header("Location: ../auth/auth.php?auth=login");
                exit();
            }

        } else {
            $_SESSION['alertError'] = "Email does not exist!";
            header("Location: ../auth/auth.php?auth=login");
            exit();
        }

    } else {
        $_SESSION['alertError'] = "All fields are required!";
        header("Location: ../auth/auth.php?auth=login");
        exit();
    }

} else {
    echo "Invalid Request";
}
?>