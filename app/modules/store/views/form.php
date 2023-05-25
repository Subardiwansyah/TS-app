<div class="card">
	<div class="card-header">
		<h5 class="card-title"><?=$title?></h5>
	</div>
	
	<div class="card-body">
		<?php 
		helper ('html');
		if (!empty($msg)) {
			// echo '<pre>'; print_r($msg); die;
			show_alert($msg);
		}
		
		echo btn_label(['class' => 'btn btn-success btn-xs',
			'url' => module_url() . '?action=add',
			'icon' => 'fa fa-plus',
			'label' => 'Tambah Data'
		]);
		
		echo btn_label(['class' => 'btn btn-light btn-xs',
			'url' => module_url(),
			'icon' => 'fa fa-arrow-circle-left',
			'label' => $current_module['judul_module']
		]);
		?>
		<hr/>
		<form method="post" action="" id="form-container" enctype="multipart/form-data">
			<div class="tab-content" id="myTabContent">
				<div class="form-group row">
					<label class="col-sm-3 col-md-2 col-lg-3 col-xl-2 col-form-label">Nama Store</label>
					<div class="col-sm-8">
						<input class="form-control" type="text" name="nama_store" value="<?=set_value('nama_store', @$nama_store)?>" placeholder="Nama Store" required="required"/>
						<input type="hidden" name="nama_store_old" value="<?=set_value('nama_store', @$nama_store)?>">
					</div>
				</div>
				<div class="form-group row">
					<label class="col-sm-3 col-md-2 col-lg-3 col-xl-2 col-form-label">Alamat</label>
					<div class="col-sm-8">
						<textarea class="form-control" type="text" name="alamat" placeholder="Alamat" required="required"><?=set_value('alamat', @$alamat)?></textarea>
					</div>
				</div>
				<div class="form-group row">
					<label class="col-sm-3 col-md-2 col-lg-3 col-xl-2 col-form-label">Aktif</label>
					<div class="col-sm-5 form-inline">
						<?php 
						foreach ($status as $item) {
							$options[$item['id_status']] = $item['nama_status'];
						}
						echo options(['name' => 'aktif'], $options, set_value('aktif', @$aktif));?>
					</div>
				</div>
				<?php 
				$id = '';
				if (!empty($_GET['id'])) {
					$id = $_GET['id'];
				} elseif (!empty($msg['id_store'])) { // ADD Auto Increment
					$id = $msg['id_store'];
				} ?>
				<input type="hidden" name="id" value="<?=$id?>"/>
				<button type="submit" name="submit" value="submit" class="btn btn-primary mt-2">Save</button>
			</div>
		</form>
	</div>
</div>