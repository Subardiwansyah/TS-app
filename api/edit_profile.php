<?php

include "config.php";
$data = array();

$id = $_POST['id'];
$nama = $_POST['nama'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$address = $_POST['address'];

$sql = "UPDATE user SET phone = '".$phone."', address = '".$address."', email = '".$email."', username = '".$email."', nama = '".$nama."' WHERE id_user = '".$id."' ";

$update = mysqli_query($conn, $sql);

$data['sql'] = $sql;

echo json_encode($data);

?>