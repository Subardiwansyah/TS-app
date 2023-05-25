<?php

include "config.php";
$data = array();
$id = $_POST['id'];

$get = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM user WHERE id_user = '".$id."' "));

$sql = "SELECT * FROM wty_claim a LEFT JOIN sku b ON a.sku = b.sku LEFT JOIN case_status c ON a.id_case_status = c.id_case_status LEFT JOIN product_return d ON a.id_product_return = d.id_product_return LEFT JOIN reject_reason e ON e.id_reject_reason = a.id_reject_reason WHERE customer_no = '".$get['no_user']."' ORDER BY a.created_date DESC ";

$query = mysqli_query($conn,$sql);

$list = array();

while($row = mysqli_fetch_assoc($query)){
	if($row['faulty_name_check'] == '0'){
		$row['faulty_name_check'] = '';
	}
	$list[] = $row;
	//array_push($list, $row);
}

echo json_encode($list);
