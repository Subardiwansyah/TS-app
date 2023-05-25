<?php
function check_user($username) 
{
	global $db;
	$query = $db->query("SELECT * FROM user WHERE (username = '$username' or phone = '$username')")->row();

	return $query;		
}

function check_login() 
{
	global $db;
	
	$error = false;
	$user = check_user($_POST['username_login']);
	
	if ($user) {
		if (!password_verify($_POST['password_login'],$user['password'])) {
			$error = 'Username or password mismatch';
		} else {
			if ($user['verified'] == 0) {
				$error = 'Account not activated';
			}
		}
	} else {
		$error = 'Username or password mismatch';
	}
	
	if ($error) {
		return $error;
	} else {
		delete_auth_cookie($user['id_user']);
		
		if (!empty($_POST['remember']))
		{
			global $app_auth;
			$token = $app_auth->generateDbToken();
			$expired_time = time() + (7*24*3600); // 7 h
			setcookie('remember', $token['selector'] . ':' . $token['external'], $expired_time, '/');
			$pin_token = mt_rand(100000,999999);
			
			$data = array ( 'id_user' => $user['id_user']
							, 'selector' => $token['selector']
							, 'token' => $token['db']
							, 'pin_token' => $pin_token
							, 'action' => 'remember'
							, 'created' => date('Y-m-d H:i:s')
							, 'expires' => date('Y-m-d H:i:s', $expired_time)
						);

			$db->insert('user_token', $data);
		}
				
		$user_detail = $db->query('SELECT * FROM user 
									WHERE id_user = ' . $user['id_user']
								)->row();

		$_SESSION ['user'] = $user_detail;
		$_SESSION['logged_in'] = true;
		
		if($user_detail){
			$data_db['last_login'] = date("Y-m-d H:i:s");
			$query = $db->update('user', $data_db, 'id_user = ' . $user['id_user']);
		}
		
		header('location:./');
	}
}

function get_user() 
{
	global $db;
	$sql = 'SELECT * FROM user';
	$result = $db->query($sql)->result();
	return $result;
}

function check_cookie($selector) 
{
	if (!empty($_COOKIE['remember'])) 
	{
		global $db;
		list($selector, $cookie_token) = explode(':', $_COOKIE['remember']);
		$sql = 'SELECT * FROM user_token WHERE selector = ?';
		$data = $db->query($sql, $selector);
		
		if ($app_auth->verifyToken($cookie_token, $data['token'])) {
		
			if ($data['expires'] > date('Y-m-d H:i:s')) 
			{
				$user_detail = $db->query('SELECT * FROM user 
										WHERE id_user = ?', $data['id_user']
									)->row();

				$_SESSION ['user'] = $user_detail;
				$_SESSION['logged_in'] = true;
			}
		}
	}
}

function delete_auth_cookie($id_user) 
{
	global $db;
	$db->delete('user_token', ['action' => 'remember', 'id_user' => $id_user]);
	setcookie('remember', '', time() - 360000, '/');	
}