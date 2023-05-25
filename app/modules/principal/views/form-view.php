
<div class="card">
	<div class="card-header">
		<h5 class="card-title"><?=$title?></h5>
	</div>
	
	<div class="card-body">
		<?php 
			helper ('html');
			echo btn_label(['class' => 'btn btn-success btn-xs',
				'url' => module_url() . '?action=add',
				'icon' => 'fa fa-plus',
				'label' => 'Tambah ' . $current_module['judul_module']
			]);
			
			echo btn_label(['class' => 'btn btn-light btn-xs',
				'url' => module_url(),
				'icon' => 'fa fa-arrow-circle-left',
				'label' => 'Daftar ' . $current_module['judul_module']
			]);
		?>
		<hr/>
		<?php
		if (!empty($msg)) {
			show_message($msg);
		}
		?>
		<form method="post" action="" class="form-horizontal" enctype="multipart/form-data">
			<div class="tab-content">
				<div class="form-group row">
					<label class="col-sm-3 col-md-2 col-lg-3 col-xl-2 col-form-label">Foto</label>
					<div class="col-sm-5">
						<?php 
						$avatar = @$_FILES['file']['name'] ?: @$avatar;
						if (!empty($avatar) ) {
							echo '<div class="img-choose" style="margin:inherit;margin-bottom:10px">
									<div class="img-choose-container">
										<img src="'.BASE_URL. $config['user_images_path'] . $avatar . '?r=' . time() . '"/>
									</div>
								</div>
								';
						}
						?>
						<input type="hidden" class="avatar-delete-img" name="avatar_delete_img" value="0">						
						<div class="upload-img-thumb mb-2"><div class="img-prop mt-2"></div></div>
					</div>
				</div>
				<div class="form-group row">
					<label class="col-sm-3 col-md-2 col-lg-3 col-xl-2 col-form-label">Username</label>
					<div class="col-sm-8 form-inline">
						<input class="form-control" type="text" name="username" disabled="disabled" value="<?=set_value('username', @$username)?>" placeholder="Username" disabled required="required"/>
					</div>
				</div>
				<div class="form-group row">
					<label class="col-sm-3 col-md-2 col-lg-3 col-xl-2 col-form-label">Nama</label>
					<div class="col-sm-8 form-inline">
						<input class="form-control" type="text" name="nama" disabled value="<?=set_value('nama', @$nama)?>" placeholder="Nama" required="required"/>
					</div>
				</div>
				<div class="form-group row">
					<label class="col-sm-3 col-md-2 col-lg-3 col-xl-2 col-form-label">Phone</label>
					<div class="col-sm-8 form-inline">
						<input class="form-control" type="text" name="phone" disabled value="<?=set_value('phone', @$phone)?>" placeholder="Phone" required="required"/>
					</div>
				</div>
				<div class="form-group row">
					<label class="col-sm-3 col-md-2 col-lg-3 col-xl-2 col-form-label">Address</label>
					<div class="col-sm-5">
						<textarea class="form-control" name="address" disabled><?=set_value('address', @$address)?></textarea>
					</div>
				</div>
				<div class="form-group row">
					<label class="col-sm-3 col-md-2 col-lg-3 col-xl-2 col-form-label">Email</label>
					<div class="col-sm-8 form-inline">
						<input class="form-control" type="text" name="email" disabled value="<?=set_value('email', @$email)?>" placeholder="Email" required="required"/>
					</div>
				</div>
				<div class="form-group row">
					<label class="col-sm-3 col-md-2 col-lg-3 col-xl-2 col-form-label">Verified</label>
					<div class="col-sm-8 form-inline">
						<?php 
						foreach ($status as $item) {
							$options[$item['id_status']] = $item['nama_status'];
						}
						echo options(['name' => 'verified', 'disabled' =>'disabled'], $options, set_value('verified', @$verified));?>
					</div>
				</div>
				<div class="form-group row mb-0">
					<div class="col-sm-8">
						<input type="hidden" name="id" value="<?=@$_GET['id']?>"/>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>