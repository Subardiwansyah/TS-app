<?php
function get_setting_registrasi() {
	global $db;
	$sql = "SELECT * FROM setting_register";
	$query = $db->query($sql)->getResultArray();
	foreach($query as $val) {
		$setting_register[$val['param']] = $val['value'];
	}
	return $setting_register;
}

function get_setting_web() {
	global $db;
	$sql = "SELECT * FROM setting_web";
	$query = $db->query($sql)->getResultArray();
	foreach($query as $val) {
		$setting_web[$val['param']] = $val['value'];
	}
	return $setting_web;
}

function no_user($id_role) {
	global $db;
	$val_role = $db->query('SELECT * FROM role WHERE id_role=?', $id_role)->row();
	
	$val = $db->query('SELECT max(no_user) as no_user FROM user WHERE id_role=?', $id_role)->row();
	$urutan = (int) substr($val['no_user'], -4);
	$urutan++;

	$no_user = $val_role['singkatan'].date("y").date("m").sprintf("%04s", $urutan);
	return $no_user;
}

function no_case() {
	global $db;
	$val = $db->query("SELECT max(case_no) as case_no FROM wty_claim WHERE case_no like '".date("y")."%'")->row();
	$urutan = (int) substr($val['case_no'], -4);
	$urutan++;

	$no_case = date("y").date("m").sprintf("%04s", $urutan);
	return $no_case;
}