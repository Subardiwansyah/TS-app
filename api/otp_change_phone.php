<?php 
include "config.php";
$data = array();
$data['status'] = false;

$id_user 		= $_POST['id_user'];
$pin 		= $_POST['pin'];
$newPhone   = $_POST['newPhone'];

$sql = "SELECT * FROM user_token WHERE pin_token = '".$pin."' and id_user = '".$id_user."' and action = 'phonechange' ORDER BY id DESC";
$cek_token = mysqli_fetch_array(mysqli_query($conn, $sql));

if(!empty($cek_token)){
	$data['status'] = true;
    $data['mssg'] = "Berhasil.";

    $sqlu = "UPDATE user SET phone = '".$newPhone."' WHERE id_user = '".$id_user."' ";
    $update = mysqli_query($conn,$sqlu);

} else {
	$data['mssg'] = "OTP Code not valid.";
}

echo json_encode($data);