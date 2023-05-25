<?php
/**
*	PHP Admin Template
*	Author		: Agus Prawoto Hadi
*	Website		: https://jagowebdev.com
*	Year		: 2021
*/

// Role 
$user = $_SESSION['user'];

$js[] = BASE_URL . 'public/vendors/bootstrap-datepicker/js/bootstrap-datepicker.js';
$js[] = BASE_URL . 'public/themes/modern/js/date-picker.js';
$js[] = BASE_URL . 'public/themes/modern/js/image-upload.js';
$js[] = BASE_URL . 'public/vendors/datatables/datatables.min.js';
$js[] = BASE_URL . 'public/vendors/datatables/js/dataTables.bootstrap4.min.js';
$styles[] = BASE_URL . 'public/vendors/datatables/css/dataTables.bootstrap4.min.css';
$styles[] = BASE_URL . 'public/vendors/bootstrap-datepicker/css/bootstrap-datepicker3.css';

helper('registrasi');

switch ($_GET['action']) 
{
	default: 
		action_notfound();
		
	// INDEX 
	case 'index':
		$js[] = BASE_URL . 'public/themes/modern/js/warranty-card.js';
		$js[] = BASE_URL . 'public/themes/modern/js/data-tables-liveajax.js';
		cek_hakakses('read_data');
		
		$where="";
		if($user['id_role']==5){
			$where .=" WHERE a.id_wty>0 AND a.id_store='$user[id_store]' AND a.customer_no not like ''";
		}else{
			$where .=" WHERE a.id_wty>0 AND a.customer_no='$user[no_user]' AND a.customer_no not like ''";
		}
	
		$data['title'] = 'Warranty Card';
		$sql = 'SELECT a.*,
					b.nama_produk,
					c.unit_status,
					d.wty_status FROM
					wty a
					LEFT JOIN sku b 
					ON a.sku = b.sku
					LEFT JOIN unit_status c
					ON a.id_unit_status = c.id_unit_status
					LEFT JOIN wty_status d
					ON a.id_wty_status = d.id_wty_status
					LEFT JOIN user e
					ON a.customer_no = e.no_user
					LEFT JOIN wty_claim f
					ON a.sku = f.sku AND a.serial_number=f.serial_number
					LEFT JOIN case_status g
					ON f.id_case_status = g.id_case_status
					'. $where;
		//echo $sql;
		$data['result'] = $db->query($sql)->getResultArray();
		
		if (!$data['result']) {
			$data['msg']['status'] = 'error';
			$data['msg']['message'] = 'Data tidak ditemukan';
		}
		
		load_view('views/result.php', $data);
	
	case 'view': 
		
		//cek_hakakses('update_data','wty_claim');
		
		$where="";
		if($user['id_role']==5){
			$where .=" WHERE a.id_wty>0 AND a.id_store='$user[id_store]' AND a.customer_no not like ''";
		}else{
			$where .=" WHERE a.id_wty>0 AND a.customer_no='$user[no_user]' AND a.customer_no not like ''";
		}
		
		if (empty($_REQUEST['id'])) {
			$result['msg']['status'] = 'error';
			$result['msg']['message'] = 'Data warranty claim yang ingin diedit tidak ditemukan';
		} else {
			$sql = 'SELECT 	a.*,
							b.nama_produk,
							b.id_kategori,
							c.unit_status,
							d.wty_status,
							e.no_user as customer_no,
							e.nama as full_name,
							e.phone,
							e.email,
							f.image1,
							f.image2,
							f.case_no,
							f.id_product_return,
							f.id_case_status,
							f.faulty_name,
							f.faulty_remark,
							f.faulty_name_check,
							f.faulty_remark_check,
							f.serial_number_new,
							f.sku_new,
							f.closed,
							g.case_status FROM
					wty a
					LEFT JOIN sku b 
					ON a.sku = b.sku
					LEFT JOIN unit_status c
					ON a.id_unit_status = c.id_unit_status
					LEFT JOIN wty_status d
					ON a.id_wty_status = d.id_wty_status					
					LEFT JOIN user e
					ON a.customer_no = e.no_user					
					LEFT JOIN wty_claim f
					ON a.sku = f.sku AND a.serial_number=f.serial_number
					LEFT JOIN case_status g
					ON f.id_case_status = g.id_case_status
					 '.$where.' AND id_wty = ?';
			$result = $db->query($sql, trim($_REQUEST['id']))->row();
			
			$sql = 'SELECT 	count(*) as jml_data FROM
					wty a
					LEFT JOIN sku b 
					ON a.sku = b.sku
					LEFT JOIN unit_status c
					ON a.id_unit_status = c.id_unit_status
					LEFT JOIN wty_status d
					ON a.id_wty_status = d.id_wty_status					
					LEFT JOIN user e
					ON a.customer_no = e.no_user					
					LEFT JOIN wty_claim f
					ON a.sku = f.sku AND a.serial_number=f.serial_number
					LEFT JOIN case_status g
					ON f.id_case_status = g.id_case_status
					'. $where.' AND id_wty = ?';
			$jmlRow = $db->query($sql, trim($_REQUEST['id']))->row();
		}
		
		$data = $result;
		$data['result']=$jmlRow['jml_data'];
		if ($data['result']==0) {
			$data['msg']['status'] = 'error';
			$data['msg']['message'] = 'Data tidak ditemukan';
		}else{
			// List module faulty
			$sql = "SELECT * FROM faulty WHERE id_kategori='".intval($data['id_kategori'])."'";
			$data['faulty'] = $db->query($sql)->result();
			
			// List module status
			$sql = 'SELECT * FROM case_status WHERE aktif=1';
			$data['case_status'] = $db->query($sql)->result();
			
			// List module product_return
			$sql = 'SELECT * FROM product_return WHERE aktif=1';
			$data['product_return'] = $db->query($sql)->result();
			
			// List module reject_reason
			$sql = 'SELECT * FROM reject_reason WHERE aktif=1';
			$data['reject_reason'] = $db->query($sql)->result();
			
			// List module sku
			$sql = "SELECT * FROM sku WHERE aktif='1'";
			$data['data_sku'] = $db->query($sql)->result();
			
			// List module status
			$sql = "SELECT * FROM status";
			$data['data_status'] = $db->query($sql)->result();
		}
		
		$data['title'] = 'Edit ' . $current_module['judul_module'];
			
		// Submit
		$data['msg'] = [];
		if (isset($_POST['submit'])) 
		{
			$form_errors = validate_form();
			$error = false;
			
			if ($form_errors) {
				$data['msg']['message'] = $form_errors;
				$error = true;
			} else {
				
				// Assign fields table
				$fields = ['faulty_name','faulty_remark'];
				
				foreach ($fields as $field) {
					$data_db[$field] = $_POST[$field];
				}
				$no_case=no_case();
				
				$data_db['case_no'] = $no_case;
				$data_db['customer_no'] = $data['customer_no'];
				$data_db['full_name'] = $data['full_name'];
				$data_db['phone'] = $data['phone'];
				$data_db['email'] = $data['email'];
				$data_db['id_store'] = $data['id_store'];
				$data_db['nama_store'] = $data['nama_store'];
				$data_db['sku'] = $data['sku'];
				$data_db['serial_number'] = $data['serial_number'];
				
				if($_SESSION['user']['id_role']=="5"){
					$data_db['type_case'] = "D.Claim";
				}else{
					$data_db['type_case'] = "C.Claim";
				}
				$data_db['id_case_status'] = "1";
				$data_db['created_date'] = date('Y-m-d H:i:s');
				$data_db['created_by'] = $user['nama'];
				$data_db['updated_date'] = date('Y-m-d H:i:s');
				$data_db['updated_by'] = "";
				
				$path = $config['foto_path'];
				$new_name1 = "product_$no_case.".pathinfo(($_FILES['image1']['name']), PATHINFO_EXTENSION);
				$new_name2 = "billing_$no_case.".pathinfo(($_FILES['image2']['name']), PATHINFO_EXTENSION);
				
				if ($_POST['foto_delete_img1']) {
					$del = delete_file($path . @$data['image1']);
					$new_name1 = '';
					if (!$del) {
						$data['msg']['message'] = 'Gagal menghapus gambar lama';
						$error = true;
					}
				}
				
				if ($_POST['foto_delete_img2']) {
					$del = delete_file($path . @$data['image2']);
					$new_name2 = '';
					if (!$del) {
						$data['msg']['message'] = 'Gagal menghapus gambar lama';
						$error = true;
					}
				}
				
				if ($_FILES['image2']['name']) 
				{
					//old file
					if ($data['image2']) {
						$del = delete_file($path . @$data['image2']);
						if (!$del) {
							$data['msg']['message'] = 'Gagal menghapus gambar lama';
							$error = true;
						}
					}
					
					$new_name2 = upload_image($path, $_FILES['image2'], 300, 300,$new_name2);

					if (!$new_name2) {
						$data['msg']['message'] = 'Error saat memperoses gambar';
						$error = true;
					}
				}else{
					$data['msg']['message'] = 'Error Receipt belum dipilih';
					$error = true;
				}
				
				if ($_FILES['image1']['name']) 
				{
					//old file
					if ($data['image1']) {
						$del = delete_file($path . @$data['image1']);
						if (!$del) {
							$data['msg']['message'] = 'Gagal menghapus gambar lama';
							$error = true;
						}
					}
					
					$new_name1 = upload_image($path, $_FILES['image1'], 300, 300,$new_name1);

					if (!$new_name1) {
						$data['msg']['message'] = 'Error saat memperoses gambar';
						$error = true;
					}
				}else{
					$data['msg']['message'] = 'Error Photo Product belum dipilih';
					$error = true;
				}				
				
						
				if (!$error) {	
					$data_db['image1'] = $new_name1;
					$data_db['image2'] = $new_name2;
					
					$query = $db->insert('wty_claim', $data_db);
					
					$sql = 'SELECT a.*,
							b.nama_produk,
							b.id_kategori,
							c.unit_status,
							d.wty_status,
							e.no_user as customer_no,
							e.nama as full_name,
							e.phone,
							e.email,
							f.image1,
							f.image2,
							f.case_no,
							f.id_product_return,
							f.id_case_status,
							f.faulty_name,
							f.faulty_remark,
							f.faulty_name_check,
							f.faulty_remark_check,
							f.serial_number_new,
							f.sku_new,
							f.closed,
							g.case_status FROM
					wty a
					LEFT JOIN sku b 
					ON a.sku = b.sku
					LEFT JOIN unit_status c
					ON a.id_unit_status = c.id_unit_status
					LEFT JOIN wty_status d
					ON a.id_wty_status = d.id_wty_status					
					LEFT JOIN user e
					ON a.customer_no = e.no_user					
					LEFT JOIN wty_claim f
					ON a.sku = f.sku AND a.serial_number=f.serial_number
					LEFT JOIN case_status g
					ON f.id_case_status = g.id_case_status
					'. $where.' AND id_wty = ?';
					$result = $db->query($sql, trim($_REQUEST['id']))->row();
					
					$data = $result;
					$data['result']=1;
		
					// List module faulty
					$sql = "SELECT * FROM faulty WHERE id_kategori='".$data['id_kategori']."'";
					$data['faulty'] = $db->query($sql)->result();
					
					// List module status
					$sql = 'SELECT * FROM case_status WHERE aktif=1';
					$data['case_status'] = $db->query($sql)->result();
					
					// List module product_return
					$sql = 'SELECT * FROM product_return WHERE aktif=1';
					$data['product_return'] = $db->query($sql)->result();
					
					// List module reject_reason
					$sql = 'SELECT * FROM reject_reason WHERE aktif=1';
					$data['reject_reason'] = $db->query($sql)->result();
					
					// List module sku
					$sql = "SELECT * FROM sku WHERE aktif='1'";
					$data['data_sku'] = $db->query($sql)->result();
					
					// List module status
					$sql = "SELECT * FROM status";
					$data['data_status'] = $db->query($sql)->result();
					
					$data['title'] = 'Edit ' . $current_module['judul_module'];
			
					if ($query) {						
						$data['msg']['message'] = 'Data berhasil disimpan';
					} else {
						$data['msg']['message'] = 'Data gagal disimpan';
						$error = true;
					}
				}
			}
			
			$data['msg']['status'] = $error ? 'error' : 'ok';
		}
		
	load_view('views/form.php', $data);
	
	case 'add':
		cek_hakakses('create_data');
		
		$breadcrumb['Add'] = '';
	
		$data['title'] = 'Registration ' . $current_module['judul_module'];
		$data['msg'] = [];
		$data['found'] = 0;
		$error = false;
		helper('registrasi');

		if (isset($_POST['submit']) && $_POST['submit']=='find') 
		{
				$sql = "SELECT a.*,b.nama_produk,d.wty_status,e.unit_status FROM wty a 
				LEFT JOIN sku b 
					ON a.sku=b.sku 
				LEFT JOIN wty_status d
					ON a.id_wty_status = d.id_wty_status
				LEFT JOIN unit_status e
					ON a.id_unit_status = e.id_unit_status
				WHERE a.serial_number ='$_POST[serial_number]' AND a.sku='$_POST[sku]'";
				$result = $db->query($sql)->row();
				$data = $result; 
				
				if(!empty($data['sku'])){
					if(!empty($data['customer_no'])){
						$data['msg']['message'] = 'SKU dan Serial Number sudah didaftarkan';
						$data['found'] = 0;
						$error = true;	
					}else{
						if(strtotime($data['wty_end'])>=strtotime(date("Y-m-d"))){
						
								if($data['wty_status']=="Out"){	
									$data['msg']['message'] = 'Serial Number tidak dapat didaftarkan';
									$data['found'] = 0;
									$error = true;
								}else{
									if($_SESSION['user']['id_role']=="5"){
										if($_SESSION['user']['id_store']!=$data['id_store'] && $data['id_store']!="1"){
											$data['msg']['message'] = 'Serial Number tidak dapat didaftarkan karena beda store';
											$data['found'] = 0;
											$error = true;
										}else{
											$data['msg']['message'] = 'Serial Number dapat didaftarkan';
											$data['found'] = 1;
										}
									}else{
										$data['msg']['message'] = 'Serial Number dapat didaftarkan status unit $data[unit_status]';
										$data['found'] = 1;
									}										
									
								}
						}else{
							$data['msg']['message'] = 'Serial Number masa berlaku sudah habis';
							$data['found'] = 0;
							$error = true;
						}
					}									
				}else{
					$data['msg']['message'] = 'Serial Number tidak ditemukan';
					$data['found'] = 0;
					$error = true;
				}
				$data['msg']['status'] = $error ? 'error' : 'ok';
				$data['title'] = 'Registration ' . $current_module['judul_module'];
				
		}
		
		if (isset($_POST['submit']) && $_POST['submit']=='register') 
		{
			require_once('app/libraries/FormValidation.php');
			$validation = new FormValidation();
			$validation->setRules('bought_date', 'Bought Date', 'trim|required');			
			$valid = $validation->validate();
			
			$error = false;
			if (!$valid) {
				$data['msg']['status'] = 'error';
				$data['msg']['message'] = $validation->getMessage();
				$error = true;
			}
			
			if (!$error) {			
				$sql = "SELECT * FROM wty a 
								WHERE a.serial_number ='$_POST[serial_number2]' 
								AND a.sku='$_POST[sku2]'";
				$result = $db->query($sql)->row();
				$data = $result; 
				
				// Assign fields table
				$fields = ['bought_date'];
				
				foreach ($fields as $field) {
					$data_db[$field] = $_POST[$field];
				}

				$data_db['customer_no'] = $user['no_user'];
				
				if(!empty($data['customer_no'])){
					$data['msg']['message'] = 'Serial Number dapat didaftarkan';
					$error = true;
				}
				
				if (!$error) {					
					$query = $db->update('wty', $data_db, 'id_wty = ' . $data['id_wty']);					
				
					if ($query) {						
						$data['msg']['message'] = 'Data berhasil disimpan';
						header('location:'.module_url());
					} else {
						$data['msg']['message'] = 'Data gagal disimpan';
						$error = true;
					}
				}
			}
			
			// List module status
			$sql = 'SELECT * FROM case_status WHERE aktif=1';
			$data['case_status'] = $db->query($sql)->result();
			
			// List module product_return
			$sql = 'SELECT * FROM product_return WHERE aktif=1';
			$data['product_return'] = $db->query($sql)->result();
			
			// List module reject_reason
			$sql = 'SELECT * FROM reject_reason WHERE aktif=1';
			$data['reject_reason'] = $db->query($sql)->result();
			
			// List module sku
			$sql = "SELECT * FROM sku WHERE aktif='1'";
			$data['data_sku'] = $db->query($sql)->result();
			
			// List module status
			$sql = "SELECT * FROM status";
			$data['data_status'] = $db->query($sql)->result();
			
			$data['found'] = 1;
			$data['msg']['status'] = $error ? 'error' : 'ok';
			$data['title'] = 'Registration ' . $current_module['judul_module'];
		}
		load_view('views/form-add.php', $data);
		
	case 'getDataDT':
		
		$result['draw'] = $start = @$_POST['draw'] ?: 1;
		
		$data_table = getListData();
		$result['recordsTotal'] = $data_table['total_data'];
		$result['recordsFiltered'] = $data_table['total_filtered'];
				
		helper('html');
		$id_user = $_SESSION['user']['id_user'];
		
		$no = @$_POST['start'] + 1 ?: 1;
				
		foreach ($data_table['content'] as $key => &$val) 
		{			
			if($val['closed']=='1'){
				$val['closed']="Yes";
			}else{
				$val['closed']="No";
			}
			$val['ignore_search_urut'] = $no;
			$val['ignore_search_action'] = btn_action([
									'view' => ['url' => BASE_URL . $current_module['nama_module'] . '/view?id='. $val['id_wty']]
							]);
			
			$no++;
		}
					
		$result['data'] = $data_table['content'];
		echo json_encode($result); exit();
}


function getListData() {
	
	global $db;
	$columns = @$_POST['columns'];
	$order_by = '';
	$user = $_SESSION['user'];
	
	// Search
	$search_all = @$_POST['search']['value'];
	
	$where="";
	if($user['id_role']==5){
		$where .=" WHERE a.id_wty>0 AND a.id_store='$user[id_store]' AND a.customer_no not like ''";
	}else{
		$where .=" WHERE a.id_wty>0 AND a.customer_no='$user[no_user]' AND a.customer_no not like ''";
	}
	
	if ($search_all) {
		// Additional Search
		$columns[]['data'] = 'a.sku';
		foreach ($columns as $val) {
			
			if (strpos($val['data'], 'ignore_search') !== false) 
				continue;
			
			if (strpos($val['data'], 'ignore') !== false)
				continue;
				
			if($val['data']=='sku' || $val['data']=='serial_number'){
				$where_col[] = "upper(a.".$val['data'].")" . ' LIKE "%' . strtoupper($search_all) . '%"';
			}else{
				$where_col[] = "upper(".$val['data'].")" . ' LIKE "%' . strtoupper($search_all) . '%"';
			}
		}
		 $where .= ' AND (' . join(' OR ', $where_col) . ') ';
	}	
	
	// Order
	$start = @$_POST['start'] ?: 0;
	$length = @$_POST['length'] ?: 10;
	
	$order_data = @$_POST['order'];
	$order = '';
	if(isset($_POST['columns'])){		
		if (strpos($_POST['columns'][$order_data[0]['column']]['data'], 'ignore_search') === false) {
			$order_by = $columns[$order_data[0]['column']]['data'] . ' ' . strtoupper($order_data[0]['dir']);
			$order = ' ORDER BY ' . $order_by . ' LIMIT ' . $start . ', ' . $length;
		}
	}else{
		$order = ' ORDER BY a.sku DESC LIMIT 0, 10';
	}

	// Query Total
	$sql = 'SELECT count(*) AS jml_data FROM
					wty a
					LEFT JOIN sku b 
					ON a.sku = b.sku
					LEFT JOIN unit_status c
					ON a.id_unit_status = c.id_unit_status
					LEFT JOIN wty_status d
					ON a.id_wty_status = d.id_wty_status
					LEFT JOIN user e
					ON a.customer_no = e.no_user
					LEFT JOIN wty_claim f
					ON a.sku = f.sku AND a.serial_number=f.serial_number 
					LEFT JOIN case_status g
					ON f.id_case_status = g.id_case_status '.$where;
					
	$query = $db->query($sql)->getRowArray();
	$total_data = $query['jml_data'];
	
	// Query Filtered
	$sql = 'SELECT count(*) AS jml_data FROM
					wty a
					LEFT JOIN sku b 
					ON a.sku = b.sku
					LEFT JOIN unit_status c
					ON a.id_unit_status = c.id_unit_status
					LEFT JOIN wty_status d
					ON a.id_wty_status = d.id_wty_status
					LEFT JOIN user e
					ON a.customer_no = e.no_user 
					LEFT JOIN wty_claim f
					ON a.sku = f.sku AND a.serial_number=f.serial_number 
					LEFT JOIN case_status g
					ON f.id_case_status = g.id_case_status '.$where;
	$query = $db->query($sql)->getRowArray();
	$total_filtered = $query['jml_data'];
	
	// Query Data
	$sql = 'SELECT a.id_wty as id_wty, a.sku as sku,
					a.serial_number as serial_number,
					a.bought_date as bought_date,
					b.nama_produk as nama_produk,
					c.unit_status as unit_status,
					a.wty_end as wty_end,
					d.wty_status as wty_status,
					g.case_status as case_status,
					f.closed as closed FROM
					wty a
					LEFT JOIN sku b 
					ON a.sku = b.sku
					LEFT JOIN unit_status c
					ON a.id_unit_status = c.id_unit_status
					LEFT JOIN wty_status d
					ON a.id_wty_status = d.id_wty_status
					LEFT JOIN user e
					ON a.customer_no = e.no_user 
					LEFT JOIN wty_claim f
					ON a.sku = f.sku AND a.serial_number=f.serial_number 
					LEFT JOIN case_status g
					ON f.id_case_status = g.id_case_status ' . $where  . $order;
	//echo $sql;				
	$content = $db->query($sql)->getResultArray();
	
	return ['total_data' => $total_data, 'total_filtered' => $total_filtered, 'content' => $content];
}

function validate_form() {
	
	global $list_action;
	require_once('app/libraries/FormValidation.php');
	$validation = new FormValidation();
	$validation->setRules('faulty_name', 'Faulty Code', 'trim|required');
	$validation->setRules('faulty_remark', 'Faulty Remark', 'trim|required');	
	
	$validation->validate();
	$form_errors =  $validation->getMessage();
	
	if ($_FILES['image1']['name']) {
		
		$file_type = $_FILES['image1']['type'];
		$allowed = ['image/jpeg', 'image/jpg'];
		
		if (!in_array($file_type, $allowed)) {
			$form_errors['image1'] = 'Tipe file harus ' . join(', ', $allowed);
		}
		
		if ($_FILES['image1']['size'] > 30000 * 1024) {
			$form_errors['image1'] = 'Ukuran file maksimal 30MB';
		}
		
		$info = getimagesize($_FILES['image1']['tmp_name']);
		if ($info[0] < 100 || $info[1] < 100) { //0 Width, 1 Height
			$form_errors['image1'] = 'Dimensi file minimal: 100px x 100px';
		}
	}
	
	if ($_FILES['image2']['name']) {
		
		$file_type = $_FILES['image2']['type'];
		$allowed = ['image/jpeg', 'image/jpg'];
		
		if (!in_array($file_type, $allowed)) {
			$form_errors['image2'] = 'Tipe file harus ' . join(', ', $allowed);
		}
		
		if ($_FILES['image2']['size'] > 30000 * 1024) {
			$form_errors['image2'] = 'Ukuran file maksimal 30MB';
		}
		
		$info = getimagesize($_FILES['image2']['tmp_name']);
		if ($info[0] < 100 || $info[1] < 100) { //0 Width, 1 Height
			$form_errors['image2'] = 'Dimensi file minimal: 100px x 100px';
		}
	}
	
	return $form_errors;
}