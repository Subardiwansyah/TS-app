<?php
function password_requirements($password, $field_title = 'Password') {
	
	$error = [];
	if (strlen($password) < 8) {
		$error[] = $field_title . ' must constains at least 8 character';
	}
	
	preg_match_all("/[a-z]/", $password, $match);
	if (!$match[0]) {
		$error[] = $field_title . ' must contains at least one small letter';	
	}
	preg_match_all("/[A-Z]/", $password, $match);
	if (!$match[0]) {
		$error[] = $field_title . ' must contains at least one capital letter';
	}
	preg_match_all("/[0-9]/", $password, $match);
	if (!$match[0]) {
		$error[] = $field_title . ' must contains at least one digit character';
	}
	
	return $error;
}

function email_requirements($email, $field_title = 'Email') {
	
	$error = [];
	if(!preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $_POST['email'])){
		$error[] = 'Invalid email address';
	}
	
	return $error;
}