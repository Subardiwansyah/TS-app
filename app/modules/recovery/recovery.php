<?php
/**
*	PHPAdmin Template
*	Developed by: Agus Prawoto Hadi
*	Website		: www.jagowebdev.com
*	Year		: 2021
*/

$site_title = 'Reset Password';
$site_desc = 'Reset Password';
$title = 'Reset Password';

$js[] = $config['base_url'] . 'public/vendors/jquery/jquery-3.3.1.min.js';
$js[] = $config['base_url'] . 'public/vendors/bootstrap/js/bootstrap.min.js';

$styles[] = $config['base_url'] . 'public/vendors/bootstrap/css/bootstrap.min.css';
$styles[] = $config['base_url'] . 'public/themes/modern/css/register.css';

$js[] = $config['base_url'] . 'public/vendors/jquery.pwstrength.bootstrap/pwstrength-bootstrap.min.js';
$js[] =	$config['base_url'] . 'public/themes/modern/js/password-meter.js';

switch ($_GET['action']) 
{
	default:
		action_notfound();
		
	case 'index':
	
		csrf_settoken();
		$error = false;
		
		$message = [];
		helper('registrasi');
		$setting_web = get_setting_web();
		
		if (!empty($_POST['submit'])) 
		{
			// Cek isian form
			array_map('trim', $_POST);
			$form_error = validate_form();
			
			$message['status'] = 'error';
			if ($form_error) {
				$message['message'] = $form_error;
				$error = true;
			}
			
			// Submit data
			if (!$error) {
				$sql = 'SELECT * FROM user WHERE email = ?';
				$user = $db->query($sql, $_POST['email'])->getRowArray();
				
				$db->beginTrans();
				
				$db->delete('user_token', ['action' => 'recovery', 'id_user' => $user['id_user']]);
				$token = $app_auth->generateDbToken();
				$data_db['selector'] = $token['selector'];
				$data_db['token'] = $token['db'];
				$data_db['pin_token'] = mt_rand(100000,999999);
				$data_db['action'] = 'recovery';
				$data_db['id_user'] = $user['id_user'];
				$data_db['created'] = date('Y-m-d H:i:s');
				$data_db['expires'] = date('Y-m-d H:i:s', strtotime('+1 hour'));
				
				$insert_token = $db->insert('user_token', $data_db);
				helper('email');
				
				// $save = true;
				if ($insert_token)
				{
					$url_token = $token['selector'] . ':' . $token['external'];
					$url = $config['base_url'].'recovery/reset?token='.$url_token;					
										
					$email_content = str_replace('{{NAME}}'
													, $user['nama']
													, email_resendlink_content()
												);
												
					$email_content = str_replace('{{url}}', $url, $email_content);
					$email_content = str_replace('{{EMAIL_SUPPORT}}', $setting_web['email_support'], $email_content);
					$email_content = str_replace('{{COMPANY}}', $setting_web['company'], $email_content);
			
					require_once 'app/config/email.php';
					$email_config = new EmailConfig;
					$email_data = array('from_email' => $setting_web['email_support']
									, 'from_title' => $setting_web['company']
									, 'to_email' => $_POST['email']
									, 'to_name' => $_POST['email']
									, 'email_subject' => 'Reset Password'
									, 'email_content' => $email_content
									, 'images' => ['logo_text' => BASEPATH . 'public/images/'.$setting_web['logo_login']]
					);
					
					require_once('app/libraries/SendEmail.php');

					$emaillib = new \App\Libraries\SendEmail;
					$emaillib->init();
					$emaillib->setProvider($email_config->provider);
					$send_email =  $emaillib->send($email_data);
				
					if ($send_email['status'] == 'ok')
					{
						$db->commitTrans();
						
						$message['status'] = 'ok';
						$message['message'] = 'Please check your inbox <strong>' . $_POST['email'] . '</strong>. If you did not receive it please check your spam folder. Do contact us when you can’t find the confirmation mail. Contact us : '.$setting_web['email_support'];
					} else {
						$message['message'] = "Error: can't send email activation link <strong>" . $send_email['status'] . '</strong>';
						$error = true;
					}
				} else {
					$message['message'] = 'Failed save token, please contact us <a href="mailto:'.$config['email_support'].'" target="_blank">'.$config['email_support'].'</a>';
					$error = true;
				}
			}
		}
		
		$page_content = 'views/form_recovery.php';
		
		if (!empty($_POST['submit']) && !$error) {
			$page_content = 'app/themes/modern/show-message-register.php';
		}
		
		include 'app/themes/modern/header-register.php';
		include $page_content;
		include 'app/themes/modern/footer-register.php';
		break;
	
	case 'reset': 

		$error = false;
		$message = [];
		
		if (empty($_GET['token'])) {
			$message['message'] = 'Token not found';
			$error = true;
		} else {
		
			@list($selector, $url_token) = explode(':', $_GET['token']);
			if (!$selector || !$url_token) {
				$message['message'] = 'Token not found';
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
				if ($dbtoken['expires'] < date('Y-m-d H:i:s')) {
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
			if (!empty($_POST['submit'])) {
				// Cek isian form
				array_map('trim', $_POST);
				$form_error = validate_form_reset();

				if ($form_error) {
					$message['message'] = $form_error;
					$error = true;
				}
				
				// Submit data
				if (!$error) {
					
					$db->delete('user_token', ['selector' => $selector]);
					$update = $db->update('user', ['password' => password_hash($_POST['password'], PASSWORD_DEFAULT)] , ['id_user' => $dbtoken['id_user']]);
					if ($update) {
						$message['status'] = 'ok';
						$message['message'] = 'Password has been changed. Login account <a href="'.$config['base_url'].'login">Sign In</a>';
					} else {
						$message['message'] = 'Failed to change password, try again or contact us <a href="mailto:' . $config['contact_email'] . '" title="Contact us">' . $config['contact_email'] . '</a>';
						$error = true;
					}		
					
				}
			}
		}
		
		$page_content = 'views/form_reset_password.php';
		
		if (!empty($_POST['submit']) && !$error) {
			$page_content = 'app/themes/modern/show-message-register.php';
		}
		
		if ($error) {
			$message['status'] = 'error';
		}
		
		include 'app/themes/modern/header-register.php';
		include $page_content;
		include 'app/themes/modern/footer-register.php';
}

function validate_form_reset() 
{
	$error = [];
	
	$validation = csrf_validation();
	// Cek CSRF token
	if ($validation['status'] == 'error') {
		return [$validation['message']];
	}
	
	$form_field = ['password' => 'Password'
				, 'password_confirm' => 'Confirm Password'
			];
	
	foreach ($form_field as $field => $field_title) {
		if (empty($_POST[$field])) {
			$error[] = 'Field ' . $field_title . ' must be entered';
		}
	}
	
	if (!$error) {
		
		helper('form_requirement');
		if ($_POST['password'] !== $_POST['password_confirm']) {
			$error[] = 'Password and confirm password does not match';
		}
		
		$invalid = password_requirements($_POST['password']);
		if ($invalid) {
			$error = array_merge($error, $invalid);
		}
	}
	return $error;
}
	
function validate_form() 
{
	global $db;
	global $config;
	
	$error = [];
	
	$validation = csrf_validation();
	// Cek CSRF token
	if ($validation['status'] == 'error') {
		return [$validation['message']];
	}
	
	if (empty($_POST['email'])) {
		$error[] = 'Email address must be entered';
	} 
	else if (!strpos($_POST['email'], '@')) {
		$error[] = 'Invalid email address';
	}
	
	if (!$error) 
	{		
		$sql = 'SELECT * FROM user WHERE email = "' . $_POST['email'] . '"';
		$result = $db->query($sql)->getRowArray();
		if ($result) {
			if ($result['verified'] == 0) {
				$error[] = 'Email not activated. Resend activation link <a href="' . $config['base_url'] . 'resendlink" title="Resend email aktivasi">Resend email</a>';
			}
		} else {
			$error[] = 'Email not registered <a href="' . $config['base_url'] . 'register" title="Registration">Registration</a>';
		}
	}
	return $error;
}