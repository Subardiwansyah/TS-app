<?php
/**
*	PHP Admin Template Jagowebdev
* 	Author	: Agus Prawoto Hadi
*	Website	: https://jagowebdev.com
*	Year	: 2021
*/

$site_title = 'Home';
$data['title'] = 'Home';
helper('registrasi');
$setting_web = get_setting_web();

$data['judul_web'] = $setting_web['judul_web'];

load_view('views/result.php', $data);