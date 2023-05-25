<?php 

$host = 'localhost';
$user = 'bardiweb_bardidb';
$password = 'Bardi123$';
$db_name = 'bardiweb_ts';

$conn = mysqli_connect($host, $user, $password, $db_name);

if(!$conn){
	echo "Could not connect to database";
}else {
	echo "Connected.";
}
?>