
<?php
/**
*	PHP Admin Template
*	Author		: Agus Prawoto Hadi
*	Website		: https://jagowebdev.com
*	Year		: 2021
*/

$js[] = BASE_URL . 'public/vendors/bootstrap-datepicker/js/bootstrap-datepicker.js';
$js[] = BASE_URL . 'public/themes/modern/js/date-picker.js';
$js[] = BASE_URL . 'public/themes/modern/js/image-upload.js';
$js[] = BASE_URL . 'public/vendors/datatables/datatables.min.js';
$js[] = BASE_URL . 'public/vendors/datatables/js/dataTables.bootstrap4.min.js';
$styles[] = BASE_URL . 'public/vendors/datatables/css/dataTables.bootstrap4.min.css';
$styles[] = BASE_URL . 'public/vendors/bootstrap-datepicker/css/bootstrap-datepicker3.css';
		
switch ($_GET['action']) 
{
	default: 
		action_notfound();
		
	// INDEX 
	case 'index':
		$js[] = BASE_URL . 'public/themes/modern/js/data-tables-ajax.js';
		cek_hakakses('read_data');

		if (!empty($_POST['delete'])) 
		{
			cek_hakakses('delete_data');
			
			$result = $db->delete('katalog', ['id_katalog' => $_POST['id']]);
			// $result = true;
			if ($result) {
				$data['msg'] = ['status' => 'ok', 'message' => 'Data katalog berhasil dihapus'];
			} else {
				$data['msg'] = ['status' => 'error', 'message' => 'Data katalog gagal dihapus'];
			}
		}
		
		$data['title'] = 'Data Katalog';
		$sql = 'SELECT * FROM katalog';
		
		$data['result'] = $db->query($sql)->getResultArray();
		
		if (!$data['result']) {
			$data['msg']['status'] = 'error';
			$data['msg']['message'] = 'Data tidak ditemukan';
		}
		
		$data['form'] = load_view('views/form-cari.php', $data, true);
		load_view('views/result.php', $data);
		
	case 'getDataDT':
		
		$result['draw'] = $start = $_POST['draw'] ?: 1;
		
		$data_table = getListData();
		$result['recordsTotal'] = $data_table['total_data'];
		$result['recordsFiltered'] = $data_table['total_filtered'];
				
		helper('html');
		//$id_katalog = $_SESSION['katalog']['id_katalog'];
		
		$no = $_POST['start'] + 1 ?: 1;
		$enabled = [0 => 'Tidak', 1 => 'Ya'];
				
		foreach ($data_table['content'] as $key => &$val) 
		{
			if ($val['image']) {
				if (file_exists($config['produk_images_path'] . $val['image'])) {
					$image = BASE_URL . $config['produk_images_path'] . $val['image'];
				} else {
					$image = BASE_URL . $config['produk_images_path'] . $val['image'];
				}
			} else {
				$image = BASE_URL . $config['produk_images_path'] . 'default.png';
			}
			
			$val['image'] = '<div class="list-foto"><img src="' . $image . '?r=' . time() . '"/></div>';
			$val['enabled']=$enabled[$val['enabled']];
			
			$val['ignore_search_urut'] = $no;
			$val['ignore_search_action'] = btn_action([
									'edit' => ['url' => BASE_URL . $current_module['nama_module'] . '/edit?id='. $val['id_katalog']]
								, 'delete' => ['url' => ''
												, 'id' =>  $val['id_katalog']
												, 'delete-title' => 'Hapus data katalog: <strong>'.$val['name'].'</strong> ?'
											]
							]);
			
			$no++;
		}
					
		$result['data'] = $data_table['content'];
		echo json_encode($result); exit();
}

function getListData() {
	
	global $db;
	$columns = $_POST['columns'];
	$order_by = '';
	
	// Search
	$search_all = @$_POST['search']['value'];
	$where = where_own();
	if ($search_all) {
		// Additional Search
		$columns[]['data'] = 'name';
		foreach ($columns as $val) {
			
			if (strpos($val['data'], 'ignore_search') !== false) 
				continue;
			
			if (strpos($val['data'], 'ignore') !== false)
				continue;
			
			$where_col[] = $val['data'] . ' LIKE "%' . $search_all . '%"';
		}
		 $where .= ' AND (' . join(' OR ', $where_col) . ') ';
	}
	
	// Order

	$start = $_POST['start'] ?: 0;
	$length = $_POST['length'] ?: 10;
	
	$order_data = $_POST['order'];
	$order = '';
	if (strpos($_POST['columns'][$order_data[0]['column']]['data'], 'ignore_search') === false) {
		$order_by = $columns[$order_data[0]['column']]['data'] . ' ' . strtoupper($order_data[0]['dir']);
		$order = 'ORDER BY ' . $order_by . ' LIMIT ' . $start . ', ' . $length;
	}

	// Query Total
	$sql = 'SELECT COUNT(*) AS jml_data FROM katalog ';
	$query = $db->query($sql)->getRowArray();
	$total_data = $query['jml_data'];
	
	// Query Filtered
	$sql = 'SELECT COUNT(*) AS jml_data FROM katalog ';
	$query = $db->query($sql)->getRowArray();
	$total_filtered = $query['jml_data'];
	
	// Query Data
	$sql = 'SELECT * FROM katalog ' . $where  . $order;
	$content = $db->query($sql)->getResultArray();
	
	return ['total_data' => $total_data, 'total_filtered' => $total_filtered, 'content' => $content];
}

function validate_form() {
	
	global $list_action;
	require_once('app/libraries/FormValidation.php');
	$validation = new FormValidation();
	$validation->setRules('name', 'Nama Produk', 'trim|required');
	$validation->setRules('description', 'Deskripsi', 'trim|required');
	
	$validation->validate();
	$form_errors =  $validation->getMessage();
			
	if ($_FILES['avatar']['name']) {
		
		$file_type = $_FILES['avatar']['type'];
		$allowed = ['image/png', 'image/jpeg', 'image/jpg'];
		
		if (!in_array($file_type, $allowed)) {
			$form_errors['avatar'] = 'Tipe file harus ' . join(', ', $allowed);
		}
		
		if ($_FILES['avatar']['size'] > 300 * 1024) {
			$form_errors['avatar'] = 'Ukuran file maksimal 300Kb';
		}
		
		$info = getimagesize($_FILES['avatar']['tmp_name']);
		if ($info[0] < 100 || $info[1] < 100) { //0 Width, 1 Height
			$form_errors['avatar'] = 'Dimensi file minimal: 100px x 100px';
		}
	}
	
	return $form_errors;
}