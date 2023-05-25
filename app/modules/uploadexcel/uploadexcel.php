<?php
set_time_limit(0);
/**
*	PHP Admin Template Jagowebdev
*	Website	: https://jagowebdev.com
* 	Author	: Agus Prawoto Hadi
*	Year	: 2021
*/
//error_reporting(0);
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

$data['title'] = 'Upload Warranty';
$data['tabel'] = ['warranty' => ['file_excel' => ['url' => BASE_URL . 'public/files/data_warranty.xlsx'
													, 'title' => 'Download File Excel Format Data Warranty'
													, 'display' => 'data_warranty.xlsx'
												  ]
								  , 'display' => 'Data Warranty'
								],
				];

$js[] = BASE_URL . 'public/themes/modern/js/uploadexcel.js';
$js[] = ['print' => true, 'script' => 'var tabel = ' . json_encode($data['tabel'])];
				
foreach ($data['tabel'] as $key => $val) {
	$data['tabel_options'][$key] = $val['display'];
}				

helper('format');		

switch ($_GET['action']) 
{
	default: 
		action_notfound();
		
	// INDEX 
	case 'index':
		
		if (isset($_POST['submit'])) 
		{
			$path = BASEPATH . 'public/tmp/';
						
			if (!$_FILES['file_excel']['name']) {
				$form_errors['file_excel'] = 'File excel belum dipilih';
			}
			
			$form_errors = validate_form();
			if ($form_errors) {
				$data['msg']['status'] = 'error';
				$data['msg']['content'] = $form_errors;
			} else {
				
				$filename = upload_file2($path, $_FILES['file_excel']);
				$id_task_upload=base_convert(microtime(false), 10, 36);
				
				require_once 'app/libraries/vendors/spout/src/Spout/Autoloader/autoload.php';
				$reader = ReaderEntityFactory::createReaderFromFile($path . $filename);
				$reader->setShouldFormatDates(true);
				$reader->open($path . $filename);
				
				$total_row = 0;
				$total_error = 0;
				$total_success = 0;
				$data_error="";
				$data_error.='Task ID: '.$id_task_upload.'<br><table border="1" cellspacing="0" cellpadding="10">
				<tr>
					<td>No</td>
					<td>Barcode</td>
					<td>SN Jual</td>
					<td>Tgl Jual</td>
					<td>Nama Toko</td>
					<td>Keterangan</td>
				</tr>';
				foreach ($reader->getSheetIterator() as $sheet) 
				{
					
					foreach ($sheet->getRowIterator() as $num_row => $row) 
					{
						
						$err_msg="";
						$error=0;
						$cols = $row->toArray();
						
						if ($num_row == 1) {
							$field_table = $cols;
							$field_name = array_map('strtolower', $field_table);
							continue;
						}
						
						$data_value = [];
						foreach ($cols as $num_col => $val) 
						{
							
							//if ($val instanceof DateTime) {
							//	$val = $val;
							//}
							if ($num_row > 1) {
								if($field_name[$num_col]=="barcode"){
									$sku=trim($val);
								}
								if($field_name[$num_col]=="sn jual"){
									$serial_number=trim($val);
								}
								if($field_name[$num_col]=="tgl jual"){
									$wty_start=trim($val);
								}
								if($field_name[$num_col]=="nama toko"){
									$nama_store=trim($val);
								}
							}							
						}
						
						if(check_empty($sku)==true || check_empty($serial_number)==true || check_empty($wty_start)==true || check_empty($nama_store)==true){
							$err_msg.="[data tidak lengkap]";
						}
						if(check_sku($sku)==false){
							$err_msg.="[sku tidak terdaftar]";
						}
						if(check_store($nama_store)==false){
							//$err_msg.="[store tidak terdaftar]";
							insert_store($nama_store);
						}
						if(check_wty($sku,$serial_number,$nama_store)==true){
							$err_msg.="[data sudah terdaftar]";
							//update_wty($sku,$serial_number,$wty_start,$nama_store);
						}
						if(check_empty($sku)==true || check_empty($serial_number)==true || check_empty($wty_start)==true || check_empty($nama_store)==true || 
							check_sku($sku)==false || check_store($nama_store)==false || check_wty($sku,$serial_number,$nama_store)==true){
							$data_error.="<tr style='color:red'>
											<td>$num_row</td>
											<td>$sku</td>
											<td>$serial_number</td>
											<td>$wty_start</td>
											<td>$nama_store</td>
											<td>$err_msg</td>
										</tr>";
							$total_error++;
							$error=1;							
						}else{
							if(insert_wty($sku,$serial_number,$wty_start,$nama_store, $id_task_upload)==true){
								$err_msg.="[data berhasil didaftarkan]";
								$data_error.="<tr>
											<td>$num_row</td>
											<td>$sku</td>
											<td>$serial_number</td>
											<td>$wty_start</td>
											<td>$nama_store</td>
											<td>$err_msg</td>
										</tr>";
								$total_success++;
							}else{								
								$err_msg.="[data gagal didaftarkan]";
								$data_error.="<tr style='color:red'>
											<td>$num_row</td>
											<td>$sku</td>
											<td>$serial_number</td>
											<td>$wty_start</td>
											<td>$nama_store</td>
											<td>$err_msg</td>
										</tr>";
								$total_error++;
							}
						}
						
						$total_row ++;						
					}
				}
				$data_error.='<tr><td colspan="6">Data sukses : <font style="color:#fff">'.format_ribuan($total_success) .'</font> data<br>Data gagal : <font style="color:red">'.format_ribuan($total_error) .'</font> data<br>Total Data : ' . format_ribuan($total_row) . ' data</td></tr></table><br><br><button type="button" id="download-button">download CSV</button>';
				
				$reader->close();
				delete_file ($path . $filename);
				
				//if ($query) {
					$data['msg']['status'] = 'ok';
					$data['msg']['content'] = 'Data sukses : <font style="color:#fff">'.format_ribuan($total_success) .'</font> data<br>
					Data gagal : <font style="color:red">'.format_ribuan($total_error) .'</font> data<br>
					Total Data : ' . format_ribuan($total_row) . ' data<br><hr>
					<div style="height: 200px; overflow-y: auto;">'.$data_error.'</div>';
				//}
				
			}
		}
				
		load_view('views/form.php', $data);
}

function validate_form() {
	
	$form_errors = [];
	if ($_FILES['file_excel']['name']) 
	{
		$file_type = $_FILES['file_excel']['type'];
		$allowed = ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
		
		if (!in_array($file_type, $allowed)) {
			$form_errors['file_excel'] = 'Tipe file harus ' . join(', ', $allowed);
		}
	}
	
	return $form_errors;
}

function check_empty($val){
	if(empty($val) || $val==""){
		return true;
	}else{
		return false;
	}
}

function check_sku($sku){
	global $db;
	$sql = "SELECT count(*) as jml FROM sku WHERE lower(sku)='".trim(strtolower($sku))."'";
	$data = $db->query($sql)->row();
	if($data['jml']>0){
		return true;
	}else{
		return false;
	}
}

function check_store($nama_store){
	global $db;
	$sql = "SELECT count(*) as jml FROM store WHERE lower(nama_store)='".trim(strtolower($nama_store))."'";
	$data = $db->query($sql)->row();
	if($data['jml']>0){
		return true;
	}else{
		return false;
	}
}

function check_wty($sku,$serial_number,$nama_store){
	global $db;
	$sql = "SELECT count(*) as jml FROM wty WHERE lower(nama_store)='".trim(strtolower($nama_store))."' 
									AND sku='".trim(strtolower($sku))."' AND serial_number='".trim(strtolower($serial_number))."'";
	$data = $db->query($sql)->row();
	if($data['jml']>0){
		return true;
	}else{
		return false;
	}
}

function insert_store($nama_store){
	global $db;
	$user = $_SESSION['user'];
	
	$sql_store = "SELECT count(*) as jml FROM store WHERE lower(nama_store)='".trim(strtolower($nama_store))."'";
	$data = $db->query($sql_store)->row();
				
	if($data['jml']==0){
		if($nama_store!=""){		
			
			$data_db['nama_store']=$nama_store;			
			$data_db['alamat'] = '';
			$data_db['aktif'] = 1;
			$data_db['created_date'] = date('Y-m-d H:i:s');
			$data_db['created_by'] = $user['nama'];
			$data_db['updated_date'] = date('Y-m-d H:i:s');
			$data_db['updated_by'] = $user['nama'];			
			
			$query = $db->insert('store', $data_db);
			
			if($query){
				return true;
			}
		}else{
			return false;
		}
	}else{
		return false;
	}
}

function insert_wty($sku,$serial_number,$wty_start,$nama_store,$id_task_upload){
	global $db;
	$user = $_SESSION['user'];
	
	$sql_store = "SELECT * FROM store WHERE lower(nama_store)='".trim(strtolower($nama_store))."'";
	$data_store = $db->query($sql_store)->row();
	
	$sql = "SELECT count(*) as jml FROM wty WHERE lower(nama_store)='".trim(strtolower($nama_store))."' 
									AND sku='".trim(strtolower($sku))."' AND serial_number='".trim(strtolower($serial_number))."'";
	$data = $db->query($sql)->row();
	
	$sql_wty = "SELECT * FROM wty WHERE lower(nama_store)='".trim(strtolower($nama_store))."' 
									AND sku='".trim(strtolower($sku))."' AND serial_number='".trim(strtolower($serial_number))."'";
	$data_wty = $db->query($sql_wty)->row();
	
	$sql = 'SELECT * FROM sku WHERE lower(sku) = ?';
	$data_sku = $db->query($sql, trim(strtolower($sku)))->row();
	
	$exp = explode(".",$wty_start);
	//d/m/Y
	//mktime($hour, $minute, $second, $month, $day, $year, $is_dst)
	$date_ex=date("Y-m-d", mktime(0,0,0,$exp[1],$exp[0],$exp[2]));
	$date1 = $date_ex;
	
	$period=$data_sku['wty_period'];
	$date_plus=date('Y-m-d', strtotime("+$period months", strtotime($date1)));
	
	if($data['jml']==0){
		if($sku!="" && $serial_number!="" && $nama_store!=""){			
			$data_db['wty_end']=$date_plus;			
			$data_db['wty_period'] = $period;
			$data_db['wty_start'] = $date_ex;			
			$data_db['id_store'] = $data_store['id_store'];			
			$data_db['nama_store'] = $data_store['nama_store'];
			$data_db['id_wty_status'] = 1;
			$data_db['id_unit_status'] = 1;
			$data_db['sku'] = $sku;
			$data_db['customer_no'] = '';	
			$data_db['id_task_upload'] = $id_task_upload;
			$data_db['serial_number'] = $serial_number;
			$data_db['created_date'] = date('Y-m-d H:i:s');
			$data_db['created_by'] = $user['nama'];
			$data_db['updated_date'] = date('Y-m-d H:i:s');
			$data_db['updated_by'] = $user['nama'];			
			
			$query = $db->insert('wty', $data_db);
			
			if($query){
				return true;
			}
		}else{
			return false;
		}
	}else{
		return false;
	}
}

function update_wty($sku,$serial_number,$wty_start,$nama_store){
	global $db;
	$user = $_SESSION['user'];
	
	$sql_store = "SELECT * FROM store WHERE lower(nama_store)='".trim(strtolower($nama_store))."'";
	$data_store = $db->query($sql_store)->row();
	
	$sql = "SELECT count(*) as jml FROM wty WHERE lower(nama_store)='".trim(strtolower($nama_store))."' 
									AND sku='".trim(strtolower($sku))."' AND serial_number='".trim(strtolower($serial_number))."'";
	$data = $db->query($sql)->row();
	
	$sql_wty = "SELECT * FROM wty WHERE lower(nama_store)='".trim(strtolower($nama_store))."' 
									AND sku='".trim(strtolower($sku))."' AND serial_number='".trim(strtolower($serial_number))."'";
	$data_wty = $db->query($sql_wty)->row();
	
	$sql = 'SELECT * FROM sku WHERE lower(sku) = ?';
	$data_sku = $db->query($sql, trim(strtolower($sku)))->row();
	
	$exp = explode("/",$wty_start);
	//d/m/Y
	//mktime($hour, $minute, $second, $month, $day, $year, $is_dst)
	$date_ex=date("Y-m-d", mktime(0,0,0,$exp[1],$exp[0],$exp[2]));
	$date1 = $date_ex;
	
	$period=$data_sku['wty_period'];
	$date_plus=date('Y-m-d', strtotime("+$period months", strtotime($date1)));
	
	if($data['jml']>0){
		if($sku!="" && $serial_number!="" && $nama_store!=""){			
			//return false;
			$data_db['wty_start'] = $date_ex;
			$data_db['wty_end'] = $date_plus;
			$query = $db->update('wty', $data_db, 'id_wty = ' . $data_wty['id_wty']);
			
			if($query){
				return true;
			}
		}else{
			return false;
		}
	}else{
		return false;
	}
}