
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
		if (@$result==0) {
			show_message('Data tidak ditemukan', 'Not Found', false);
		} else {
			if (!empty($msg)) {
				show_message($msg);
			}
		
			$disabled="disabled";
			
		?>
		<form method="post" action="" class="form-horizontal" enctype="multipart/form-data">
			<div class="tab-content">
				<div class="col-6">			
					<div class="form-group row">
						<label class="col-sm-3 col-form-label">SKU No</label>
						<div class="col-sm-8 form-inline">
							<?php
								$options_sku[0] = "[Pilih SKU No.]";
								if(!empty(@$data_sku)){
									foreach ($data_sku as $key => $val) {
										$options_sku[$val['sku']] = $val['sku']." - ".$val['nama_produk'];
									}		
								}
								if($disabled=="disabled"){
									echo options(['name' => 'sku', 'disabled'=>'disabled'], $options_sku, set_value('sku', @$sku));
								}else{
									echo options(['name' => 'sku'], $options_sku, set_value('sku', @$sku));
								}
							?>							
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-3 col-form-label">Product Decription</label>
						<div class="col-sm-8">
							<input class="form-control" type="text" name="nama_produk" readonly  value="<?=set_value('nama_produk', @$nama_produk)?>" placeholder="Product" required="required"/>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-3 col-form-label">S/N</label>
						<div class="col-sm-8">
							<input type="hidden" name="serial_number_old" value="<?=set_value('serial_number_old', @$serial_number)?>"/>
							<input class="form-control" type="text" name="serial_number" <?=$disabled?> value="<?=set_value('serial_number', @$serial_number)?>" placeholder="Serial Number" required="required"/>
						</div>
					</div>		
					<div class="form-group row">
						<label class="col-sm-3 col-form-label">Store</label>
						<div class="col-sm-8 form-inline">
							<?php
								foreach ($store as $key => $val) {
									$options_store[$val['id_store']] = $val['nama_store'];
								}	
								if($disabled=="disabled"){
									echo options(['name' => 'id_store', 'disabled'=>'disabled'], $options_store, set_value('id_store', @$id_store));
								}else{
									echo options(['name' => 'id_store'], $options_store, set_value('id_store', @$id_store));
								}
								
							?>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-3 col-form-label">WTY Period</label>
						<div class="col-sm-2">
							<input class="form-control" type="text" name="wty_period" readonly value="<?=set_value('wty_period', @$wty_period)?>" placeholder="WTY Period" required="required"/> 
						</div> <small id="wtyinline" class="text-muted">Months</small>
					</div>
					<div class="form-group row">
						<label class="col-sm-3 col-form-label">WTY Start</label>
						<div class="col-sm-5">
							<input class="form-control" type="date" name="wty_start" <?=$disabled?> value="<?=set_value('wty_start', @$wty_start)?>" placeholder="WTY Start" required="required"/>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-3 col-form-label">WTY End</label>
						<div class="col-sm-5">
							<input class="form-control" type="date" name="wty_end" disabled value="<?=set_value('wty_end', @$wty_end)?>" placeholder="WTY End"/>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-3 col-form-label">WTY Status</label>
						<div class="col-sm-3">
							<?php
								foreach ($wty_status as $key => $val) {
									$options_wty_status[$val['id_wty_status']] = $val['wty_status'];
								}			
								if($disabled=="disabled"){
									echo options(['name' => 'id_wty_status', 'disabled'=>'disabled'], $options_wty_status, set_value('id_wty_status', @$id_wty_status));
								}else{
									echo options(['name' => 'id_wty_status'], $options_wty_status, set_value('id_wty_status', @$id_wty_status));
								}								
							?>
						</div>
					</div>	
					<div class="form-group row">
						<label class="col-sm-3 col-form-label">Unit Status</label>
						<div class="col-sm-3">
							<?php
								foreach ($unit_status as $key => $val) {
									$options_unit_status[$val['id_unit_status']] = $val['unit_status'];
								}		
								if($disabled=="disabled"){
									echo options(['name' => 'id_unit_status', 'disabled'=>'disabled'], $options_unit_status, set_value('id_unit_status', @$id_unit_status));
								}else{
									echo options(['name' => 'id_unit_status'], $options_unit_status, set_value('id_unit_status', @$id_unit_status));
								}
							?>
						</div>
					</div>
					<div class="form-group row mb-0">
						<div class="col-sm-8">
							<input type="hidden" name="id" value="<?=@$_GET['id']?>"/>
						</div>
					</div>
				</div>
				<div class="col-12">
					<br><br><h5>History Warranty</h5><br>
					<div class="table-responsive">
						<table class="table table-striped table-bordered table-hover data-tables">
							<thead>
							<tr>
								<th>SKU</th>
								<th>Serial Number</th>
								<th>Status</th>
								<th>Updated Date</th>
								<th>Updated By</th>
							</tr>
							</thead>							
							<tbody>
								<?php
														
									helper ('html');
									$no = 1;
									global $list_action;
									if ($history) {
										foreach ($history as $key => $val) {								
											echo '<tr>
													<td>' . $val['sku'] . '</td>
													<td>' . $val['serial_number'].'</td>
													<td>' . $val['unit_status'].'</td>
													<td>' . format_tanggal_indo($val['updated_date']) . '</td>
													<td>' . $val['updated_by'] . '</td>
												</tr>';
											$no++;
										}
									}
									$settings['order'] = [2,'asc'];
									$settings['columnDefs'][] = ['targets' => 1, 'orderable' => false];
									
								?>
							</tbody>
						</table>
						<span id="dataTables-setting" style="display:none"><?=json_encode($settings)?></span>
					</div>
				</div>
			</div>			
		</form>			
		<?php }?>
	</div>
</div>