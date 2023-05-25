<?php

date_default_timezone_set("Asia/Jakarta");

$host = 'localhost';
$user = 'bardiweb_bardidb';
$password = 'Bardi123$';
$db_name = 'bardiweb_ts';

$conn = mysqli_connect($host, $user, $password, $db_name);

if(!$conn){
	echo "Could not connect to database";
}

function tgl_indo($tanggal){
	$bulan = array (
		1 =>   'Januari',
		'Februari',
		'Maret',
		'April',
		'Mei',
		'Juni',
		'Juli',
		'Agustus',
		'September',
		'Oktober',
		'November',
		'Desember'
	);
	$pecahkan = explode('-', $tanggal);
 
	return $pecahkan[2] . ' ' . $bulan[ (int)$pecahkan[1] ] . ' ' . $pecahkan[0];
}

?>