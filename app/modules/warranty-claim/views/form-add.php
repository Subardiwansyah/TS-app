
<div class="card">
	<div class="card-header">
		<h5 class="card-title"><?=$title?></h5>
	</div>
	
	<div class="card-body">
		<?php 
			helper ('html');
			/*
			echo btn_label(['class' => 'btn btn-success btn-xs',
				'url' => module_url() . '?action=add',
				'icon' => 'fa fa-plus',
				'label' => 'Tambah ' . $current_module['judul_module']
			]);
			*/
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
				<div class="col-6">
					<div class="form-group row">
						<label class="col-sm-3 col-form-label">SKU No</label>
						<div class="col-sm-5">
							<input class="form-control" type="text" name="sku" value="<?=set_value('sku', @$sku)?>" placeholder="SKU" required="required"/>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-3 col-form-label">S/N</label>
						<div class="col-sm-8">
							<input class="form-control" type="text" name="serial_number" value="<?=set_value('serial_number', @$serial_number)?>" placeholder="Serial Number" required="required"/>
						</div>
					</div>
					<div class="form-group row mb-0">
						<div class="col-sm-8">
							<button type="submit" name="submit" value="find" class="btn btn-primary">Find</button>
						</div>
					</div>					
				</div>
				<?php if ($found==1) {?>
				<div class="col-6">
					<div class="form-group row">
						<label class="col-sm-3 col-form-label">SKU No</label>
						<div class="col-sm-5">
							<input class="form-control" type="text" name="sku" disabled value="<?=set_value('sku', @$sku)?>" placeholder="SKU" required="required"/>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-3 col-form-label">S/N</label>
						<div class="col-sm-8">
							<input class="form-control" type="text" name="serial_number" disabled value="<?=set_value('serial_number', @$serial_number)?>" placeholder="Serial Number" required="required"/>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-3 col-form-label">Product</label>
						<div class="col-sm-8">
							<input class="form-control" type="text" name="nama_produk" disabled value="<?=set_value('nama_produk', @$nama_produk)?>" placeholder="Product" required="required"/>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-3 col-form-label">WTY End</label>
						<div class="col-sm-5">
							<input class="form-control" type="text" name="wty_end" readonly value="<?=set_value('wty_end', @$wty_end)?>" placeholder="WTY End" required="required"/>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-3 col-form-label">WTY Status</label>
						<div class="col-sm-3">
							<input class="form-control" type="text" name="wty_status" readonly value="<?=set_value('wty_status', @$wty_status)?>" placeholder="WTY Status" required="required"/> 
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-3 col-form-label">Bought Date</label>
						<div class="col-sm-8">
							<input class="form-control" type="date" name="bought_date" placeholder="Bought Date" value="<?=set_value('bought_date', @$wty_status)?>"/>
						</div>
					</div>
					<div class="form-group row mb-0">
						<div class="col-sm-8">
							<input type="hidden" name="sku2" value="<?=@$sku?>"/>
							<input type="hidden" name="serial_number2" value="<?=@$serial_number?>"/>
							<button type="submit" name="submit" value="register" class="btn btn-primary">Register</button>
						</div>
					</div>					
				</div>
				<?php }?>
			</div>			
		</form>			
	</div>
</div>