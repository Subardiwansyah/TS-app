
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
			
			$result = $db->delete('kategori', ['id_kategori' => $_POST['id']]);
			// $result = true;
			if ($result) {
				$data['msg'] = ['status' => 'ok', 'message' => 'Data katalog berhasil dihapus'];
			} else {
				$data['msg'] = ['status' => 'error', 'message' => 'Data katalog gagal dihapus'];
			}
		}
		
		$data['title'] = 'Data Brand';
		$sql = 'SELECT * FROM kategori';
		
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
			$validation->setRules('nama_kategori', 'Nama Brand', 'trim|required');
			$valid = $validation->validate();
			
			if (!$valid) {
				$data['msg']['status'] = 'error';
				$data['msg']['message'] = $validation->getMessage();
				$error = true;
			}
			
			if (!$error) {
				
				$fields = ['nama_kategori'];
				foreach ($fields as $field) {
					$data_db[$field] = $_POST[$field];
				}
				$data_db['created_date'] = date('Y-m-d H:i:s');
				$data_db['created_by'] = $user['nama'];	
				$data_db['updated_date'] = date('Y-m-d H:i:s');
				$data_db['updated_by'] = $user['nama'];
				$query = $db->insert('kategori', $data_db);

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
			$result['msg']['message'] = 'Data brand yang ingin diedit tidak ditemukan';
		} else {
			$sql = 'SELECT * FROM kategori WHERE id_kategori = ?';
			$result = $db->query($sql, trim($_REQUEST['id']))->row();
		}
		
		if (!$result)
			data_notfound();
			
		$data = $result;
		
		$data['title'] = 'Edit ' . $current_module['judul_module'];
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
				$fields = ['nama_kategori'];
				
				foreach ($fields as $field) {
					$data_db[$field] = $_POST[$field];
				}				
				
				if (!$error) {
					$data_db['updated_date'] = date('Y-m-d H:i:s');
					$data_db['updated_by'] = $user['nama'];
					$query = $db->update('kategori', $data_db, 'id_kategori = ' . $_POST['id']);
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
		
		load_view('views/form.php', $data);
		
	case 'view': 
		
		cek_hakakses('update_data');
		
		if (empty($_REQUEST['id'])) {
			$result['msg']['status'] = 'error';
			$result['msg']['message'] = 'Data brand yang ingin diedit tidak ditemukan';
		} else {
			$sql = 'SELECT * FROM kategori WHERE id_kategori = ?';
			$result = $db->query($sql, trim($_REQUEST['id']))->row();
		}
		
		if (!$result)
			data_notfound();
			
		$data = $result;
		
		$data['title'] = 'Edit ' . $current_module['judul_module'];
		$breadcrumb['Edit'] = '';
			
		// Submit
		$data['msg'] = [];
		
		load_view('views/form-view.php', $data);
		
	case 'getDataDT':
		
		$result['draw'] = $start = $_POST['draw'] ?: 1;
		
		$data_table = getListData();
		$result['recordsTotal'] = $data_table['total_data'];
		$result['recordsFiltered'] = $data_table['total_filtered'];
				
		helper('html');
		
		$no = $_POST['start'] + 1 ?: 1;
				
		foreach ($data_table['content'] as $key => &$val) 
		{
			
			$val['ignore_search_urut'] = $no;
			$val['ignore_search_action'] = btn_action([
									'edit' => ['url' => BASE_URL . $current_module['nama_module'] . '/edit?id='. $val['id_kategori']]
									, 'delete' => ['url' => ''
												, 'id' =>  $val['id_kategori']
												, 'delete-title' => 'Hapus data kategori: <strong>'.$val['nama_kategori'].'</strong> ?'
											]
									,'view' => ['url' => BASE_URL . $current_module['nama_module'] . '/view?id='. $val['id_kategori']]
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
		$columns[]['data'] = 'nama_kategori';
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
	$sql = 'SELECT COUNT(*) AS jml_data FROM kategori ';
	$query = $db->query($sql)->getRowArray();
	$total_data = $query['jml_data'];
	
	// Query Filtered
	$sql = 'SELECT COUNT(*) AS jml_data FROM kategori '.$where;
	$query = $db->query($sql)->getRowArray();
	$total_filtered = $query['jml_data'];
	
	// Query Data
	$sql = 'SELECT * FROM kategori ' . $where  . $order;
	$content = $db->query($sql)->getResultArray();
	
	return ['total_data' => $total_data, 'total_filtered' => $total_filtered, 'content' => $content];
}

function validate_form() {
	
	global $list_action;
	require_once('app/libraries/FormValidation.php');
	$validation = new FormValidation();
	$validation->setRules('nama_kategori', 'Nama Kategori', 'trim|required');
	
	$validation->validate();
	$form_errors =  $validation->getMessage();
	
	return $form_errors;
}