<?php

include "config.php";
$data = array();

$sku = $_POST['sku'];
$sn = $_POST['sn'];

$list = array();
$list['status'] = false;

#$sql = "SELECT * FROM wty_claim a LEFT JOIN sku b ON a.sku = b.sku LEFT JOIN wty c ON a.sku = c.sku WHERE a.sku = '".$sku."' and a.serial_number = '".$sn."' ";

$sql = "SELECT * FROM wty a LEFT JOIN sku b ON a.sku = b.sku WHERE a.sku = '".$sku."' and a.serial_number = '".$sn."' ";

$result = mysqli_fetch_array(mysqli_query($conn, $sql));

if(!empty($result)){
	$list['status'] 		= true;
	$list['sku'] 			= $result['sku'];
	$list['store'] 			= $result['nama_store'];
	$list['serial_number'] 	= $result['serial_number'];
	$list['product'] 		= $result['nama_produk'];
	$list['bought_date'] 	= $result['bought_date'] == "0000-00-00" ? "" : tgl_indo($result['bought_date']);
	$list['warranty_end'] 	= tgl_indo($result['wty_end']);
	$list['warranty_start'] = tgl_indo($result['wty_start']);
	$list['warranty_status']= $result['id_wty_status'];
} else {
	$list['mssg'] = "Serial Number dan SKU tidak terdaftar.";
	$list['sql'] = $sql;
}

echo json_encode($list);