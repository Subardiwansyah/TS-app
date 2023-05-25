<?php

include "config.php";

$sql = "SELECT * FROM katalog ORDER BY created_date DESC";

$result = mysqli_query($conn, $sql);

$list = array();
$sum = mysqli_num_rows($result);
if( $sum > 0){
	while($row = mysqli_fetch_assoc($result)){
		//$list[] = $row;
		$row['image'] = "https://rma.techno-solution.biz/uploads/katalog/".$row["image"];
		array_push($list, $row);
	}
	echo json_encode(array(
        "success"=>true,
        "data"=> $list,
    ));
} else {
    echo json_encode(array(
        "success"=>false,
    ));
}