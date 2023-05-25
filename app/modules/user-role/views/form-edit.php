
<div class="card">
	<div class="card-header">
		<h5 class="card-title"><?=$title?></h5>
	</div>
	
	<div class="card-body">
		<?php 
			helper ('html');
			
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
						<div class="upload-img-thumb mb-2"><div class="img-prop mt-2"></div></div>
					</div>
				</div>
				<div class="form-group row">
					<label class="col-sm-3 col-md-2 col-lg-3 col-xl-2 col-form-label">Username</label>
					<div class="col-sm-8 form-inline">
						<input class="form-control" type="text" name="username" disabled="disabled" value="<?=set_value('username', @$username)?>" placeholder="Username"/>
					</div>
				</div>
				<div class="form-group row">
					<label class="col-sm-3 col-md-2 col-lg-3 col-xl-2 col-form-label">Nama</label>
					<div class="col-sm-8 form-inline">
						<input class="form-control" type="text" name="nama" disabled value="<?=set_value('nama', @$nama)?>" placeholder="Nama"/>
					</div>
				</div>
				<div class="form-group row">
					<label class="col-sm-3 col-md-2 col-lg-3 col-xl-2 col-form-label">Phone</label>
					<div class="col-sm-8 form-inline">
						<input class="form-control" type="text" name="phone" disabled value="<?=set_value('phone', @$phone)?>" placeholder="Phone"/>
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
						<input class="form-control" type="text" disabled name="email" value="<?=set_value('email', @$email)?>" placeholder="Email"/>
					</div>
				</div>
				<?php 
				global $list_action;
				if ($list_action['update_data'] == 'all') {?>
					<div class="form-group row">
						<label class="col-sm-3 col-md-2 col-lg-3 col-xl-2 col-form-label">Role</label>
						<div class="col-sm-8 form-inline">
							<?php
							foreach ($role as $key => $val) {
								$options_role[$val['id_role']] = $val['judul_role'];
							}
							echo options(['name' => 'id_role'], $options_role, set_value('id_role', @$id_role));
							?>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-3 col-md-2 col-lg-3 col-xl-2 col-form-label">Store</label>
						<div class="col-sm-8 form-inline">
						<?php
						$options_store['0'] = "[Pilih Store]";
						foreach ($store as $key => $val) {
							$options_store[$val['id_store']] = $val['nama_store'];
						}
						echo options(['name' => 'id_store','disabled'=>'disabled'], $options_store, set_value('id_store', @$id_store));
						?>
					</div>
					</div>
				<?php }?>
				<div class="form-group row">
					<label class="col-sm-3 col-md-2 col-lg-3 col-xl-2 col-form-label">Verified</label>
					<div class="col-sm-8 form-inline">
						<?php 
						foreach ($status as $key => $val) {
							$options_status[$val['id_status']] = $val['nama_status'];
						}
						echo options(['name' => 'verified','disabled'=>'disabled'], $options_status, set_value('verified', @$verified));?>
					</div>
				</div>
				<div class="form-group row mb-0">
					<div class="col-sm-8">
						<button type="submit" name="submit" value="submit" class="btn btn-primary">Submit</button>
						<input type="hidden" name="id" value="<?=@$_GET['id']?>"/>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>