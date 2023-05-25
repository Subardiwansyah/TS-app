<?php
include "config.php";
$data = array();

$id = $_POST['id'];
$sku = $_POST['sku'];
$sn = $_POST['sn'];
$boughtDate = $_POST['boughtDate'];
    
$data['status'] = true;
    $data['mssg'] = '';
    
    $get = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM user WHERE id_user = '".$id."' "));
    
    $cek_card = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM wty WHERE sku = '".$sku."' AND serial_number = '".$sn."' AND customer_no = '".$get['no_user']."' "));


    if($cek_card['id_wty_status'] == '2'){
        $data['status'] = false;
        $data['mssg'] = 'Out Warranty.';
    } else {
        $data['status'] = true;
    }
    
echo json_encode($data);
