<?php

header('Access-Control-Allow-Origin: *');
header('Content-type: application/json');
//header('Access-Control-Allow-Method: POST');
header('Access-Control-Allow-Headers: Origin, Content-Type, Accept');

//if($_SERVER['REQUEST_METHOD'] === 'POST'){

    include "config.php";
    $data = array();
    $id = $_POST['id'];
    

    $get = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM user WHERE id_user = '".$id."' "));

    //$sql = "SELECT * FROM wty_claim a LEFT JOIN wty b ON a.sku = b.sku LEFT JOIN sku c ON a.sku = b.sku WHERE a.customer_no = '".$get['no_user']."' ";

    $sql = "SELECT a.*, b.* FROM wty a LEFT JOIN sku b ON a.sku = b.sku LEFT JOIN store c ON a.id_store = c.id_store WHERE a.customer_no = '".$get['no_user']."' ORDER BY a.updated_date DESC ";

    $query = mysqli_query($conn,$sql);

    $list = array();
    $data = array();


    while($row = mysqli_fetch_assoc($query)){
        
        if($row['id_wty_status'] == '2'){
            $row['wty_status'] = "Out Warranty";
        } else {
            $row['wty_status'] = "In Warranty";
        }

        $row['bought_date'] = $row['bought_date'] == "0000-00-00" ? "" : tgl_indo($row['bought_date']);

        $row['wty_end'] = tgl_indo($row['wty_end']);
        $row['wty_start'] = tgl_indo($row['wty_start']);
        
        $sql1 = "SELECT a.sku, a.serial_number, b.unit_status FROM wty a LEFT JOIN unit_status b ON a.id_unit_status = b.id_unit_status WHERE (a.customer_no is NULL or a.customer_no in ('','0')) and id_parent = '".$row['id_wty']."' ORDER BY id_wty ASC ";
        $query1 = mysqli_query($conn, $sql1);

        $count = mysqli_num_rows($query1);
        if($count > 0){
            $row['jumhis'] = 1;
        } else {
            $row['jumhis'] = 0;
        }
        

        $row['get'] = array('sku'=>'');
        $row['get1'] = array('sku'=>'');
        $row['get2'] = array('sku'=>'');
        $no = 1;
        while($d = mysqli_fetch_assoc($query1)){
            //array_push($data, $d);
            if($no == 1){
                $row['get'] = $d;
            } else if($no == 2){
                $row['get1'] = $d;
            } else if($no == 3){
                $row['get2'] = $d;
            }
            
            //$row['get'] = $d;
            $no++;
        }
        
        $list[] = $row;
        //array_push($list, $row);
    }

    echo json_encode($list);
//} else {
//    die(header('HTTP/1.1 405 Request Method Not Allowed'));
// }