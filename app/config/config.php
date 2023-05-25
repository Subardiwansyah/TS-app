<?php
$config = [
	'base_url' => 'https://bardi.my.id/'
	, 'images_url' => 'https://bardi.my.id/public/images/'
	, 'theme' => 'modern'
	, 'default_module' => 'user'
	, 'user_images_path' => 'uploads/profile/'
	, 'produk_images_path' => 'uploads/katalog/'
	, 'images_path' => 'public/images/'
	, 'foto_path' => 'uploads/'
	, 'dokumen_path' => 'public/files/dokumen/'
	, 'email_support' => 'technosolution.notification@gmail.com'
];

/* CSRF */
/*
	Ini adalah konfigurasi untuk csrf auto dimana setiap kali request dilakukan (halaman dibuka), akan di generate token CSRF, token ini disimpan di cookie, selanjutnya setiap ada submit data dengan method POST, maka variabel $_POST akan dicek apakah token pada variabel tersebut sama dengan token yang ada di cookie. Isikan true jika ingin fitur ini diaktifkan dan isikan false jika tidak ingin diaktifkan.

	Fungsi builtin terkait csrf ini ada di file system/libraries/csrf.php
*/
$csrf_token = [ 
	
	'enable' => false,
	
	/*
		Jika auto_check bernilai true, sistem akan melakukan pengecekan csrf secara otomatis ketika form disubmit. Proteksi csrf juga dapat dilakukan secara manual dengan menggunakan builtin fungsi yang sudah disertakan pada aplikasi ini. Contoh penerapannya adalah pada module login, silakan bukan app/modules/login.php
	*/
	'auto_check' => false,
	
	'auto_validation' => false,
	
	// Nama field, misal: <input type="hidden" name="csrf_app_token" value="..."/>
	'name' => 'csrf_app_token',
	
	// Cookie lifetime in seconds
	'expire' => 7200,
	
	/* exit program ketika terjadi error pada validasi token (token tidak ditemukan atau token tidak sesuai 
		jika bernilai true maka program akan otomatis keluar jika token tidak ditemukan
		jika bernilai false, maka perlu dilakukan pengecekan manual yan=itu dengan menguji nilai $csrf_token['status'], misal:
		
		$validation = csrf_validation();
		
		if ($validation['status'] == 'error') {
			echo $validation['message'];
		}
		
		atau 
		
		if ($validation['status'] == 'error') {
			if ($validation['error_type'] == 'token_notfound') {
				echo 'Token tidak ditemukan, silakan isi ulang form dan tekan tombol submit';
			} elseif ($validation['error_type'] == 'token_missmatch') {
				echo 'Token expiren, silakan refresh halaman ini';
			}
		}
	*/
	'exit_error' => false 
];

/* RBAC */
/* 	Jika enable bernilai true, maka ketika halaman dibuka, sistem akan otomatis mengecek apakah role tersebut boleh melakukan aksi add, edit, atau delete. 
	Berikut metode pengecekan nya:
		-	Add dan Edit. System akan mengecek variabel $_GET['action'], jika variabel tersebut bernilai add atau edit, maka akan dilakukan pengecekan apakah role boleh melakukan aksi add dan edit;
			Sehingga penting untuk mendefinisikan url untuk aksi edit menggunakan pola http://localhost/nama_module/add atau http://localhost/nama_module/edit
		-	Delete. System akan mengecek variabel $_POST['delete'], jika variabel tersebut didefinisikan maka akan dilakukan pengecekan apakah role diperbolehkan melakukan aksi delete.
			Sehingga penting untuk menambahkan <input type="hidden" name="delete"> pada form delete
	Akses yang dicek hanya jika role tersebut diberi hak akses No, jika nilai hak akses Own, system tidak melakukan pengecekan 
*/
$check_role_action = ['enable_global' => true, 'field' =>'id_user_input'];