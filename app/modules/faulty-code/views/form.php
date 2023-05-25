
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
					<label class="col-sm-3 col-md-2 col-lg-3 col-xl-2 col-form-label">Brand</label>
					<div class="col-sm-4">
						<?php 
						$options_kategori['0'] = "[Pilih Brand]";
						foreach ($kategori as $key => $val) {
							$options_kategori[$val['id_kategori']] = $val['nama_kategori'];
						}
						echo options(['name' => 'id_kategori', 'id' => 'id_kategori', 'onchange'=>"selectProduk(this.options[this.selectedIndex].value)"], $options_kategori, set_value('id_kategori', @$id_kategori))?>
					</div>
				</div>
				<div class="form-group row">
					<label class="col-sm-3 col-md-2 col-lg-3 col-xl-2 col-form-label">Produk</label>
					<div class="col-sm-4">
						<?php 
						$options_subkategori['0'] = "[Pilih Produk]";
						if(@$id_kategori>0){
							foreach ($subkategori as $key => $val) {
								$options_subkategori[$val['id_subkategori']] = $val['nama_subkategori'];
							}
						}
						echo options(['name' => 'id_subkategori', 'id' => 'id_subkategori'], $options_subkategori, set_value('id_subkategori', @$id_subkategori))?>
					</div>
				</div>
				<div class="form-group row">
					<label class="col-sm-3 col-md-2 col-lg-3 col-xl-2 col-form-label">Faulty code</label>
					<div class="col-sm-5">
						<input class="form-control" type="text" name="faulty_code" value="<?=set_value('faulty_code', @$faulty_code)?>" placeholder="Faulty Code" required="required"/>
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
<script type="text/javascript"> 
<?php
	if(isset($_POST['submit'])){
		$id_kategori=$_POST['id_kategori'];
		$id_subkategori=$_POST['id_subkategori'];
	}
?>
$(function() {
    <?php if(@$id_kategori>0){?>
	loadData('<?php echo @$id_kategori?>');
	$("#id_subkategori").val('<?php echo @$id_subkategori?>');
	<?php
	}
	?>
});

function selectProduk(id_kategori){	
	if(id_kategori!="name"){
		loadData(id_kategori); 
	}else{
		$("#id_subkategori").html("<option value='0'>[Pilih Produk]</option>");  
	}
}
function loadData(loadId){
	var dataString = 'loadId='+ loadId;
	$.ajax({
		type: "POST",
		url: "<?php echo module_url();?>?action=loadData",
		data: dataString,
		cache: false,
		success: function(data){
			$("#id_subkategori").html("<option value='0'>[Pilih Produk]</option>"); 
			$("#id_subkategori").append(data);  
			<?php if(@$id_subkategori>0){?>
			$("#id_subkategori").val('<?php echo @$id_subkategori?>');
			<?php }?>
		}
	});
}
</script> 