<?php
    
    header('Access-Control-Allow-Origin: *');
    header('Content-type: application/json');
    header('Access-Control-Allow-Method: POST');
    header('Access-Control-Allow-Headers: Origin, Content-Type, Accept');
    
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        
        include "config.php";
        $data           = array();
        $id             = $_POST['id'];
        $data['stat']   = false;
        
        $query = mysqli_query($conn, "SELECT * FROM user WHERE id_user = '".$id."' ");
        $list = array();
        while($row = mysqli_fetch_array($query)){
            $list[] = $row;
            //array_push($list, $row);
			if(empty($row['token'])){
				$sql = "UPDATE user SET token = '".$_POST['token']."' WHERE id_user = '".$id."' ";
				$update = mysqli_query($conn, $sql);
			}
        }
        
        //$data = $list;
         
        echo json_encode(array(
            "success" => true,
            "data" => $list
        ));
        
    } else {
        die(header('HTTP/1.1 405 Request Method Not Allowed'));
    }
