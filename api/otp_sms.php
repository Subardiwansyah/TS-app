<?php
include "config.php";
$data = array();
$data['status'] = false;
$id_user 		= $_POST['id_user'];
$pin 		= $_POST['pin'];

$sql = "SELECT * FROM user_token WHERE pin_token = '".$pin."' and id_user = '".$id_user."' and action = 'phone' order by id DESC";
$cek_token = mysqli_fetch_array(mysqli_query($conn, $sql));

if(!empty($cek_token)){
	$data['status'] = true;
	$cek 		= "SELECT * FROM user WHERE id_user = '".$id_user."' ";
	$result = mysqli_fetch_array(mysqli_query($conn, $cek));
    
    $update = mysqli_query($conn,"UPDATE user SET verified = '1' WHERE id_user = '".$id_user."' ");
    
	$data['mssg']	= "Berhasil";
	$data['id']		= $result['id_user'];
	$data['nama']	= $result['nama'];
	$data['phone']	= $result['phone'];
	$data['email']	= $result['email'];
	$data['address']	= $result['address'];
	
} else {
	$data['mssg'] = "OTP Code not valid.";
	$data['id'] = $_POST['id_user'];
	$data['otp'] = $_POST['pin'];
}

echo json_encode($data);

?>