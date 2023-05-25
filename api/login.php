<?php 
include "config.php";
$data = array();

$email 		= $_POST['username'];
$password	= $_POST['password'];
$cek 		= "SELECT * FROM user WHERE (email = '$email' OR phone = '$email')";

$result = mysqli_fetch_array(mysqli_query($conn, $cek));

if(password_verify($password, $result['password'])){
	$data['status'] = true;
	$data['mssg']	= "Berhasil";
	$data['id']		= $result['id_user'];
	$data['nama']	= $result['nama'];
	$data['phone']	= $result['phone'];
	$data['email']	= $result['email'];
	$data['address']	= $result['address'];
} else {
	$data['status']	= false;
	$data['mssg']	= "Username atau Password Anda Salah.";
}

echo json_encode($data);

