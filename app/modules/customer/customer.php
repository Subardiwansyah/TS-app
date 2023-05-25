
<?php
/**
*	PHP Admin Template
*	Author		: Agus Prawoto Hadi
*	Website		: https://jagowebdev.com
*	Year		: 2021
*/

// Role 
$sql = 'SELECT * FROM role';
$role = $db->query($sql)->result();
$data['role'] = $role;

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
			
			$result = $db->delete('user', ['id_user' => $_POST['id']]);
			// $result = true;
			if ($result) {
				$data['msg'] = ['status' => 'ok', 'message' => 'Data user berhasil dihapus'];
			} else {
				$data['msg'] = ['status' => 'error', 'message' => 'Data user gagal dihapus'];
			}
		}
		
		$data['title'] = 'Data Customer';
		$sql = 'SELECT * FROM user LEFT JOIN role USING(id_role)' . where_own()." and id_role = '4'";
		
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
		$data['msg'] = [];
		$error = false;
		if (isset($_POST['submit'])) 
		{
			require_once('app/libraries/FormValidation.php');
			$validation = new FormValidation();
			$validation->setRules('username', 'Username', 'trim|required|unique[user]');
			$validation->setRules('nama', 'Nama', 'trim|required');
			$validation->setRules('phone', 'Phone', 'trim|required');
			$validation->setRules('email', 'Email', 'trim|required|valid_email');
			$validation->setRules('password', 'Password', 'trim|required|min_length[3]');
			$valid = $validation->validate();
			
			if (!$valid) {
				$data['msg']['status'] = 'error';
				$data['msg']['message'] = $validation->getMessage();
				$error = true;
			} 
			
			if ($_POST['password'] !== $_POST['ulangi_password']) {
				$data['msg']['status'] = 'error';
				$data['msg']['message'][] = 'Password baru dengan ulangi password baru tidak sama'; 
				$error = true;
			}
			
			if (!$error) {
				
				$fields = ['username', 'nama', 'email','phone','address'];
				foreach ($fields as $field) {
					$data_db[$field] = $_POST[$field];
				}
				$data_db['id_role'] = 4;
				$data_db['verified'] = 1; 
				$data_db['status'] = 1;
				$data_db['created'] = date('Y-m-d H:i:s');
				$data_db['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);				
				
				$query = $db->insert('user', $data_db);

				if ($query) {
					$data['msg']['status'] = 'ok';
					$data['msg']['message'] = 'Data berhasil disimpan';
					header('location:'.module_url());
				} else {
					$data['msg']['status'] = 'error';
					$data['msg']['message'] = 'Data gagal disimpan';
				}
				
				$data['title'] = 'Tambah Data Customer';
				
			}
		}
		load_view('views/form-add.php', $data);
		
	case 'edit': 
		
		cek_hakakses('update_data');
		
		if (empty($_REQUEST['id'])) {
			$result['msg']['status'] = 'error';
			$result['msg']['message'] = 'Data user yang ingin diedit tidak ditemukan';
		} else {
			$sql = 'SELECT * FROM user WHERE id_user = ?';
			$result = $db->query($sql, trim($_REQUEST['id']))->row();
		}
		
		if (!$result)
			data_notfound();
			
		$data = $result;
				
		$data['title'] = 'Edit ' . $current_module['judul_module'];
		
		// List module status
		$sql = 'SELECT * FROM status';
		$data['status'] = $db->query($sql)->result();
		
		$breadcrumb['Edit'] = '';
			
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
				$fields = ['nama', 'email','phone','address', 'verified'];
				
				foreach ($fields as $field) {
					$data_db[$field] = $_POST[$field];
				}
				
				if(!empty($_POST['password']) && !empty($_POST['ulangi_password'])){
					if ($_POST['password'] == $_POST['ulangi_password']) {
						$data_db['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
					}else{
						$data['msg']['message'] = 'Ulangi password tidak sama';
						$error = true;
					}
				}
				
				$sql = 'SELECT avatar FROM user WHERE id_user = ?';
				$result = $db->query($sql, trim($_GET['id']))->row();
				$img_db = $result;
		
				$path = $config['user_images_path'];
				$new_name = $img_db['avatar'];
				
				if ($_POST['avatar_delete_img']) {
					$del = delete_file($path . $img_db['avatar']);
					$new_name = '';
					if (!$del) {
						$data['msg']['message'] = 'Gagal menghapus gambar lama';
						$error = true;
					}
				}
				
				if ($_FILES['avatar']['name']) 
				{
					//old file
					if ($img_db['avatar']) {
						$del = delete_file($path . $img_db['avatar']);
						if (!$del) {
							$data['msg']['message'] = 'Gagal menghapus gambar lama';
							$error = true;
						}
					}
					
					$new_name = upload_image($path, $_FILES['avatar'], 300, 300);

					if (!$new_name) {
						$data['msg']['message'] = 'Error saat memperoses gambar';
						$error = true;
					}
				}
				
				if (!$error) {
					$data_db['avatar'] = $new_name;
					$query = $db->update('user', $data_db, 'id_user = ' . $_POST['id']);
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
		
		load_view('views/form-edit.php', $data);
		
	case 'view': 
		
		cek_hakakses('update_data');
		
		if (empty($_REQUEST['id'])) {
			$result['msg']['status'] = 'error';
			$result['msg']['message'] = 'Data user yang ingin diedit tidak ditemukan';
		} else {
			$sql = 'SELECT * FROM user WHERE id_user = ?';
			$result = $db->query($sql, trim($_REQUEST['id']))->row();
		}		
		
		if (!$result)
			data_notfound();
			
		$data = $result;
				
		$data['title'] = 'Edit ' . $current_module['judul_module'];
		
		// List module status
		$sql = 'SELECT * FROM status';
		$data['status'] = $db->query($sql)->result();
		
		$breadcrumb['Edit'] = '';
			
		// Submit
		$data['msg'] = [];		
		
		load_view('views/form-view.php', $data);
		
	case 'edit-password':
		
		$sql = 'SELECT * FROM user WHERE id_user = ?';
		$user = $db->query($sql, trim($_SESSION['user']['id_user']))->row();
		$data = $user;
		$data['title'] = 'Edit Password';
		$breadcrumb['Edit Password'] = '';
		
		// Submit
		$data['msg'] = [];
		if (isset($_POST['submit'])) 
		{
			require_once('app/libraries/FormValidation.php');
			$validation = new FormValidation();
			$validation->setRules('password_lama', 'Password Lama', 'trim|required');
			$validation->setRules('password_baru', 'Password Baru', 'trim|required');
			$validation->setRules('ulangi_password_baru', 'Ulangi Password Baru', 'trim|required');
			
			$valid = $validation->validate();
			
			$error = false;
			if (!$valid) {
				$data['msg']['status'] = 'error';
				$data['msg']['message'] = $validation->getMessage();
				$error = true;
			}
			
			if (!password_verify($_POST['password_lama'],$user['password'])) {
				$data['msg']['status'] = 'error';
				$data['msg']['message'][] = 'Password lama tidak cocok'; 
				$error = true;
			}
			
			if ($_POST['password_baru'] !== $_POST['ulangi_password_baru']) {
				$data['msg']['status'] = 'error';
				$data['msg']['message'][] = 'Password baru dengan ulangi password baru tidak sama'; 
				$error = true;
			}
			
			if (!$error) {
				$data_db['password'] = password_hash($_POST['password_baru'], PASSWORD_DEFAULT);

				// print_r($data_db); die;
				$query = $db->update('user', $data_db, 'id_user = ' . $user['id_user']);
				
				if ($query) {
					$data['msg']['status'] = 'ok';
					$data['msg']['message'] = 'Data berhasil diupdate';
				} else {
					$data['msg']['status'] = 'error';
					$data['msg']['message'] = 'Data gagal disimpan';
				}
				
				$data['title'] = 'Edit Password';
			}
		}
		load_view('views/form-edit-password.php', $data);
		
	case 'getDataDT':
		
		$result['draw'] = $start = $_POST['draw'] ?: 1;
		
		$data_table = getListData();
		$result['recordsTotal'] = $data_table['total_data'];
		$result['recordsFiltered'] = $data_table['total_filtered'];
				
		helper('html');
		$id_user = $_SESSION['user']['id_user'];
		
		$no = $_POST['start'] + 1 ?: 1;
		$verified = [0 => 'Tidak', 1 => 'Ya'];
				
		foreach ($data_table['content'] as $key => &$val) 
		{
			if ($val['avatar']) {
				if (file_exists($config['user_images_path'] . $val['avatar'])) {
					$avatar = BASE_URL . $config['user_images_path'] . $val['avatar'];
				} else {
					$avatar = BASE_URL . $config['user_images_path'] . $val['avatar'];
				}
			} else {
				$avatar = BASE_URL . $config['user_images_path'] . 'default.png';
			}
			
			$val['avatar'] = '<div class="list-foto"><img src="' . $avatar . '?r=' . time() . '"/></div>';
			$val['verified']=$verified[$val['verified']];
			
			$val['ignore_search_urut'] = $no;
			$val['ignore_search_action'] = btn_action([
									'edit' => ['url' => BASE_URL . $current_module['nama_module'] . '/edit?id='. $val['id_user']]
									, 'delete' => ['url' => ''
												, 'id' =>  $val['id_user']
												, 'delete-title' => 'Hapus data customer: <strong>'.$val['nama'].'</strong> ?'
											]
									,'view' => ['url' => BASE_URL . $current_module['nama_module'] . '/view?id='. $val['id_user']]
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
		$columns[]['data'] = 'username';
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
	$sql = 'SELECT COUNT(*) AS jml_data FROM user LEFT JOIN role USING(id_role) ' . where_own() .' AND id_role=4';
	$query = $db->query($sql)->getRowArray();
	$total_data = $query['jml_data'];
	
	// Query Filtered
	$sql = 'SELECT COUNT(*) AS jml_data FROM user LEFT JOIN role USING(id_role) ' . $where.' AND id_role=4';
	$query = $db->query($sql)->getRowArray();
	$total_filtered = $query['jml_data'];
	
	// Query Data
	$sql = 'SELECT * FROM user LEFT JOIN role USING(id_role) ' . $where  .' AND id_role=4 '. $order;
	$content = $db->query($sql)->getResultArray();
	
	return ['total_data' => $total_data, 'total_filtered' => $total_filtered, 'content' => $content];
}

function validate_form() {
	
	global $list_action;
	require_once('app/libraries/FormValidation.php');
	$validation = new FormValidation();
	$validation->setRules('nama', 'Nama', 'trim|required');
	$validation->setRules('email', 'Email', 'trim|required|valid_email');
	
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