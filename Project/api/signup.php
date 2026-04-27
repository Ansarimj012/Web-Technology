<?php
    session_start();
   require_once __DIR__ . "/config.php";
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $hastag = '#'. rand(1000, 9999);
    $full_username = $username . $hastag;
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $c_password = $_POST['c_password'];

    if(!empty($username) && !empty($email) && !empty($password) && !empty($c_password)){
        if(filter_var($email, FILTER_VALIDATE_EMAIL)){
            $email_escaped = mysqli_real_escape_string($conn, $email);
            $sql = mysqli_query($conn, "SELECT * FROM users WHERE email = '{$email_escaped}'");
            if(mysqli_num_rows($sql) > 0){
                header("location: ../auth/auth.php?auth=signup");
                $_SESSION['alertError'] = "$email - This email already exists!";
            } else {
                if($password == $c_password){
                    $ran_id = rand(time(), 100000000);
                    $status = "Active now";
                    // Secure bcrypt hash
                    $encrypt_pass = password_hash($password, PASSWORD_BCRYPT);
                    $insert_query = mysqli_query($conn, "INSERT INTO users (unique_id, username, hastag, full_username, email, password, status)
                        VALUES ('{$ran_id}', '{$username}', '{$hastag}', '{$full_username}', '{$email_escaped}', '{$encrypt_pass}', '{$status}')");
                    if($insert_query){
                        $select_sql2 = mysqli_query($conn, "SELECT * FROM users WHERE email = '{$email_escaped}'");
                        if(mysqli_num_rows($select_sql2) > 0){
                            $result = mysqli_fetch_assoc($select_sql2);
                            $_SESSION['unique_id'] = $result['unique_id'];
                            header("location: ../users.php");
                            $_SESSION['alertSuccess'] = "Account created successfully!";
                        } else {
                            header("location: ../auth/auth.php?auth=signup");
                            $_SESSION['alertError'] = "This email address does not exist!";
                        }
                    } else {
                        header("location: ../auth/auth.php?auth=signup");
                        $_SESSION['alertError'] = "Something went wrong. Please try again!";
                    }
                } else {
                    header("location: ../auth/auth.php?auth=signup");
                    $_SESSION['alertError'] = "Password & Confirm Password do not match!";
                }
            }
        } else {
            header("location: ../auth/auth.php?auth=signup");
            $_SESSION['alertError'] = "$email is not a valid email!";
        }
    } else {
        header("location: ../auth/auth.php?auth=signup");
        $_SESSION['alertError'] = "All input fields are required!";
    }
?>
