
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
						$avatar = @$_FILES['file']['name'] ?: @$image;
						if (!empty($avatar) ) {
							echo '<div class="img-choose" style="margin:inherit;margin-bottom:10px">
									<div class="img-choose-container">
										<img src="'.BASE_URL. $config['produk_images_path'] . $avatar . '?r=' . time() . '"/>
										<a href="javascript:void(0)" class="remove-img"><i class="fas fa-times"></i></a>
									</div>
								</div>
								';
						}
						?>
						<input type="hidden" class="avatar-delete-img" name="avatar_delete_img" value="0">
						<input type="file" class="file" name="avatar">
						<input type="hidden" class="avatar-max-size" name="avatar_max_size" value="3072000"/>
							<?php if (!empty($form_errors['avatar'])) echo '<small class="alert alert-danger">' . $form_errors['avatar'] . '</small>'?>
						<small class="small" style="display:block">Maksimal 3Mb, Minimal 100px x 100px, Tipe file: .JPG, .JPEG</small>
						<div class="upload-img-thumb mb-2"><div class="img-prop mt-2"></div></div>
					</div>
				</div>
				<div class="form-group row">
					<label class="col-sm-3 col-md-2 col-lg-3 col-xl-2 col-form-label">Nama Produk</label>
					<div class="col-sm-5">
						<input class="form-control" type="text" name="name" value="<?=set_value('name', @$name)?>" placeholder="Nama Produk" required="required"/>
					</div>
				</div>
				<div class="form-group row">
					<label class="col-sm-3 col-md-2 col-lg-3 col-xl-2 col-form-label">Deskripsi</label>
					<div class="col-sm-8">
						<textarea class="form-control" name="description" placeholder="Deskripsi" required="required"><?=set_value('description', @$description)?></textarea>
					</div>
				</div>
				<div class="form-group row">
					<label class="col-sm-3 col-md-2 col-lg-3 col-xl-2 col-form-label">Tampil Harga</label>
					<div class="col-sm-8 form-inline">
						<?php 
						//foreach ($status as $item) {
							$isShowPrice['Y'] = "Yes";
							$isShowPrice['N'] = "No";
						//}
						echo options(['name' => 'show_price','id'=> 'show_price'], $isShowPrice, set_value('show_price', @$show_price));?>
					</div>
				</div>
				<div class="form-group row" id="div_price">
					<label class="col-sm-3 col-md-2 col-lg-3 col-xl-2 col-form-label">Harga Asli</label>
					<div class="col-sm-5">
						<input class="form-control" type="number" id="price" name="price" value="<?=set_value('price', @$price)?>" placeholder="Harga Asli" required="required"/>
					</div>
				</div>
				<div class="form-group row" id="div_promo">
					<label class="col-sm-3 col-md-2 col-lg-3 col-xl-2 col-form-label">Promo</label>
					<div class="col-sm-8 form-inline">
						<?php 
						//foreach ($status as $item) {
							$isPromo['Y'] = "Yes";
							$isPromo['N'] = "No";
						//}
						echo options(['name' => 'promo','id'=> 'promo'], $isPromo, set_value('promo', @$promo));?>
					</div>
				</div>
				<div class="form-group row" id="div_price_promo">
					<label class="col-sm-3 col-md-2 col-lg-3 col-xl-2 col-form-label">Harga Promo</label>
					<div class="col-sm-5">
						<input class="form-control" id="price_promo" type="number" name="price_promo" value="<?=set_value('price_promo', @$price_promo)?>" placeholder="Harga Promo" required="required"/>
					</div>
				</div>
				<div class="form-group row">
					<label class="col-sm-3 col-md-2 col-lg-3 col-xl-2 col-form-label">Enabled</label>
					<div class="col-sm-8 form-inline">
						<?php 
						foreach ($status as $item) {
							$options[$item['id_status']] = $item['nama_status'];
						}
						echo options(['name' => 'enabled'], $options, set_value('enabled', @$enabled));?>
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
<script>
$(document).ready(function(){
	<?php if(@$show_price=="Y"){?>
	$('#div_price').show();
	$('#div_promo').show();
	<?php }else{?>
	$('#div_price').hide();
	$('#div_promo').hide();
	<?php }?>
	
	<?php if(@$promo=="Y"){?>
	$('#div_price_promo').show();
	<?php }else{?>
	$('#div_price_promo').hide();
	<?php }?>
	
	$('#promo').val("<?php if(@$promo==""){echo "N";}else{$promo=$promo;}?>");
	$('#show_price').val("<?php echo @$show_price?>");
	
	$('#promo').on('change', function() {
		if($('#promo').val()=='Y'){
			$('#div_price_promo').show();
			$('#price_promo').val(<?php echo @$price_promo?>);
		}else{
			$('#div_price_promo').hide();
			$('#price_promo').val("0");
		}
	});
	
	$('#show_price').on('change', function() {
		if($('#show_price').val()=='Y'){
			$('#div_price').show();
			$('#price').val(<?php echo @$price_promo?>);			
			$('#div_promo').show();	
		}else{
			$('#div_price').hide();
			$('#price').val(0);
			
			$('#div_promo').hide();	
		}
		
		if($('#promo').val()=="Y"){
			$('#div_price_promo').show();
			$('#price_promo').val(<?php echo @$price_promo?>);
		}else{
			$('#div_price_promo').hide();
			$('#price_promo').val("0");
		}
	});
});
</script>