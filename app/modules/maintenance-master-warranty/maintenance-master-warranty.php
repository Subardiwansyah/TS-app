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
$js[] = BASE_URL . 'public/themes/modern/js/data-tables.js';
$js[] = BASE_URL . 'public/vendors/datatables/js/dataTables.bootstrap4.min.js';
$styles[] = BASE_URL . 'public/vendors/datatables/css/dataTables.bootstrap4.min.css';
$styles[] = BASE_URL . 'public/vendors/bootstrap-datepicker/css/bootstrap-datepicker3.css';

$js[] ="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js";
$js[] ="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js";
$js[] ="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js";
$js[] ="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js";
$js[] ="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js";

$styles[] = "https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css";


helper('registrasi');

switch ($_GET['action']) 
{
	default: 
		action_notfound();
		
	// INDEX 
	case 'index':
		$js[] = BASE_URL . 'public/themes/modern/js/maintenance-master-warranty.js';
		$js[] = BASE_URL . 'public/themes/modern/js/data-tables-liveajax.js';
		cek_hakakses('read_data');
		
		$data['title'] = 'Maintenance Master Warranty';
		$sql = 'SELECT a.*,
					b.nama_produk,
					c.unit_status,
					d.wty_status, 
					(SELECT count(*) FROM wty_claim e WHERE e.sku=a.sku AND e.serial_number=a.serial_number) jml_claim 
					FROM wty a
					LEFT JOIN sku b 
					ON a.sku = b.sku
					LEFT JOIN unit_status c
					ON a.id_unit_status = c.id_unit_status
					LEFT JOIN wty_status d
					ON a.id_wty_status = d.id_wty_status 
					ORDER BY a.id_wty ASC ';
		
		$data['result'] = $db->query($sql)->getResultArray();
		
		if (!$data['result']) {
			$data['msg']['status'] = 'error';
			$data['msg']['message'] = 'Data tidak ditemukan';
		}
		
		load_view('views/result.php', $data);
	
	case 'edit': 
		
		cek_hakakses('update_data');
		
		if (empty($_REQUEST['id'])) {
			$result['msg']['status'] = 'error';
			$result['msg']['message'] = 'Data warranty yang ingin diedit tidak ditemukan';
		} else {
			$sql = 'SELECT 	a.*,
							b.nama_produk,
							b.id_kategori,
							c.unit_status,
							d.wty_status,
							(SELECT count(*) FROM wty_claim e WHERE e.sku=a.sku AND e.serial_number=a.serial_number) jml_claim 
					FROM wty a
					LEFT JOIN sku b 
					ON a.sku = b.sku
					LEFT JOIN unit_status c
					ON a.id_unit_status = c.id_unit_status
					LEFT JOIN wty_status d
					ON a.id_wty_status = d.id_wty_status
					WHERE id_wty = ?';
			$result = $db->query($sql, trim($_REQUEST['id']))->row();
			
		}
		
		if (!$result)
			data_notfound();
			
		$data = $result;
		$data['result'] = count($result);
			
		// Submit
		$data['msg'] = [];
		$error=false;
		if (isset($_POST['submit'])) 
		{
			require_once('app/libraries/FormValidation.php');
			$validation = new FormValidation();
			//$validation->setRules('sku', 'SKU No.', 'trim|required');
			if($_POST['serial_number']!=$_POST['serial_number_old']){
				$validation->setRules('serial_number', 'Serial Number', 'trim|required|unique[wty]');
			}
			$validation->setRules('id_store', 'Store', 'trim|required');
			$validation->setRules('wty_start', 'WTY Start', 'trim|required');
			//$validation->setRules('id_wty_status', 'WTY Status', 'trim|required');
			//$validation->setRules('id_unit_status', 'Unit Status', 'trim|required');
			$valid = $validation->validate();
			
			if (!$valid) {
				$data['msg']['status'] = 'error';
				$data['msg']['message'] = $validation->getMessage();
				$error = true;
			}
			
			if($_POST['serial_number']!=$_POST['serial_number_old']){
				$sql = 'SELECT count(*) as jml_data FROM wty a WHERE upper(a.serial_number) = ?';
				$data_cek = $db->query($sql, trim(strtoupper($_POST['serial_number'])))->row();
				if($data_cek['jml_data']>0){
					$data['msg']['status'] = 'error';
					$data['msg']['message'] = 'Serial Number: '.trim($_POST['serial_number']).' already exist';
					$error = true;
				}
			}
				
			if (!$error) {
				
				// Assign fields table
				$fields = ['serial_number','id_store','wty_start'];
				
				foreach ($fields as $field) {
					$data_db[$field] = $_POST[$field];
				}
				
				$sql = 'SELECT * FROM store a WHERE a.id_store = ?';
				$data = $db->query($sql, trim($_POST['id_store']))->row();
				
				$date1 = $_POST['wty_start'];
				$period=$_POST['wty_period'];
				$date_plus=date('Y-m-d', strtotime("+$period months", strtotime($date1)));
				
				$data_db['wty_end'] = $date_plus;
				$data_db['nama_store'] = $data['nama_store'];
				$data_db['updated_date'] = date('Y-m-d H:i:s');
				$data_db['updated_by'] = $user['nama'];				
				
				if (!$error) {
					$query = $db->update('wty', $data_db, 'id_wty = ' . $_POST['id']);
					
					$sql = 'SELECT * FROM wty a WHERE a.id_wty = ?';
					$data = $db->query($sql, trim($_POST['id']))->row();					
					
					if(!empty($data['id_wty_claim']) && $data['id_unit_status']!=1){
						if($data['id_unit_status']==4){
							$data_db2['sku_new'] = $data['sku'];
							$data_db2['serial_number_new'] = $data['serial_number'];
						}else if($data['id_unit_status']==3){
							$data_db2['serial_number_new'] = $data['serial_number'];
						}
						
						$query = $db->update('wty_claim', @$data_db2, 'id_wty_claim = ' . $data['id_wty_claim']);	
					}
					
					if(!empty($data['id_parent'])){
						$id_parent=$data['id_parent'];
					}else{
						$id_parent=$data['id_wty'];
					}
					
					$data_db3['wty_start'] = $data['wty_start'];
					$data_db3['wty_end'] = $data['wty_end'];
					$query = $db->update('wty', $data_db3, 'id_parent = ' . $id_parent);
					
					if ($query) {
						$data['msg']['message'] = 'Data berhasil disimpan';
					} else {
						$data['msg']['message'] = 'Data gagal disimpan';
						$error = true;
					}
				}
			}
			
			$data['msg']['status'] = $error ? 'error' : 'ok';
			$data['result']=1;
		}
	
		// List module sku
		$sql = "SELECT * FROM sku WHERE aktif='1'";
		$data['data_sku'] = $db->query($sql)->result();
		
		// List module dealer
		$sql = "SELECT * FROM store WHERE aktif='1'";
		$data['store'] = $db->query($sql)->result();
		
		// List module wty_status
		$sql = "SELECT * FROM wty_status";
		$data['wty_status'] = $db->query($sql)->result();
		
		// List module case_status
		$sql = "SELECT * FROM unit_status";
		$data['unit_status'] = $db->query($sql)->result();
		
		if($data['id_parent']!=0){
			$sql = 'SELECT *,b.unit_status FROM wty a, unit_status b WHERE a.id_parent=(SELECT id_parent FROM wty WHERE id_wty='.trim($_REQUEST['id']).') AND a.id_unit_status=b.id_unit_status ORDER BY id_wty ASC';
			$data['history'] = $db->query($sql)->getResultArray();
		}else{
			$data['history']="";
		}
		
		$data['title'] = 'Edit ' . $current_module['judul_module'];
			
		load_view('views/form.php', $data);
	
	case 'view': 
		
		if (empty($_REQUEST['id'])) {
			$result['msg']['status'] = 'error';
			$result['msg']['message'] = 'Data warranty yang ingin diedit tidak ditemukan';
		} else {
			$sql = 'SELECT 	a.*,
							b.nama_produk,
							b.id_kategori,
							c.unit_status,
							d.wty_status,
							(SELECT count(*) FROM wty_claim e WHERE e.sku=a.sku AND e.serial_number=a.serial_number) jml_claim 
					FROM wty a
					LEFT JOIN sku b 
					ON a.sku = b.sku
					LEFT JOIN unit_status c
					ON a.id_unit_status = c.id_unit_status
					LEFT JOIN wty_status d
					ON a.id_wty_status = d.id_wty_status
					WHERE id_wty = ?';
			$result = $db->query($sql, trim($_REQUEST['id']))->row();
		}
		
		if (!$result)
			data_notfound();
			
		$data =  $result;	
		$data['result'] = count($result);
	
		// List module sku
		$sql = "SELECT * FROM sku WHERE aktif='1'";
		$data['data_sku'] = $db->query($sql)->result();
		
		// List module dealer
		$sql = "SELECT * FROM store WHERE aktif='1'";
		$data['store'] = $db->query($sql)->result();
		
		// List module wty_status
		$sql = "SELECT * FROM wty_status";
		$data['wty_status'] = $db->query($sql)->result();
		
		// List module case_status
		$sql = "SELECT * FROM unit_status";
		$data['unit_status'] = $db->query($sql)->result();
		
		if($data['id_parent']!=0){
			$sql = 'SELECT *,b.unit_status FROM wty a, unit_status b WHERE a.id_parent=(SELECT id_parent FROM wty WHERE id_wty='.trim($_REQUEST['id']).') AND a.id_unit_status=b.id_unit_status ORDER BY id_wty ASC';
			$data['history'] = $db->query($sql)->getResultArray();
		}else{
			$data['history']="";
		}
		
		$data['title'] = 'Edit ' . $current_module['judul_module'];
			
		load_view('views/form-view.php', $data);
	
	case 'add':
		cek_hakakses('create_data');
		
		$breadcrumb['Add'] = '';
		
		$data['title'] = 'Tambah ' . $current_module['judul_module'];
		$data['msg'] = [];
		$data['found'] = 0;
		$data['result'] = 1;
		$error = false;
		helper('registrasi');

		if (isset($_POST['submit'])) 
		{
			require_once('app/libraries/FormValidation.php');
			$validation = new FormValidation();
			$validation->setRules('sku', 'SKU', 'trim|required');
			$validation->setRules('serial_number', 'Serial Number', 'trim|required|unique[wty]');
			$validation->setRules('id_store', 'Store', 'trim|required');
			$validation->setRules('wty_start', 'WTY Start', 'trim|required');
			//$validation->setRules('id_wty_status', 'WTY Status', 'trim|required');
			//$validation->setRules('id_unit_status', 'Unit Status', 'trim|required');
			$valid = $validation->validate();
			
			if (!$valid) {
				$data['msg']['status'] = 'error';
				$data['msg']['message'] = $validation->getMessage();
				$error = true;
			}
			
			if($_POST['serial_number']!=""){
				$sql = 'SELECT count(*) as jml_data FROM wty a WHERE upper(a.serial_number) = ?';
				$data_cek = $db->query($sql, trim(strtoupper($_POST['serial_number'])))->row();
				if($data_cek['jml_data']>0){
					$data['msg']['status'] = 'error';
					$data['msg']['message'] = 'Serial Number: '.trim($_POST['serial_number']).' already exist';
					$error = true;
				}
			}
			
			if (!$error) {
				
				$fields = ['sku','serial_number','id_store','wty_start','id_wty_status','id_unit_status'];
				foreach ($fields as $field) {
					$data_db[$field] = $_POST[$field];
				}
				
				$sql = 'SELECT * FROM sku a WHERE a.sku = ?';
				$data_sku = $db->query($sql, trim($_POST['sku']))->row();
				
				$sql = 'SELECT * FROM store a WHERE a.id_store = ?';
				$data = $db->query($sql, trim($_POST['id_store']))->row();
				
				$date1 = $_POST['wty_start'];
				$period=$data_sku['wty_period'];
				$date_plus=date('Y-m-d', strtotime("+$period months", strtotime($date1)));	
				$data['wty_end']=$date_plus;
				
				$data_db['wty_period'] = $period;
				$data_db['wty_end'] = $date_plus;				
				$data_db['nama_store'] = $data['nama_store'];
				$data_db['created_date'] = date('Y-m-d H:i:s');
				$data_db['created_by'] = $user['nama'];
				$data_db['updated_date'] = date('Y-m-d H:i:s');
				$data_db['updated_by'] = $user['nama'];			
				
				$query = $db->insert('wty', $data_db);
				
				$data['result']=1;
				
				if ($query) {
					$data['msg']['status'] = 'ok';
					$data['msg']['message'] = 'Data berhasil disimpan';
				} else {
					$data['msg']['status'] = 'error';
					$data['msg']['message'] = 'Data gagal disimpan';
				}				
			}
		}		
		
		// List module sku
		$sql = "SELECT * FROM sku WHERE aktif='1'";
		$data['data_sku'] = $db->query($sql)->result();
		
		// List module dealer
		$sql = "SELECT * FROM store WHERE aktif='1'";
		$data['store'] = $db->query($sql)->result();
		
		// List module wty_status
		$sql = "SELECT * FROM wty_status";
		$data['wty_status'] = $db->query($sql)->result();
		
		// List module case_status
		$sql = "SELECT * FROM unit_status";
		$data['unit_status'] = $db->query($sql)->result();		
		
		$data['history']="";
		
		
		$data['title'] = 'Tambah ' . $current_module['judul_module'];
		
		load_view('views/form.php', $data);
		
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
			
			$val['ignore_search_urut'] = $no;
			
			$val['wty_start']=format_tanggal_indo($val['wty_start']);
			$val['wty_end']=format_tanggal_indo($val['wty_end']);
			
			
			if($val['wty_status']=="Out"){ 
				$val['ignore_search_action'] = btn_action([
							'view' => ['url' => BASE_URL . $current_module['nama_module'] . '/view?id='. $val['id_wty']]
					]);
			}else{
				if($user['id_role']==5){
					$val['ignore_search_action'] = btn_action([
							'view' => ['url' => BASE_URL . $current_module['nama_module'] . '/view?id='. $val['id_wty']]
					]);
				}else{
					$val['ignore_search_action'] = btn_action([
									'edit' => ['url' => BASE_URL . $current_module['nama_module'] . '/edit?id='. $val['id_wty']]
									,'view' => ['url' => BASE_URL . $current_module['nama_module'] . '/view?id='. $val['id_wty']]									
					]);					
				}			
			}
			
			$no++;
		}
					
		$result['data'] = $data_table['content'];
		echo json_encode($result); exit();
}

function getListData() {
	
	global $db;
	$columns = @$_POST['columns'];
	$order_by = '';
	$user=$_SESSION['user'];
	
	// Search
	$search_all = @$_POST['search']['value'];
	$where = where_own();
	if($user['id_role']==5){
		$where .= " AND a.id_store='$user[id_store]'";
	}
	if ($search_all) {
		// Additional Search
		$columns[]['data'] = 'a.sku';
		foreach ($columns as $val) {
			
			if (strpos($val['data'], 'ignore_search') !== false) 
				continue;
			
			if (strpos($val['data'], 'ignore') !== false)
				continue;
			
			if (strpos($val['data'], 'jml_claim') !== false)
				continue;
			
			if (strpos($val['data'], 'id_wty') !== false)
				continue;
			
			if($val['data']=='sku' || $val['data']=='nama_store' || $val['data']=='wty_period' || $val['data']=='wty_start' || $val['data']=='wty_end' || $val['data']=='serial_number'){
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
		/*
		foreach ($columns as $val) {
			
			if (strpos($val['data'], 'ignore_search') !== false) 
				continue;
			
			if (strpos($val['data'], 'ignore') !== false)
				continue;
			
			if (strpos($val['data'], 'jml_claim') !== false)
				continue;
			
			if( isset($val['search']['value'])){ 
				if($val['data']=='sku' || $val['data']=='nama_store' || $val['data']=='wty_period' || $val['data']=='wty_start' || $val['data']=='wty_end' || $val['data']=='serial_number'){
					$where.= " AND upper(a.".$val['data'].")" . ' LIKE "%' . strtoupper($val['search']['value']) . '%"';
				}else{
					if($val['data']=='nama_kategori' || $val['data']=='nama_produk'){
						$where.= " AND upper(".$val['data'].")" . ' LIKE "%' . strtoupper($val['search']['value']) . '%"';
					}
				}
			}
		}
		*/
		if (strpos($_POST['columns'][$order_data[0]['column']]['data'], 'ignore_search') === false) {
			$order_by = $columns[$order_data[0]['column']]['data'] . ' ' . strtoupper($order_data[0]['dir']);
			if($length>0){
				$order = 'ORDER BY ' . $order_by . ' LIMIT ' . $start . ', ' . $length;
			}else{
				$order = 'ORDER BY ' . $order_by;
			}
		}
	}else{
		$order = 'ORDER BY a.id_wty ASC LIMIT 0, 10';
	}

	// Query Total
	$sql = 'SELECT count(*) as jml_data
					FROM wty a
					LEFT JOIN sku b 
					ON a.sku = b.sku
					LEFT JOIN unit_status c
					ON a.id_unit_status = c.id_unit_status
					LEFT JOIN wty_status d
					ON a.id_wty_status = d.id_wty_status 
					LEFT JOIN wty_claim e
					ON a.id_wty_claim = e.id_wty_claim 
					LEFT JOIN kategori f 
					ON b.id_kategori = f.id_kategori 					
					LEFT JOIN subkategori g 
					ON b.id_subkategori = g.id_subkategori ';
	$query = $db->query($sql)->getRowArray();
	$total_data = $query['jml_data'];
	
	// Query Filtered
	$sql = 'SELECT count(*) as jml_data
					FROM wty a
					LEFT JOIN sku b 
					ON a.sku = b.sku
					LEFT JOIN unit_status c
					ON a.id_unit_status = c.id_unit_status
					LEFT JOIN wty_status d
					ON a.id_wty_status = d.id_wty_status  
					LEFT JOIN wty_claim e
					ON a.id_wty_claim = e.id_wty_claim 
					LEFT JOIN kategori f 
					ON b.id_kategori = f.id_kategori 					
					LEFT JOIN subkategori g 
					ON b.id_subkategori = g.id_subkategori '.$where;
	$query = $db->query($sql)->getRowArray();
	$total_filtered = $query['jml_data'];
	
	// Query Data
	$sql = 'SELECT a.id_wty,
					a.sku as sku,
					a.serial_number as serial_number,
					a.wty_period as wty_period,
					a.wty_start as wty_start,
					a.wty_end as wty_end,
					a.nama_store as nama_store,
					b.nama_produk as nama_produk,
					c.unit_status as unit_status,
					d.wty_status as wty_status,
					e.case_no as case_no,
					f.nama_kategori as nama_kategori,
					g.nama_subkategori as nama_subkategori,
					(SELECT count(*) FROM wty_claim e WHERE e.sku=a.sku AND e.serial_number=a.serial_number) jml_claim 
					FROM wty a
					LEFT JOIN sku b 
					ON a.sku = b.sku
					LEFT JOIN unit_status c
					ON a.id_unit_status = c.id_unit_status
					LEFT JOIN wty_status d
					ON a.id_wty_status = d.id_wty_status  
					LEFT JOIN wty_claim e
					ON a.id_wty_claim = e.id_wty_claim 					
					LEFT JOIN kategori f 
					ON b.id_kategori = f.id_kategori 					
					LEFT JOIN subkategori g 
					ON b.id_subkategori = g.id_subkategori ' . $where  . $order;
	$content = $db->query($sql)->getResultArray();
	
	return ['total_data' => $total_data, 'total_filtered' => $total_filtered, 'content' => $content];
}