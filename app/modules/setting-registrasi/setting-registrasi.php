<?php
/**
*	PHP Admin Template
*	Developed by: Agus Prawoto Hadi
*	Website		: www.jagowebdev.com
*	Year		: 2021
*/

$js[] = BASE_URL . 'public/themes/modern/js/setting-registrasi.js';

switch ($_GET['action']) 
{
	default: 
		action_notfound();
		
	// INDEX 
	case 'index':
		
		$data['msg'] = [];
		
		if (!empty($_POST['submit'])) 
		{
			cek_hakakses('update_data');
			
			$form_errors = validate_form();
			$error = false;
						
			if ($form_errors) {
				$data['msg']['content'] = $form_errors;
				$error = true;
			} else {
				$db->beginTrans();
				
				$sql = 'DELETE FROM setting_register';
				$db->query($sql);
				
				$param_value = ['enable', 'metode_aktivasi', 'id_role'];
				foreach ($param_value as $value) {
					$data_db[] = ['param' => $value, 'value' => $_POST[$value]];
				}
				
				$db->insertBatch('setting_register', $data_db);
				
				$result = $db->completeTrans();
				
				if ($result) {
					$data['msg']['status'] = 'ok';
					$data['msg']['content'] = 'Data berhasil disimpan';
				} else {
					$data['msg']['content'] = 'Data gagal disimpan';
					$error = true;
				}
				
			}
			
			if ($error) {
				$data['msg']['status'] = 'error';
			}
		}
		
		$sql = 'SELECT * FROM setting_register';
		$query = $db->query($sql)->getResultArray();
		foreach($query as $val) {
			$data[$val['param']] = $val['value'];
		}
		
		$sql = 'SELECT * FROM role';
		$role = $db->query($sql)->result();
		$data['role'] = $role;

		$data['title'] = $current_module['judul_module'];
		load_view('views/form.php', $data);
}

function validate_form() 
{
	require_once('app/libraries/FormValidation.php');
	$validation = new FormValidation();
	$validation->setRules('enable', 'Diperbolehkan', 'trim|required');
	$validation->setRules('metode_aktivasi', 'Metode Aktivasi', 'trim|required');
	
	$validation->validate();
	$form_errors =  $validation->getMessage();
		
	return $form_errors;
}