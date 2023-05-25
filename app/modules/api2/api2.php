<?php
helper('registrasi');
$setting_register = get_setting_registrasi();
$data = array();

if(empty($_POST['action'])){
	$fullName = $_POST['fullname'];
	$phoneNumber = $_POST['phonenumber'];
	$emailAddress = $_POST['emailaddress'];
	$address = $_POST['address'];
	$province = $_POST['province'];
	$city = $_POST['city'];
	$subdistrict = $_POST['subdistrict'];

	$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
	$confirmPassword = $_POST['confirmpassword'];

	$data['status'] = false;
	$data['exist'] = false;

	if(!is_numeric($phoneNumber)){
		$data['mssg'] = "Invalid phone number";
	}

	else if(!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)){
		$data['mssg'] = "Invalid email format";
	} else if($_POST['password'] != $_POST['confirmpassword']){
		$data['mssg'] = "Error confirm password";
	} else {
		
		$cek = $db->query('SELECT * FROM user WHERE email = "'.$emailAddress.'" ')->getRowArray();
		$phone = $db->query('SELECT * FROM user WHERE phone = "'.$phoneNumber.'" ')->getRowArray();
		
		if(!empty($cek)){
			$data['mssg'] = "Email Address already exist!";
			$data['exist'] = true;
			
		} else if(!empty($phone)){
			$data['mssg'] = "Phone Number already exist!";
			$data['exist'] = true;
		} else {
			$data['status'] = true;
		}
		
	}

	$sub = $db->query('SELECT * FROM kecamatan WHERE id_kec = "'.$subdistrict.'" ' )->getRowArray();
	$kota = $db->query('SELECT * FROM kabupaten WHERE id_kab = "'.$city.'" ' )->getRowArray();
	$prov = $db->query('SELECT * FROM provinsi WHERE id_prov = "'.$province.'"' )->getRowArray();

	$completeAddress = $address.' Kec. '.$sub['nama'].' Kab/Kota. '.$kota['nama'].' '.$prov['nama'];

	if($data['status']){
		$data_db['nama'] 	= $_POST['fullname'];
		$data_db['no_user'] = no_user($setting_register['id_role']);
		$data_db['email'] 	= $_POST['emailaddress'];
		$data_db['phone'] 	= $_POST['phonenumber'];
		$data_db['address'] = $completeAddress;
		$data_db['id_prov'] = $province;
		$data_db['id_kab'] = $city;
		$data_db['id_kec'] = $subdistrict;
		$data_db['detail_address'] = $address;
		$data_db['username'] = $_POST['emailaddress'];
		$data_db['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
		$data_db['verified'] = '0';
		$data_db['status'] = 1;
		$data_db['created'] = date('Y-m-d H:i:s');
		$data_db['id_role'] = '4';
		$insert_user = $db->insert('user', $data_db);
		$id_user = $db->lastInsertId();
		$send_email = send_confirm_email($id_user);
		
		$data['email'] = $_POST['emailaddress'];
		$data['id_user'] = $id_user;
		
	}
} else if($_POST['action'] == 'RESEND') {
	$id_user = $_POST['id_user'];
	if(!empty($id_user)){
		$data['status'] = true;
		$send_email = send_confirm_email($id_user);
		$data['mssg'] = "Berhasil.";
	} else {
		$data['status'] = false;
		$data['mssg'] = "Resend Failed.";
	}
}

echo json_encode($data); 


function send_confirm_email($id_user){
	global $app_auth, $db, $config;
	$token = $app_auth->generateDbToken();
	$pin_token = mt_rand(100000,999999);
	$data_db = [];
	$data_db['selector'] = $token['selector'];
	$data_db['token'] = $token['db'];
	$data_db['pin_token'] = $pin_token;
	$data_db['action'] = 'register';
	$data_db['id_user'] = $id_user;
	$data_db['created'] = date('Y-m-d H:i:s');
	$data_db['expires'] = date('Y-m-d H:i:s', strtotime('+1 hour'));
	
	$insert_token = $db->insert('user_token', $data_db);
	
	helper('email');
	helper('registrasi');
	$setting_web = get_setting_web();

	$url_token = $token['selector'] . ':' . $token['external'];
	$url = $config['base_url'].'register/confirm?token='.$url_token;
	$email_content = str_replace('{{NAME}}'
								, $_POST['fullname']
								, email_pin_content()
							);
	
	$email_content = str_replace('{{url}}', $url, $email_content);
	$email_content = str_replace('{{PIN}}', $pin_token, $email_content);
	$email_content = str_replace('{{JUDUL_WEB}}', $setting_web['judul_web'], $email_content);
	$email_content = str_replace('{{EMAIL_SUPPORT}}', $setting_web['email_support'], $email_content);
	$email_content = str_replace('{{COMPANY}}', $setting_web['company'], $email_content);
	
	require_once 'app/config/email.php';
	$email_config = new EmailConfig;
	$email_data = array('from_email' => $config['email_support']
					, 'from_title' => $setting_web['company']
					, 'to_email' => $_POST['emailaddress']
					, 'to_name' => 'Bardi'
					, 'email_subject' => 'Link Activation Account'
					, 'email_content' => $email_content
					, 'images' => ['logo_text' => BASEPATH . 'public/images/'.$setting_web['logo_login']]
	);
	
	require_once('app/libraries/SendEmail.php');

	$emaillib = new \App\Libraries\SendEmail;
	$emaillib->init();
	$emaillib->setProvider($email_config->provider);
	$send_email =  $emaillib->send($email_data);
	
	return $send_email;
}

	
?>
