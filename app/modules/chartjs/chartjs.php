<?php
/**
* PHP Admin Template
* Author	: Agus Prawoto Hadi
* Website	: https://jagowebdev.com
* Year		: 2021
*/

$js[] = BASE_URL . 'public/vendors/chartjs/Chart.bundle.min.js';
$styles[] = BASE_URL . 'public/vendors/chartjs/Chart.min.css';

switch ($_GET['action']) 
{
    default: 
        action_notfound();
    
    	// INDEX 
    case 'index':
	
		$list_tahun = [2019, 2020, 2021];
		
		$tahun = 2021;
		if (!empty($_GET['tahun']) && in_array($_GET['tahun'], $list_tahun)) {
			$tahun = $_GET['tahun']; 
		}
			
        $sql = 'SELECT MONTH(tgl_trx) AS bulan, COUNT(id_trx) as JML, SUM(total_harga) total
				FROM penjualan
				WHERE tgl_trx LIKE "' . $tahun . '%"
				GROUP BY MONTH(tgl_trx)';
				
        $penjualan = $db->query($sql)->getResultArray();
		
		$sql = 'SELECT id_produk, nama, COUNT(id_produk) AS jml
				FROM penjualan_detail
				LEFT JOIN penjualan USING(id_trx)
				LEFT JOIN barang USING(id_produk)
				WHERE tgl_trx LIKE "' . $tahun . '%"
				GROUP BY id_produk
				ORDER BY jml DESC LIMIT 7';
		
		$item_terjual = $db->query($sql)->getResultArray();
		
        $data['penjualan'] = $penjualan;
        $data['item_terjual'] = $item_terjual;
        $data['tahun'] = $tahun;

        if (!$data['penjualan'])
            data_notfound();

        load_view('views/result.php', $data);
}