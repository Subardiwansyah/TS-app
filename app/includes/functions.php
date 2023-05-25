<?php
/**
* PHP Admin Template
* Author	: Agus Prawoto Hadi
* Website	: https://jagowebdev.com
* Year		: 2021
*/
/* Create breadcrumb
$data: title as key, and url as value */ 

function breadcrumb($data) 
{
	$separator = '&raquo;';
	echo '<nav aria-label="breadcrumb">
  <ol class="breadcrumb">';
	foreach ($data as $title => $url) {
		if ($url) {
			echo '<li class="breadcrumb-item"><a href="'.$url.'">'.$title.'</a></li>';
		} else {
			echo '<li class="breadcrumb-item active" aria-current="page">'.$title.'</li>';
		}
	}
	echo '
  </ol>
</nav>';
}

function nama_bulan() 
{
	return [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']; 
}

// $date harus yyyy-mm-dd
function format_tanggal($date, $format = 'dd mmmm yyyy') 
{
	// echo $format;
	
	if ($date == '0000-00-00' || $date == '')
		return $date;
	
	$format = strtolower($format);
	$new_format = $date;
	
	list($year, $month, $date) = explode('-', $date);
	if (strpos($format, 'dd') !== false) {
		$new_format = str_replace('dd', $date, $format);
	}
	
	if (strpos($format, 'mmmm') !== false) {
		$bulan = nama_bulan();
		$new_format = str_replace('mmmm', $bulan[ ($month * 1) ], $new_format);
	} else if (strpos($format, 'mm') !== false) {
		$new_format = str_replace('mm', $month, $new_format);
	}
	
	if (strpos($format, 'yyyy') !== false) {
		$new_format = str_replace('yyyy', $year, $new_format);
	}
	return $new_format;
}

function format_tanggal_indo($date) 
{
	$time="";
	if(preg_match('/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/',$date)){
		$var=explode(" ",$date);
		$date=$var[0];
		$time=$var[1];
	}	
	
	if ($date == '0000-00-00' || $date == '')
		return $date;
	
	//$bulan = nama_bulan();
	$exp = explode('-', $date);
	
	if($time!=""){
		$time=', '.$time;
	}
	return $exp[2] . '/' . $exp[1] . '/' . $exp[0] . $time; // * untuk mengubah 02 menjadi 2
}

function format_tanggal_db($date) 
{
	$time="";
	if(preg_match('/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/',$date)){
		$var=explode(" ",$date);
		$date=$var[0];
		$time=$var[1];
	}	
	
	if ($date == '0000-00-00' || $date == '')
		return $date;
	
	$bulan = nama_bulan();
	$exp = explode('-', $date);
	
	if($time!=""){
		$time=', '.$time;
	}
	return $exp[2] . ' ' . $bulan[ ($exp[1] * 1) ] . ' ' . $exp[0] . $time; // * untuk mengubah 02 menjadi 2
}

function validateDate($date)
{
    $formats = ['Y-m-d','Y-m-d H:i:s','Y-m-d H:i:s.u'];
    foreach($formats as $format) {
        $d = DateTime::createFromFormat($format, $date);
        if ($d && $d->format($format) == $date) return true;
    }
    return false;
}

function prepare_datadb($data) {
	foreach ($data as $field) {
		$result[$field] = $_POST[$field];
	}
	return $result;
}

function get_menu_db ($aktif = 'all', $show_all = false) 
{
	global $db;
	global $current_module;
	// print_r($current_module);
	$result = [];
	$nama_module = $current_module['nama_module'];
	
	$where = ' ';
	$where_aktif = '';
	if ($aktif != 'all') {
		$where_aktif = ' AND aktif = '.$aktif;
	}
	
	$role = '';
	if (!$show_all) {
		$role = ' AND id_role = ' . $_SESSION['user']['id_role'];
	}
	
	$sql = 'SELECT * FROM menu 
				LEFT JOIN menu_role USING (id_menu)
				LEFT JOIN module USING (id_module)
			WHERE 1 = 1 ' . $role
				. $where_aktif.' 
			ORDER BY urut';
	
	$db->query($sql);
	
	$current_id = '';
	while ($row = $db->fetch()) 
	{
		$result[$row['id_menu']] = $row;
		$result[$row['id_menu']]['highlight'] = 0;
		$result[$row['id_menu']]['depth'] = 0;

		if ($nama_module == $row['nama_module']) {
			
			$current_id = $row['id_menu'];
			$result[$row['id_menu']]['highlight'] = 1;
		}
		
	}
	// echo '<pre>'; print_r($result);
	
	if ($current_id) {
		menu_current($result, $current_id);
	}
	
	return $result;
}

function menu_current( &$result, $current_id) 
{
	$parent = $result[$current_id]['id_parent'];

	$result[$parent]['highlight'] = 1; // Highlight menu parent
	if (@$result[$parent]['id_parent']) {
		menu_current($result, $parent);
	}
}

function menu_list($result)
{
	$refs = array();
	$list = array();
	// echo '<pre>'; print_r($result);
	foreach ($result as $key => $data)
	{
		if (!$key || empty($data['id_menu'])) // Highlight OR No parent
			continue;
		
		$thisref = &$refs[ $data['id_menu'] ];
		foreach ($data as $field => $value) {
			$thisref[$field] = $value;
		}

		// no parent
		if ($data['id_parent'] == 0) {
			
			$list[ $data['id_menu'] ] = &$thisref;
		} else {
			
			$thisref['depth'] = ++$refs[ $data['id_menu']]['depth'];			
			$refs[ $data['id_parent'] ]['children'][ $data['id_menu'] ] = &$thisref;
		}
	}
	set_depth($list);	
	return $list;
}

function set_depth(&$result, $depth = 0) {
	foreach ($result as $key => &$val) 
	{
		$val['depth'] = $depth;
		if (key_exists('children', $val)) {
			set_depth($val['children'], $val['depth'] + 1);
		}
	}
}

function build_menu( $arr, $submenu = false)
{
	global $current_module;
	$menu = "\n" . '<ul'.$submenu.'>'."\r\n";
	// echo '<pre>';
	// print_r($arr);
	foreach ($arr as $key => $val) 
	{
	// echo '<pre>ff'; print_r($arr); die;
		if (!$key)
			continue;
	
		// Check new
		$new = '';
		if (key_exists('new', $val)) {
			$new = $val['new'] == 1 ? '<span class="menu-baru">NEW</span>' : '';
		}
		$arrow = key_exists('children', $val) ? '<span class="pull-right-container">
								<i class="fa fa-angle-left arrow"></i>
							</span>' : '';
		$has_child = key_exists('children', $val) ? 'has-children' : '';
		
		if ($has_child) {
			$url = '#';
			$onClick = ' onclick="javascript:void(0)"';
		} else {
			$onClick = '';
			$url = $val['url'];
		}
		
		// class attribute for <li>
		$class_li = [];		
		if ($current_module['nama_module'] == $val['nama_module']) {
			$class_li[] = 'tree-open';
		}
		
		if ($val['highlight']) {
			$class_li[] = 'highlight tree-open';
		}
		
		if ($class_li) {
			$class_li = ' class="' . join(' ', $class_li) . '"';
		} else {
			$class_li = '';
		}
		
		// Class attribute for <a>, children of <li>
		$class_a = ['depth-' . $val['depth']];
		if ($has_child) {
			$class_a[] = 'has-children';
		}
		
		$class_a = ' class="' . join(' ', $class_a) . '"';
		
		// Menu icon
		$menu_icon = '';
		if ($val['class']) {
			$menu_icon = '<i class="sidebar-menu-icon ' . $val['class'] . '"></i>';
		}

		// Menu
		$menu .= '<li'. $class_li . '>
					<a '.$class_a.' href="'. BASE_URL . $url.'"'.$onClick.'>'.
						$menu_icon.
						$val['nama_menu']. " ".
						$new.
						$arrow.
					'</a>';
		
		if (key_exists('children', $val))
		{ 	
			$menu .= build_menu($val['children'], ' class="submenu"');
		} 
		$menu .= "</li>\n";
	}
	$menu .= "</ul>\n";
	return $menu;
}

function create_image_mime ($tipe_file, $newfile)
{
	switch ($tipe_file)
	{
		case "image/gif":
			return imagecreatefromgif($newfile);
			
		case "image/png":
			return imagecreatefrompng($newfile);
			
		case "image/bmp":
			return imagecreatefrombmp($newfile);
			
		default:
			return imagecreatefromjpeg($newfile);		
	}
}
	
function create_image ($tipe_file, $resized_img, $newfile)
{
	switch ($tipe_file)
	{
		case "image/gif":
			return imagegif ($resized_img,$newfile, 85);
			
		case "image/png":
			imagesavealpha($resized_img, true);
			$color = imagecolorallocatealpha($resized_img, 0,0,0,127);
			imagefill($resized_img, 0,0, $color);
			return imagepng ($resized_img,$newfile, 9);
			
		case "image/bmp":
			return imagecreatefrombmp($newfile);
			
		default:
			return imagejpeg ($resized_img,$newfile, 85);
			
	}
}

function get_filename($file_name, $path) {
	
	$file_name_path = $path . $file_name;
	if ($file_name != "" && file_exists($file_name_path))
	{
		$file_ext = strrchr($file_name, '.');
		$file_basename = substr($file_name, 0, strripos($file_name, '.'));
		$num = 1;
		while (file_exists($file_name_path)){
			$file_name = $file_basename."($num)".$file_ext;
			$num++;
			$file_name_path = $path . $file_name;
		}
		
		return $file_name;
	}
	return $file_name;
}

function upload_image($path, $file, $max_w = 500, $max_h = 500, $file_name='') 
{
	
	$file_type = $file['type'];
	if($file_name==""){
		$new_name =  get_filename(stripslashes($file['name']), $path); 
	}else{
		$new_name = $file_name;
	}	
	
	$move = move_uploaded_file($file['tmp_name'], $path . $new_name);
	
	$save_image = false;
	/*if ($move) {
		$dim = image_dimension($path . $new_name, $max_w, $max_h);
		$save_image = save_image($path . $new_name, $file_type, $dim[0], $dim[1]);
	}
	
	if ($save_image)
		return $new_name;
	else
		return false;
	*/
	return $new_name;
}

function image_dimension($images, $maxw=null, $maxh=null)
{
	if($images)
	{
		$img_size = @getimagesize($images);
		$w = $img_size[0];
		$h = $img_size[1];
		$dim = array('w','h');
		foreach($dim AS $val){
			$max = "max{$val}";
			if(${$val} > ${$max} && ${$max}){
				$alt = ($val == 'w') ? 'h' : 'w';
				$ratio = ${$alt} / ${$val};
				${$val} = ${$max};
				${$alt} = ${$val} * $ratio;
			}
		}
		return array($w,$h);
	}
}

function save_image($image, $file_type, $w, $h) 
{
	$img_size = @getimagesize($image);
	
	$resized_img = imagecreatetruecolor($w,$h);
	$new_img = create_image_mime($file_type, $image);
	imagecopyresized($resized_img, $new_img, 0, 0, 0, 0, $w, $h, $img_size[0], $img_size[1]);
	$do = create_image($file_type, $resized_img, $image);
	ImageDestroy ($resized_img);
	ImageDestroy ($new_img);
	return $do;
}

function upload_file($path, $file) 
{
	$new_name =  get_filename(stripslashes($file['name']), $path); ;
	$move = move_uploaded_file($file['tmp_name'], $path . $new_name);
	if ($move) 
		return $new_name;
	else
		return false;
}

function upload_file2($path, $file) 
{
	$new_name =  stripslashes($file['name']);
	$move = move_uploaded_file($file['tmp_name'], $path . $new_name);
	if ($move) 
		return $new_name;
	else
		return false;
}

function get_dimensi_kartu($ori_panjang, $ori_lebar, $dpi) {
	// print_r($ori_panjang); die;
	$px = 0.393700787; 
	$panjang = $ori_panjang * $dpi * $px;
	$lebar = $ori_lebar * $dpi * $px;
	return ['w' => $panjang, 'h' => $lebar];
}

function generateQRCode($version, $ecc, $text, $module_width) {
	
	require BASE_PATH . 'libraries' . DS . 'vendors' . DS . 'qrcode' . DS . 'qrcode_extended.php';
	$qr = new QRCodeExtended();
	$ecc_code = ['L' => QR_ERROR_CORRECT_LEVEL_L
		, 'M' => QR_ERROR_CORRECT_LEVEL_M
		, 'Q' => QR_ERROR_CORRECT_LEVEL_Q
		, 'H' => QR_ERROR_CORRECT_LEVEL_H
	];
	$qr->setErrorCorrectLevel($ecc_code[$ecc]);
	$qr->setTypeNumber($version);
	$qr->addData($text);
	$qr->make();
	return $qr->saveHtml($module_width);
}