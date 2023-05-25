<?php

include "config.php";
$data = array();

$id = $_POST["id"];

$sql = "SELECT * FROM kecamatan WHERE id_kab = '".$id."' ";

$query = mysqli_query($conn, $sql);

$list = array();

while($row = mysqli_fetch_assoc($query)){
	$list[] = $row;
}

$data = $list;

echo json_encode($data);