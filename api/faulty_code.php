<?php

include "config.php";
$data = array();

$sql = "SELECT distinct(faulty_code) FROM faulty";

$query = mysqli_query($conn, $sql);

$list = array();

while($row = mysqli_fetch_assoc($query)){
	$list[] = $row;
}

$data = $list;

echo json_encode($data);
