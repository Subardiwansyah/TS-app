<?php

include "config.php";
$data = array();
$data['status'] = false;

$id = $_POST['id_user'];
$new = $_POST['password'];
$confirm = $_POST['confirm'];

if($new != $confirm){
	$data['mssg'] = "Password do not match.";
} else if(strlen($new) < 5) {
	$data['mssg'] = "Must be more than 5 charater.";
} else {
	$data['status'] = true;
	$sql = "UPDATE user SET password = '".password_hash($_POST['password'], PASSWORD_DEFAULT)."' WHERE id_user = '".$id."' ";
	$update = mysqli_query($conn,$sql);
}

echo json_encode($data);