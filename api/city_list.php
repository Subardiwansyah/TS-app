<?php

include "config.php";
$data = array();

$id = $_POST["id"];
$sql = "SELECT * FROM kabupaten WHERE id_prov = '".$id."' ";
$query = mysqli_query($conn, $sql);

$list = array();

while($row = mysqli_fetch_assoc($query)){
	$list[] = $row;
}

$data = $list;

echo json_encode($data);