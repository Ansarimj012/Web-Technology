<?php
    if (isset($_POST['update_pfp'])) {
        if (isset($_FILES['image'])) {
            $sql = mysqli_query($conn, "SELECT * FROM users WHERE unique_id = {$_SESSION['unique_id']}");
            $row = mysqli_fetch_assoc($sql);

            if ($row['img'] == 'logo.jpg') {
                $old_pfp = "";
            } else {
                $old_pfp = "api/images/pfp/" . $row['img'];
            }

            $img_name = $_FILES['image']['name'];
            $img_type = $_FILES['image']['type'];
            $tmp_name = $_FILES['image']['tmp_name'];

            $img_explode = explode('.', $img_name);
            $img_ext = end($img_explode);

            $extensions = ["jpeg", "png", "jpg"];
            if (in_array($img_ext, $extensions) === true) {
                $types = ["image/jpeg", "image/jpg", "image/png"];
                if (in_array($img_type, $types) === true) {
                    $time = time();
                    $new_img_name = $time . $img_name;
                    $unique_id = $_SESSION['unique_id'];
                    if (move_uploaded_file($tmp_name, "api/images/pfp/" . $new_img_name)) {
                        $update_pfp_query = mysqli_query($conn, "UPDATE `users` SET `img` = '{$new_img_name}' WHERE `users`.`unique_id` = $unique_id;");

                        // Delete Old PFP
                        if (!empty($old_pfp)) {
                            unlink($old_pfp);
                        }

                        $_SESSION['alertSuccess'] = 'Profile Pic Updated';
                    }
                } else {
                    echo "Please upload an image file - jpeg, png, jpg";
                }
            } else {
                echo "Please upload an image file - jpeg, png, jpg";
            }
        }
    }
?>