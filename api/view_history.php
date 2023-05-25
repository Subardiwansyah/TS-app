<?php

    include "config.php";
    $data = array();
    $id = $_POST['id'];
    $id_wty = $_POST['id_wty'];

    $get = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM user WHERE id_user = '".$id."' "));

    $sql1 = "SELECT a.sku, a.serial_number, b.unit_status FROM wty a LEFT JOIN unit_status b ON a.id_unit_status = b.id_unit_status WHERE customer_no = '".$get['no_user']."' and id_parent = '".$id_wty."' ";
    $query1 = mysqli_query($conn, $sql1);

    $list = array();

    while($row = mysqli_fetch_assoc($query1)){
        $list[] = $row;
    }
    
    echo json_encode($list);

?>