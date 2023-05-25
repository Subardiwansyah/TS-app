<?php
    
    include "config.php";
    $data = array();
    $data['status'] = false;
    
    $target_dir = "../uploads/profile/";
    
    $id = $_POST['id'];
    
    $target_file = $target_dir . basename($_FILES['image']['name']);
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    $image_name = "profile_".$id.".".$imageFileType;
    $tmp_name = $_FILES['image']['tmp_name'];
    $imagePath = $target_dir . $image_name;
    move_uploaded_file($tmp_name, $imagePath);
    
    $avatar = "profile_".$id.".jpg";

    $sql = "UPDATE user SET avatar = '".$avatar."' WHERE id_user = '".$id."' ";
    $update = mysqli_query($conn, $sql);

    $data['sql'] = $sql;
    
    echo json_encode($data);
