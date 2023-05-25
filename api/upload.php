<?php

include "config.php";
$data = array();
$data['status'] = false;

$image = $_FILES['image']['name'];
$images = $_FILES['images']['name'];
$faulty = $_POST['faulty'];
$faulty_code = $_POST['faulty_code'];
$sku = $_POST['sku'];
$sn = $_POST['sn'];
$id = $_POST['id'];

$target_dir = "../uploads/";
//$target_dir = "upload/";

if($faulty == ""){
	$data["mssg"] = "Faulty Description is empty.";
} else if($faulty_code == ""){
	$data["mssg"] = "Faulty Code is empty.";
} else if($image == ""){
	$data["mssg"] = "Product image is empty.";
} else if($images == ""){
	$data["mssg"] = "Purchase Bill/Receipt is empty";
} else {

	$cek_claim = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM wty_claim WHERE sku = '".$sku."' and serial_number = '".$sn."' "));

	if(empty($cek_claim) or $cek_claim['closed'] == '1'){
		
		$val = mysqli_fetch_array(mysqli_query($conn,"SELECT max(case_no) as case_no FROM wty_claim "));
		$urutan = (int) substr($val['case_no'], -4);
		$urutan++;
		$case_no = date("y").date("m").sprintf("%04s", $urutan);

		for($i = 0; $i < 2; $i++){
			
			if($i==0){
				$target_file = $target_dir . basename($_FILES['image']['name']);
				$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
				$image_name = "product_".$case_no.".".$imageFileType;
				$image1 = $image_name;
				$tmp_name = $_FILES['image']['tmp_name'];
				$imagePath = $target_dir . $image_name;
				
			} else {
				$target_file = $target_dir . basename($_FILES['images']['name']);
				$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
				$image_name = "billing_".$case_no.".".$imageFileType;
				$image2 = $image_name;
				$tmp_name = $_FILES['images']['tmp_name'];
				$imagePath = $target_dir . $image_name;
				
			}
			move_uploaded_file($tmp_name, $imagePath);
			
		}

		$data['image'] = $image;
		$data['faulty'] = $faulty;
		$data['sku'] = $sku;
		$data['id'] = $id;

		$get = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM user WHERE id_user = '".$id."' "));
		$get_wty = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM wty WHERE sku = '".$sku."' and  serial_number = '".$sn."' "));
		
		$data['status'] = true;
		$data['img'] = $_FILES['image']['tmp_name'];
		
		$ins_serial_number = $get_wty['serial_number'];
		if($cek_claim['id_case_status'] == '6' OR $cek_claim['id_case_status'] == '7'){
			$ins_serial_number = $cek_claim['serial_number_new'];
		}

		$sql = "INSERT INTO wty_claim (
					case_no,
					customer_no,
					full_name,
                    phone,
                    email,
					id_store,
					nama_store,
					sku,
					serial_number,
					faulty_remark,
					faulty_name,
					id_case_status,
					type_case,
					image1,
					image2,
					created_date,
                    updated_date,
                    created_by,
					updated_by
				) VALUES (
					'".$case_no."',
					'".$get['no_user']."',
					'".$get['nama']."',
                    '".$get['phone']."',
                    '".$get['email']."',
					'".$get_wty['id_store']."',
					'".$get_wty['nama_store']."',
					'".$get_wty['sku']."',
					'".$ins_serial_number."',
					'".$faulty."',
					'".$faulty_code."',
					'1',
					'C.Claim',
					'".$image1."',
					'".$image2."',
					'".date('Y-m-d H:i:s')."',
                    '".date('Y-m-d H:i:s')."',
                    '".$get['nama']."',
					''
				)";	
		$data['sql'] = $sql;
		$insert = mysqli_query($conn, $sql);
	} else {
		$data['mssg'] = "Item already claimed.";
	}
}

echo json_encode($data);

?>
