
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

$js[] ="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js";
$js[] ="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js";
$js[] ="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js";
$js[] ="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js";
$js[] ="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js";

$styles[] = "https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css";

$user = $_SESSION['user'];
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
	
	case 'add':
		cek_hakakses('create_data');
		
		$breadcrumb['Add'] = '';
	
		$data['title'] = 'Tambah ' . $current_module['judul_module'];
		
		// List module status
		$sql = 'SELECT * FROM status';
		$data['status'] = $db->query($sql)->result();
	
		$data['msg'] = [];
		$error = false;
		if (isset($_POST['submit'])) 
		{
			require_once('app/libraries/FormValidation.php');
			$validation = new FormValidation();
			$validation->setRules('name', 'Nama Produk', 'trim|required');
			$validation->setRules('description', 'Deskripsi', 'trim|required');
			$valid = $validation->validate();
			
			if (!$valid) {
				$data['msg']['status'] = 'error';
				$data['msg']['message'] = $validation->getMessage();
				$error = true;
			}
			
			if (!$error) {
				
				$fields = ['name', 'description','price','show_price','price_promo','promo','enabled'];
				foreach ($fields as $field) {
					$data_db[$field] = $_POST[$field];
				}
				if($data_db['promo']==""){
					$data_db['promo']="N";
				}else{
					$data_db['promo']=$data_db['promo'];
				}
				
				$path = $config['produk_images_path'];
				
				
				if ($_FILES['avatar']['name']) 
				{
					$ext = end((explode(".", $_FILES['avatar']['name'])));
					$new_name = upload_image($path, $_FILES['avatar'], 300, 300,"IMG".date("YmdHis")."_".substr(md5(rand()), 0, 7).".$ext");

					if (!$new_name) {
						$data['msg']['message'] = 'Error saat memperoses gambar';
						$error = true;
					}
				}
				
				$data_db['image'] = $new_name;
				$data_db['rating'] = "4.5";
				$data_db['colors'] = "";
				$data_db['created_date'] = date('Y-m-d H:i:s');
				$data_db['created_by'] = $user['nama'];	
				$data_db['updated_date'] = date('Y-m-d H:i:s');
				$data_db['updated_by'] = $user['nama'];
				$query = $db->insert('katalog', $data_db);

				if ($query) {
					$data['msg']['status'] = 'ok';
					$data['msg']['message'] = 'Data berhasil disimpan';
					header('location:'.module_url());
				} else {
					$data['msg']['status'] = 'error';
					$data['msg']['message'] = 'Data gagal disimpan';
				}
				
				$data['title'] = 'Edit Data Katalog';
				
			}
		}
		load_view('views/form.php', $data);
		
	case 'edit': 
		
		cek_hakakses('update_data');
		
		if (empty($_REQUEST['id'])) {
			$result['msg']['status'] = 'error';
			$result['msg']['message'] = 'Data katalog yang ingin diedit tidak ditemukan';
		} else {
			$sql = 'SELECT * FROM katalog WHERE id_katalog = ?';
			$result = $db->query($sql, trim($_REQUEST['id']))->row();
		}
		
		if (!$result)
			data_notfound();
			
		$data = $result;
		
		$data['title'] = 'Edit ' . $current_module['judul_module'];
		$breadcrumb['Edit'] = '';
		
		// List module status
		$sql = 'SELECT * FROM status';
		$data['status'] = $db->query($sql)->result();
		
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
				$fields = ['name', 'description','price','show_price','price_promo','promo','enabled'];
				
				foreach ($fields as $field) {
					$data_db[$field] = $_POST[$field];
				}
				if($data_db['promo']==""){
					$data_db['promo']="N";
				}else{
					$data_db['promo']=$data_db['promo'];
				}
				
				$sql = 'SELECT image FROM katalog WHERE id_katalog = ?';
				$result = $db->query($sql, trim($_GET['id']))->row();
				$img_db = $result;
		
				$path = $config['produk_images_path'];
				$new_name = $img_db['image'];
				
				if ($_POST['avatar_delete_img']) {
					$del = delete_file($path . $img_db['image']);
					$new_name = '';
					if (!$del) {
						$data['msg']['message'] = 'Gagal menghapus gambar lama';
						$error = true;
					}
				}
				
				if ($_FILES['avatar']['name']) 
				{
					//old file
					if ($img_db['image']) {
						$del = delete_file($path . $img_db['image']);
						if (!$del) {
							$data['msg']['message'] = 'Gagal menghapus gambar lama';
							$error = true;
						}
					}
					$ext = end((explode(".", $_FILES['avatar']['name'])));
					$new_name = upload_image($path, $_FILES['avatar'], 300, 300,"IMG".date("YmdHis")."_".substr(md5(rand()), 0, 7).".$ext");

					if (!$new_name) {
						$data['msg']['message'] = 'Error saat memperoses gambar';
						$error = true;
					}
				}
				
				if (!$error) {
					$data_db['image'] = $new_name;					
		
					$data_db['updated_date'] = date('Y-m-d H:i:s');
					$data_db['updated_by'] = $user['nama'];
					$query = $db->update('katalog', $data_db, 'id_katalog = ' . $_POST['id']);
					if ($query) {
						$data['msg']['message'] = 'Data berhasil disimpan';
						echo "<script> window.location.href = '".BASE_URL . $current_module['nama_module'] . '/edit?id='. $_POST['id']."';</script>";
						//header('location:'.module_url());
					} else {
						$data['msg']['message'] = 'Data gagal disimpan';
						$error = true;
					}
				}
			}
			
			$data['msg']['status'] = $error ? 'error' : 'ok';
		}
				
		load_view('views/form.php', $data);
		
	case 'view': 
		
		cek_hakakses('update_data');
		
		if (empty($_REQUEST['id'])) {
			$result['msg']['status'] = 'error';
			$result['msg']['message'] = 'Data katalog yang ingin diedit tidak ditemukan';
		} else {
			$sql = 'SELECT * FROM katalog WHERE id_katalog = ?';
			$result = $db->query($sql, trim($_REQUEST['id']))->row();
		}
		
		if (!$result)
			data_notfound();
			
		$data = $result;
		
		$data['title'] = 'Edit ' . $current_module['judul_module'];
		$breadcrumb['Edit'] = '';
		
		// List module status
		$sql = 'SELECT * FROM status';
		$data['status'] = $db->query($sql)->result();
		
		// Submit
		$data['msg'] = [];
				
		load_view('views/form-view.php', $data);
		
	case 'getDataDT':
		
		$result['draw'] = $start = $_POST['draw'] ?: 1;
		
		$data_table = getListData();
		$result['recordsTotal'] = $data_table['total_data'];
		$result['recordsFiltered'] = $data_table['total_filtered'];
				
		helper('html');
		//$id_katalog = $_SESSION['katalog']['id_katalog'];
		
		$no = $_POST['start'] + 1 ?: 1;
		$enabled = [0 => 'No', 1 => 'Yes'];
		$promo = ['N' => 'No', 'Y' => 'Yes'];
		$show_price = ['N' => 'No', 'Y' => 'Yes'];
				
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
			$val['promo']=$promo[$val['promo']];
			$val['show_price']=$show_price[$val['show_price']];
			
			$val['ignore_search_urut'] = $no;
			$val['ignore_search_action'] = btn_action([
									'edit' => ['url' => BASE_URL . $current_module['nama_module'] . '/edit?id='. $val['id_katalog']]
									, 'delete' => ['url' => ''
												, 'id' =>  $val['id_katalog']
												, 'delete-title' => 'Hapus data katalog: <strong>'.$val['name'].'</strong> ?'
											]
									,'view' => ['url' => BASE_URL . $current_module['nama_module'] . '/view?id='. $val['id_katalog']]
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
		if($length>0){
			$order = 'ORDER BY ' . $order_by . ' LIMIT ' . $start . ', ' . $length;
		}else{
			$order = 'ORDER BY ' . $order_by;
		}
	}

	// Query Total
	$sql = 'SELECT COUNT(*) AS jml_data FROM katalog ';
	$query = $db->query($sql)->getRowArray();
	$total_data = $query['jml_data'];
	
	// Query Filtered
	$sql = 'SELECT COUNT(*) AS jml_data FROM katalog '. $where;
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
		$allowed = ['image/jpeg', 'image/jpg'];
		
		if (!in_array($file_type, $allowed)) {
			$form_errors['avatar'] = 'Tipe file harus ' . join(', ', $allowed);
		}
		
		if ($_FILES['avatar']['size'] > 3000 * 1024) {
			$form_errors['avatar'] = 'Ukuran file maksimal 3Mb';
		}
		
		$info = getimagesize($_FILES['avatar']['tmp_name']);
		if ($info[0] < 100 || $info[1] < 100) { //0 Width, 1 Height
			$form_errors['avatar'] = 'Dimensi file minimal: 100px x 100px';
		}
	}
	
	return $form_errors;
}