<?php 
include "config.php";
$data = array();

$data['status'] = false;

$id 		= $_POST['id'];
$password 	= $_POST['password'];

$sql 		= "SELECT * FROM user WHERE id_user = '".$id."' ";
$result		= mysqli_fetch_array(mysqli_query($conn, $sql));

if(password_verify($password, $result['password'])){
	$data['status'] = true;
} else {
	$data['mssg']	= "Your password is wrong.";
}

echo json_encode($data);