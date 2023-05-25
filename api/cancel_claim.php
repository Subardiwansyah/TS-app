<?php
    
include "config.php";
$data = array();
    $data['status'] = false;
$id_wty_claim = $_POST['id_wty_claim'];
    
$data['data'] = $id_wty_claim;
    
    $get = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM wty_claim WHERE id_wty_claim = '".$id_wty_claim."' "));
    
    if($get['id_case_status'] == '1' ){
        $data['status'] = true;
        $sql = "DELETE FROM wty_claim WHERE id_wty_claim = '".$id_wty_claim."' ";
        
        $delete = mysqli_query($conn, $sql);
    } else {
        $data['mssg'] = "Cannot cancel this claim.";
    }
    
    

    
echo json_encode($data);
