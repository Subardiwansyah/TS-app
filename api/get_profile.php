<?php
    
    include "config.php";
    $data = array();
    $data['status'] = false;
    
    $id = $_POST['id'];
    
    $query = mysqli_query($conn, "SELECT nama, phone, email, address, avatar FROM user WHERE id_user = '".$id."' ");
    $list = array();
    while($row = mysqli_fetch_assoc($query)){
        $list[] = $row;
    }
    
    $data = $list;
    
    echo json_encode($data);
