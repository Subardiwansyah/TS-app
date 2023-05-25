<?php

include "config.php";
$data = array();

$fullName = $_POST['fullname'];
$phoneNumber = $_POST['phonenumber'];
$emailAddress = $_POST['emailaddress'];
$address = $_POST['address'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$confirmPassword = $_POST['confirmpassword'];

$data['status'] = false;

//generate no user
$val_role = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM role WHERE id_role='4'"));
$val = mysqli_fetch_array(mysqli_query($conn,"SELECT max(no_user) as no_user FROM user WHERE id_role='4'"));
$urutan = (int) substr($val['no_user'], -4);
$urutan++;
$no_user = $val_role['singkatan'].date("y").date("m").sprintf("%04s", $urutan);

if(!is_numeric($phoneNumber)){
	$data['mssg'] = "Invalid phone number";
}
else if(!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)){
	$data['mssg'] = "Invalid email format";
} else if($_POST['password'] != $_POST['confirmpassword']){
	$data['mssg'] = "Error confirm password";
} else {
	$data['status'] = true;
}

if($data['status']){
	


$sql = "INSERT INTO user 
		(
			no_user,
			phone,
			email,
			username,
			address,
			nama,
			password,
			status,
			id_role
		) VALUES (
			'".$no_user."',
			'".$phoneNumber."',
			'".$emailAddress."',
			'".$emailAddress."',
			'".$address."',
			'".$fullName."',
			'".$password."',
			'1',
			'4' 
		)";
$data['phone'] = $phoneNumber;
$data['cust_no'] = $no_user;

$insert = mysqli_query($conn, $sql);
}

echo json_encode($data);

?>