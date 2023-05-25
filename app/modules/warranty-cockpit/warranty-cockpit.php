
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
$js[] = BASE_URL . 'public/vendors/datatables/datatables.fixedheader.min.js';
		
switch ($_GET['action']) 
{
	default: 
		action_notfound();
		
	// INDEX 
	case 'index':
		$js[] = BASE_URL . 'public/themes/modern/js/warranty-cockpit.js';
		$js[] = BASE_URL . 'public/themes/modern/js/data-tables-liveajax.js';
		cek_hakakses('read_data');
		
		$data['title'] = 'Warranty Cockpit';
		$sql = 'SELECT * FROM wty_claim LEFT JOIN case_status USING(id_case_status)';
		
		$data['result'] = $db->query($sql)->getResultArray();
		
		if (!$data['result']) {
			$data['msg']['status'] = 'error';
			$data['msg']['message'] = 'Data tidak ditemukan';
		}
		
		//Unlock warranty claim
		$db->query("UPDATE wty_claim SET locked='0', locked_by='' WHERE locked_by='".trim($user['nama'])."'");
		
		load_view('views/result.php', $data);
	
	case 'edit': 
		
		cek_hakakses('update_data');
		
		if (empty($_REQUEST['id'])) {
			$result['msg']['status'] = 'error';
			$result['msg']['message'] = 'Data warranty cockpit yang ingin diedit tidak ditemukan';
		} else {
			$sql = 'SELECT a.*, (SELECT f.id_kategori FROM sku f WHERE a.sku=f.sku) as id_kategori, b.wty_period, b.wty_end, d.wty_status, c.unit_status, b.bought_date FROM wty_claim a, wty b, unit_status c, wty_status d WHERE a.sku=b.sku AND a.serial_number=b.serial_number AND b.id_unit_status=c.id_unit_status AND b.id_wty_status=d.id_wty_status AND a.id_wty_claim = ?';
			$result = $db->query($sql, trim($_REQUEST['id']))->row();
		}
		
		if (!$result)
			data_notfound();
			
		$data =  $result;	
		$data['result'] = count($result);
		
		if (!$result) {
			$data['msg']['status'] = 'error';
			$data['msg']['message'] = 'Data tidak ditemukan';
		}else{
			$data['user']=$_SESSION['user'];
				
			//Lock warranty claim
			if(intval($data['locked'])==0){
				$db->query("UPDATE wty_claim SET locked='1', locked_by='".$user['nama']."' WHERE id_wty_claim='".trim($_REQUEST['id'])."'");
			}
			
			if($data['id_case_status']=="1"){
				$db->query("UPDATE wty_claim SET id_case_status='2', updated_by='".$user['nama']."' WHERE id_wty_claim='".trim($_REQUEST['id'])."'");
			}
		}
		
		$sql = 'SELECT a.*, (SELECT f.id_kategori FROM sku f WHERE a.sku=f.sku) as id_kategori, b.wty_period, b.wty_end, d.wty_status, c.unit_status, b.bought_date FROM wty_claim a, wty b, unit_status c, wty_status d WHERE a.sku=b.sku AND a.serial_number=b.serial_number AND b.id_unit_status=c.id_unit_status AND b.id_wty_status=d.id_wty_status AND a.id_wty_claim = ?';
		$result = $db->query($sql, trim($_REQUEST['id']))->row();
		
		if (!$result)
			data_notfound();
			
		$data = $result;
		$data['result'] = count($result);
		
		$data['title'] = 'Edit ' . $current_module['judul_module'];
			
		// List module faulty
		if($result){
			$sql = "SELECT * FROM faulty WHERE id_kategori='".$data['id_kategori']."'";
			$data['faulty'] = $db->query($sql)->result();
		}else{
			$sql = "SELECT * FROM faulty WHERE id_kategori='1'";
			$data['faulty'] = $db->query($sql)->result();
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
		
		// List module store
		$sql = "SELECT * FROM store";
		$data['data_store'] = $db->query($sql)->result();
		
		$sql = 'SELECT a.*, b.case_status FROM history_wty_claim a, 
					case_status b WHERE a.id_case_status=b.id_case_status 
					AND a.id_wty_claim=? order by a.created_date desc';
		$data['history'] = $db->query($sql,trim($_REQUEST['id']))->getResultArray();		
		
					
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
				$fields = ['id_case_status','id_product_return','faulty_name_check','faulty_remark_check','closed'];
				
				foreach ($fields as $field) {
					$data_db[$field] = $_POST[$field];
				}
				if($_POST['id_case_status']=='8'){
					$data_db['id_reject_reason'] = $_POST['id_reject_reason'];
				}else{
					$data_db['id_reject_reason'] = "0";
				}
				
				$data_db['updated_date'] = date('Y-m-d H:i:s');
				$data_db['updated_by'] = $user['nama'];
				
				/*
				Case Status
					1 New 	
					2 Reviewed
					3 Wait Faulty 	
					4 Received
					5 Checked
					6 Replaced
					7 Upgrade
					8 Rejected 
					9 Closed
					
					Unit Status
					1 New 
					2 RMA
					3 REP
					4 UPG
					*/
				if($_POST['id_case_status']=='6' || $_POST['id_case_status']=='7'){
					
					if($_POST['id_case_status']=='7'){
						if(isset($_POST['sku_new']) && $_POST['sku_new']!=""){
							$sku = $_POST['sku_new'];
						}else{
							$sku = $data['sku'];
						}
						
						$data_db3['sku'] =  $sku;						
						$id_unit_status = '4';
					}else{
						$sku = $data['sku'];
						$data_db3['sku'] =  $sku;
						$id_unit_status = '3';
					}
					
					$sql = 'SELECT a.*, (SELECT f.id_kategori FROM sku f WHERE a.sku=f.sku) as id_kategori, b.id_wty, b.wty_period, b.wty_end, b.wty_start, b.id_parent, d.wty_status, c.unit_status, b.bought_date, b.customer_no FROM wty_claim a, wty b, unit_status c, wty_status d WHERE a.sku=b.sku AND a.serial_number=b.serial_number AND b.id_unit_status=c.id_unit_status AND b.id_wty_status=d.id_wty_status AND a.id_wty_claim = ?';
					$data = $db->query($sql, trim($_REQUEST['id']))->row();
					
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
					
					// List module store
					$sql = "SELECT * FROM store";
					$data['data_store'] = $db->query($sql)->result();
					
					$sql = 'SELECT a.*, b.case_status FROM history_wty_claim a, 
								case_status b WHERE a.id_case_status=b.id_case_status 
								AND a.id_wty_claim=? order by a.created_date desc';
					$data['history'] = $db->query($sql,trim($_REQUEST['id']))->getResultArray();
						
					if(isset($_POST['serial_number_new']) && isset($_POST['serial_number_new_confirm'])){
						if($_POST['serial_number_new']!=$_POST['serial_number_new_confirm'] || $_POST['serial_number_new']==''){					
							$data['msg']['message'] = 'Confirm serial number does not match';
							$error = true;
						}else{
							$serial_number = $_POST['serial_number_new'];
							
							$sql = "SELECT count(*) as jml_sn FROM wty b WHERE b.serial_number='$serial_number'";;
							$cek_sn = $db->query($sql)->row();
							
							$sql = "SELECT id_unit_status, id_wty, customer_no, bought_date FROM wty a WHERE 
													a.sku='$sku' AND a.serial_number='$serial_number'";;
							$jml_sn = $db->query($sql)->row();
							
							if($data['id_parent']!=0){
								$id_parent = $data['id_parent'];
								$bought_date = $data['bought_date'];
							}else{
								$id_parent = $data['id_wty'];
								$bought_date = $data['bought_date'];
							}
							
							if(strtotime($data['wty_end'])>=strtotime(date("Y-m-d"))){
								$id_wty_status = '1';
							}else{
								$id_wty_status = '2';
							}							
							
							if($cek_sn['jml_sn']==0){
								$data_db3['id_store'] =  "1";
								$data_db3['nama_store'] =  "TS Store";
								$data_db3['id_task_upload'] = "";
								
								$data_db3['id_parent'] = $id_parent;
								$data_db3['serial_number'] = $serial_number;
								$data_db3['wty_period'] = $data['wty_period'];
								$data_db3['wty_start'] = $data['wty_start'];
								$data_db3['wty_end'] = $data['wty_end'];
								
								$data_db3['id_wty_status'] = $id_wty_status;
								$data_db3['id_unit_status'] = $id_unit_status;
								$data_db3['bought_date'] = $bought_date;
								$data_db3['created_date'] = date('Y-m-d H:i:s');
								$data_db3['created_by'] = $user['nama'];
								$data_db3['updated_date'] = date('Y-m-d H:i:s');
								$data_db3['updated_by'] = $user['nama'];
								$data_db3['id_wty_claim'] = $data['id_wty_claim'];
								$data_db3['customer_no'] = "";
						
								$query = $db->insert('wty', $data_db3);									
																
							}else{								
								if(strtolower($data['type_case'])==strtolower("d.claim")){
									if($jml_sn['id_unit_status']!=1){
										$data['msg']['message'] = 'Serial Number sudah digunakan';
										$error = true;
									}
								}else{
									$data['msg']['message'] = 'Serial Number already exists';
									$error = true;
								}
							}							
							
							$data_db4['id_wty_status'] = $id_wty_status;
							$data_db4['id_parent'] = $id_parent;
							$data_db4['id_wty_claim'] = $data['id_wty_claim'];
							$data_db4['customer_no'] = $data['customer_no'];
							$data_db4['id_unit_status'] = $id_unit_status;
							$data_db4['bought_date'] = $bought_date;
							if($data['id_wty']!=""){
								$query = $db->update('wty', $data_db4, 'id_wty = ' . $data['id_wty']);
							}
							if($jml_sn['id_wty']!=""){
								$query = $db->update('wty', $data_db4, 'id_wty = ' . $jml_sn['id_wty']);
							}
						}
					}
				}
						
				if (!$error) {
					if($_POST['id_case_status']=='7'){
						$data_db['sku_new'] = $sku;
					}
					if(isset($_POST['serial_number_new']) && isset($_POST['serial_number_new_confirm'])){
						if($_POST['id_case_status']=='6' || $_POST['id_case_status']=='7'){
							$data_db['serial_number_new'] = $serial_number;
						}
					}
					
					$query = $db->update('wty_claim', $data_db, 'id_wty_claim = ' . $_POST['id']);

					if ($query) {
						$data_db2['id_wty_claim'] =  $_POST['id'];
						if($_POST['closed']==1){
							$data_db2['keterangan'] = "Case Closed";
						}
						$data_db2['id_case_status'] =  $_POST['id_case_status'];
						$data_db2['created_date'] = date('Y-m-d H:i:s');
						$data_db2['created_by'] = $user['nama'];
				
						$query = $db->insert('history_wty_claim', $data_db2);						
						
						$sql = 'SELECT a.*, (SELECT f.id_kategori FROM sku f WHERE a.sku=f.sku) as id_kategori, b.id_wty, b.wty_period, b.wty_end, b.wty_start, d.wty_status, c.unit_status, b.bought_date FROM wty_claim a, wty b, unit_status c, wty_status d WHERE a.sku=b.sku AND a.serial_number=b.serial_number AND b.id_unit_status=c.id_unit_status AND b.id_wty_status=d.id_wty_status AND a.id_wty_claim = ?';
						$data = $db->query($sql, trim($_REQUEST['id']))->row();
						
						// List module faulty
						$sql = "SELECT * FROM faulty WHERE id_kategori='".$data['id_kategori']."'";
						$data['faulty'] = $db->query($sql)->result();
					
						// List module case status
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
						
						// List module store
						$sql = "SELECT * FROM store";
						$data['data_store'] = $db->query($sql)->result();					
						
						$sql = 'SELECT a.*, b.case_status FROM history_wty_claim a, 
									case_status b WHERE a.id_case_status=b.id_case_status 
									AND a.id_wty_claim=? order by a.created_date desc';
						$data['history'] = $db->query($sql,trim($_REQUEST['id']))->getResultArray();
						
						$data['msg']['message'] = 'Data berhasil disimpan';
					} else {						
						$data['msg']['message'] = 'Data gagal disimpan';
						$error = true;
					}
				}
			}
			
			$data['title'] = 'Edit ' . $current_module['judul_module'];

			$data['msg']['status'] = $error ? 'error' : 'ok';
			$data['result']=1;
		}
		
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
			$color="";
			$fontcolor="#000";
			$booking    =new DateTime($val['updated_date']);
			$today      =new DateTime();
			$diff = $today->diff($booking);
			$day=$diff->d;
			
			$val['day']=$day;
			$val['created_date']=format_tanggal_indo($val['created_date']);
			
			if(empty($val['updated_by']) || $val['updated_by']==null){
				$val['updated_by']="";
			}
			
			$val['ignore_search_urut'] = $no;
			$val['ignore_search_action'] = btn_action([
									'view' => ['url' => BASE_URL . $current_module['nama_module'] . '/edit?id='. $val['id_wty_claim']]
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
	
	// Search
	$search_all = @$_POST['search']['value'];
	$where = where_own();
	if ($search_all) {
		// Additional Search
		$columns[]['data'] = 'case_no';
		foreach ($columns as $val) {
			
			if (strpos($val['data'], 'ignore_search') !== false) 
				continue;
			
			if (strpos($val['data'], 'ignore') !== false)
				continue;
			
			if (strpos($val['data'], 'day') !== false)
				continue;
			
			$where_col[] = "upper(".$val['data'].")" . ' LIKE "%' . strtoupper($search_all) . '%"';
		}
		$where .= ' AND (' . join(' OR ', $where_col) . ') ';
	}	
	
	// Order
	$start = @$_POST['start'] ?: 0;
	$length = @$_POST['length'] ?: 10;
	
	$order_data = @$_POST['order'];
	$order = '';
	if(isset($_POST['columns'])){
		
		//search by columns
		foreach ($columns as $val) {
			if (strpos($val['data'], 'ignore_search') !== false) 
					continue;
				
			if (strpos($val['data'], 'ignore') !== false)
					continue;
				
			if (strpos($val['data'], 'day') !== false) 
					continue;
				
			if( isset($val['search']['value'])){ 
				$where.=" AND upper(".$val['data'].") LIKE '%".strtoupper($val['search']['value'])."%' ";
			}
		}
		
		if (strpos($_POST['columns'][$order_data[0]['column']]['data'], 'ignore_search') === false) {
			$order_by = $columns[$order_data[0]['column']]['data'] . ' ' . strtoupper($order_data[0]['dir']);
			$order = 'ORDER BY ' . $order_by . ' LIMIT ' . $start . ', ' . $length;
		}
	}else{
		$order = 'ORDER BY case_no DESC LIMIT 0, 10';
	}

	// Query Total
	$sql = 'SELECT COUNT(*) AS jml_data FROM wty_claim  a
				LEFT JOIN case_status USING(id_case_status)
				LEFT JOIN status b ON a.closed = b.id_status ';
	$query = $db->query($sql)->getRowArray();
	$total_data = $query['jml_data'];
	
	// Query Filtered
	$sql = 'SELECT COUNT(*) AS jml_data FROM wty_claim a
				LEFT JOIN case_status USING(id_case_status)
				LEFT JOIN status b ON a.closed = b.id_status  '.$where;
	$query = $db->query($sql)->getRowArray();
	$total_filtered = $query['jml_data'];
	
	// Query Data
	$sql = 'SELECT * FROM wty_claim a 
				LEFT JOIN case_status USING(id_case_status)
				LEFT JOIN status b ON a.closed = b.id_status ' . $where  . $order;
	$content = $db->query($sql)->getResultArray();
	
	return ['total_data' => $total_data, 'total_filtered' => $total_filtered, 'content' => $content];
}

function validate_form() {
	
	global $list_action;
	require_once('app/libraries/FormValidation.php');
	$validation = new FormValidation();
	$validation->setRules('id_case_status', 'ID Case Status', 'trim|required');
	
	$validation->validate();
	$form_errors =  $validation->getMessage();
	
	return $form_errors;
}