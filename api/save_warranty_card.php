<?php 
include "config.php";
$data = array();

$id = $_POST['id'];
$sku = $_POST['sku'];
$sn = $_POST['sn'];
$boughtDate = $_POST['boughtDate'];

$data['id'] = $id;
$data['sku'] = $sku;
$data['sn'] = $sn;
$data['boughtDate'] = $_POST['boughtDate'];
$data['status'] = false;
$get = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM user WHERE id_user = '".$id."' "));

$cek_card = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM wty WHERE sku = '".$sku."' AND serial_number = '".$sn."' "));
$data['ids'] = $id;
if(empty($cek_card['customer_no'])){
	$sql = "UPDATE wty SET customer_no = '".$get['no_user']."', updated_date = '".date('Y-m-d h:i:s')."' WHERE sku = '".$sku."' and serial_number = '".$sn."' ";
	$update = mysqli_query($conn, $sql);
	$data['sql'] = $sql;
	$data['custo'] = true;
	$data['status'] = true;
} else {
	$data['mssg'] = "Warranty card already registered.";
}
echo json_encode($data);
