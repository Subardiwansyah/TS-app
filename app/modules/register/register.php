<?php
/**
*	PHPAdmin Template
*	Developed by: Agus Prawoto Hadi
*	Website		: www.jagowebdev.com
*	Year		: 2021
*/

$site_title = 'Registration Account';
$site_desc = 'Registration Account';
$title = 'Registration Account';

$js[] = $config['base_url'] . 'public/vendors/jquery/jquery-3.3.1.min.js';
$js[] = $config['base_url'] . 'public/vendors/bootstrap/js/bootstrap.min.js';
								
$styles[] = $config['base_url'] . 'public/vendors/bootstrap/css/bootstrap.min.css';
$styles[] = $config['base_url'] . 'public/vendors/font-awesome/css/font-awesome.min.css';
$styles[] = $config['base_url'] . 'public/themes/modern/css/register.css';

$js[] = $config['base_url'] . 'public/vendors/jquery.pwstrength.bootstrap/pwstrength-bootstrap.min.js';
$js[] =	$config['base_url'] . 'public/themes/modern/js/password-meter.js';

helper('registrasi');
$setting_register = get_setting_registrasi();
$setting_web = get_setting_web();

if ($setting_register['enable'] == 'N') {
	redirectto_login();
}

switch ($_GET['action']) 
{
	default:
		action_notfound();
		
	case 'index':
	
		csrf_settoken();
		$error = false;
			
		if (!empty($_POST['submit'])) 
		{
			$validation = csrf_validation();
			
			// Cek CSRF token
			if ($validation['status'] == 'error') {
				$message = ['status' => 'error', 'message' => $validation['message']];
				$error = true;
			}
			
			// Cek email belum diaktifkan
			if (!$error) {
				$sql = 'SELECT * FROM user WHERE email = "' . $_POST['email'] . '" AND verified = 0';
				$result = $db->query($sql)->getRowArray();
				if ($result) {
					$message['status'] = 'error';
					$message['message'] = 'Email already exist, user not activated. Resend Email Activation <a href="'.$config['base_url'].'resendlink" title="Resend Email">Resend email</a>';
					$error = true;
				}
			}
			
			// Cek isian form
			if (!$error) {
				// Trim $_POST
				array_map('trim', $_POST);
				$form_error = validate_form();
				
				if ($form_error) {
					$message['status'] = 'error';
					$message['message'] = $form_error;
					$error = true;
				}
			}
			
			// Submit data
			if (!$error) {
				
				$message['status'] = 'error';
				
				$db->beginTrans();

				$verified = $setting_register['metode_aktivasi'] == 'langsung' ? 1 : 0;
				
				$data_db['nama'] = $_POST['name'];
				$data_db['no_user'] = no_user($setting_register['id_role']);
				$data_db['email'] = $_POST['email'];
				$data_db['phone'] = $_POST['phone'];
				$data_db['address'] = $_POST['address'];
				$data_db['username'] = $_POST['email'];
				$data_db['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
				$data_db['verified'] = $verified;
				$data_db['status'] = 1;
				$data_db['created'] = date('Y-m-d H:i:s');
				$data_db['id_role'] = $setting_register['id_role'];
				$insert_user = $db->insert('user', $data_db);
				$id_user = $db->lastInsertId();
				
				$error = false;
				if (!$insert_user)
				{
					$message['message'] = 'System error, please try again later...';
					$db->rollbackTrans();
					$error = true;
				
				} else {
					
					if ($setting_register['metode_aktivasi'] == 'manual') 
					{
						$message['message'] = 'Thank you for registration, please wait until admin approves your registration';
						
					} else if ($setting_register['metode_aktivasi'] == 'langsung') {
						
						$message['message'] = 'Thank you for registration, your account has been activated successfully. <a href="' . $config['base_url'] . '/login">Sign in</a>';
						
					} else if ($setting_register['metode_aktivasi'] == 'email') {
						
						$send_email = send_confirm_email($id_user);
					
						if ($send_email['status'] == 'error')
						{
							$message['message'] = "Error: can't send email activation link <strong>" . $send_email['status'] . '</strong>';
							$error = true;
						} else {
							$message['message'] = 'Thanks for your registration, please check your inbox <strong>' . $_POST['email'] . '</strong>. If you did not receive it please check your spam folder. Do contact us when you can’t find the activation mail. Contact us : '.$setting_web['email_support'];
						}
					}
					
					if (!$error) {
						$db->commitTrans();
						$message['status'] = 'ok';
						//$page_content = 'views/show_message.php';
					}
				}
			}	
		}
		
	    $page_content = 'views/form.php';
		if (!empty($_POST['submit']) && !$error) {
			//$page_content = 'app/themes/modern/show-message-register.php';
		}

		include 'app/themes/modern/header-register.php';
		include $page_content;
		include 'app/themes/modern/footer-register.php';
		break;
	
	case 'confirm': 

		$error = false;
		$message['status'] = 'error';
		
		if (empty($_GET['token'])) {
			$message_content = 'Token not found';
			$error = true;
		} else {
		
			@list($selector, $url_token) = explode(':', $_GET['token']);
			if (!$selector || !$url_token) {
				$message_content = 'Token not found';
				$error = true;
			}
		}
		
		if (!$error) {
			
			$sql = 'SELECT * FROM user_token
				WHERE selector = ?';
			$dbtoken = $db->query($sql, $selector)->getRowArray();
			
			if ($dbtoken) 
			{
				$error = false;
				
				$sql = 'SELECT * FROM user
				WHERE id_user = ?';
				$user = $db->query($sql, $dbtoken['id_user'])->getRowArray();
				
				if ($user['verified'] == 1) {
					$message_content = 'Your account was activated';
					$error = true;
				} 
				else if ($dbtoken['expires'] < date('Y-m-d H:i:s')) {
					$message_content = 'Link expired. Resend Email Activation <a href="'. $config['base_url'].'resendlink">Resend email</a>';
					$error = true;
				} 
				else if (!$app_auth->validateToken($url_token, $dbtoken['token'])) {
					$message_content = 'Token invalid, Resend Email Activation <a href="'. $config['base_url'].'resendlink">Resend email</a>';
					$error = true;
				}
				
			} else {
				$message_content = 'Token not found or account was activated';
				$error = true;
			}
		}
		
		if (!$error)
		{
			$db->beginTrans();

			$query = $db->delete('user_token', ['selector' => $selector]);
			$query = $db->delete('user_token', ['action' => 'register', 'id_user' => $dbtoken['id_user']]);
			
			$sql = 'UPDATE user SET verified = 1 WHERE id_user = ?';
			$query = $db->update('user', ['verified' => 1], ['id_user' => $dbtoken['id_user']]);
			
			$update = $db->completeTrans();
		
			if ($update) {
				$message['status'] = 'ok';
				$message_content = 'Regard, Your account was activated. Login account <a href="'.$config['base_url'].'login">Sign In</a>';
			} else {
				$this->data['message'] = 'Token not found, try again or contact us <a href="mailto:' . $config['contact_email'] . '" title="contact us">' . $config['contact_email'] . '</a>';
			}					
		}
		
		$message['message'] = $message_content;
		include 'app/themes/modern/header-register.php';
		include 'app/themes/modern/show-message-register.php';
		include 'app/themes/modern/footer-register.php';
}

function send_confirm_email($id_user) 
{
	global $app_auth, $db, $config;
	
	$token = $app_auth->generateDbToken();
	$data_db = [];
	$data_db['selector'] = $token['selector'];
	$data_db['token'] = $token['db'];
	$data_db['pin_token'] = mt_rand(100000,999999);
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
								, $_POST['name']
								, email_registration_content()
							);
							
	$email_content = str_replace('{{url}}', $url, $email_content);
	$email_content = str_replace('{{JUDUL_WEB}}', $setting_web['judul_web'], $email_content);
	$email_content = str_replace('{{EMAIL_SUPPORT}}', $setting_web['email_support'], $email_content);
	$email_content = str_replace('{{COMPANY}}', $setting_web['company'], $email_content);
	
	require_once 'app/config/email.php';
	$email_config = new EmailConfig;
	$email_data = array('from_email' => $config['email_support']
					, 'from_title' => $setting_web['company']
					, 'to_email' => $_POST['email']
					, 'to_name' => $_POST['name']
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

function validate_form() {
	global $db;
	helper ('form_requirement');
	
	$error = [];
	
	$form_field = ['name' => 'Name'
				, 'email' => 'Email'
				, 'password' => 'Password'
				, 'password_confirm' => 'Confirm Password'
			];
	
	foreach ($form_field as $field => $field_title) {
		if (empty($_POST[$field])) {
			$error[] = 'Field ' . $field_title . ' must be entered';
		}
	}
	
	if (!$error) 
	{
		if (strlen($_POST['name']) < 5) {
			$error[] = 'Field ' . $form_field['name'] . ' must constains at least 5 character';
		}
				
		// Passsword
		if ($_POST['password'] !== $_POST['password_confirm']) {
			$error[] = 'Password and confirm password does not match';
		}
		
		// Phone
		if (!is_numeric($_POST['phone'])) {
			$error[] = 'Invalid mobile number';
		}
		
		$invalid = password_requirements($_POST['password']);
		if ($invalid) {
			$error = array_merge($error, $invalid);
		}
		
		// Email
		$invalid = email_requirements($_POST['email']);
		if ($invalid) {
			$error = array_merge($error, $invalid);
		}

		$sql = 'SELECT * FROM user WHERE email = "' . $_POST['email'] . '"';
		$result = $db->query($sql)->getRowArray();
		if ($result) {
			$error[] = 'Email already used';
		}
		
		// Phone
		$sql = 'SELECT * FROM user WHERE phone = "' . $_POST['phone'] . '"';
		$result = $db->query($sql)->getRowArray();
		if ($result) {
			$error[] = 'Phone already used';
		}
		
		// Username
		/*
		$sql = 'SELECT * FROM user WHERE username = "' . $_POST['email'] . '"';
		$result = $db->query($sql)->getRowArray();
		if ($result) {
			$error[] = 'Username already used';
		}
		*/
	}
	return $error;
}