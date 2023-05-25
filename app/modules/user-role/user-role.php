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
			cek_hakakses('delete_data', 'user', 'id_user');
			$result = $db->delete('user', ['id_user' => $_POST['id']]);
			// $result = true;
			if ($result) {
				$data['msg'] = ['status' => 'ok', 'message' => 'Data user berhasil dihapus'];
			} else {
				$data['msg'] = ['status' => 'error', 'message' => 'Data user gagal dihapus'];
			}
		}
		
		$data['title'] = 'Data User';
		$sql = 'SELECT * FROM user LEFT JOIN role USING(id_role)' . where_own('id_user')." and id_role not like '4'";
		
		$data['result'] = $db->query($sql)->getResultArray();
		
		if (!$data['result']) {
			$data['msg']['status'] = 'error';
			$data['msg']['message'] = 'Data tidak ditemukan';
		}
		
		$data['form'] = load_view('views/form-cari.php', $data, true);
		load_view('views/result.php', $data);		
		
	case 'edit': 
		
		cek_hakakses('update_data', null, 'id_user');
		
		if (empty($_REQUEST['id'])) {
			$result['msg']['status'] = 'error';
			$result['msg']['message'] = 'Data user yang ingin diedit tidak ditemukan';
		} else {
			$sql = 'SELECT * FROM user WHERE id_user = ?';
			$result = $db->query($sql, trim($_GET['id']))->row();
		}
		
		$data = $result;
		
		$data['title'] = 'Edit ' . $current_module['judul_module'];
		
		// List module role
		$sql = 'SELECT * FROM role';
		$data['role'] = $db->query($sql)->result();
		
		// List module store
		$sql = 'SELECT * FROM store WHERE aktif=1';
		$data['store'] = $db->query($sql)->result();
		
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
				$fields = ['id_role'];
				
				foreach ($fields as $field) {
					$data_db[$field] = $_POST[$field];
				}
				
				if (!$error) {
					if($new_name!=""){
					$data_db['avatar'] = $new_name;
					}
					$query = $db->update('user', $data_db, 'id_user = ' . $_POST['id']);
					if ($query) {
						$data['msg']['message'] = 'Data berhasil disimpan';
						header('location:'.module_url());
					} else {
						$data['msg']['message'] = 'Data gagal disimpan';
						$error = true;
					}
				}
			}
			
			$data['msg']['status'] = $error ? 'error' : 'ok';
		}
		
		load_view('views/form-edit.php', $data);
	
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
		$order = 'ORDER BY ' . $order_by . ' LIMIT ' . $start . ', ' . $length;
	}

	// Query Total
	$sql = 'SELECT COUNT(*) AS jml_data FROM user LEFT JOIN role USING(id_role) ' . where_own();
	$query = $db->query($sql)->getRowArray();
	$total_data = $query['jml_data'];
	
	// Query Filtered
	$sql = 'SELECT COUNT(*) AS jml_data FROM user LEFT JOIN role USING(id_role) ' . $where;
	$query = $db->query($sql)->getRowArray();
	$total_filtered = $query['jml_data'];
	
	// Query Data
	$sql = 'SELECT * FROM user LEFT JOIN role USING(id_role) ' . $where . $order;
	$content = $db->query($sql)->getResultArray();
	
	return ['total_data' => $total_data, 'total_filtered' => $total_filtered, 'content' => $content];
}

function validate_form() {
	
	global $list_action;
	require_once('app/libraries/FormValidation.php');
	$validation = new FormValidation();
	$validation->setRules('id_role', 'Role', 'trim|required');
	
	$validation->validate();
	$form_errors =  $validation->getMessage();
	
	return $form_errors;
}