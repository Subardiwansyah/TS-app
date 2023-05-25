<style>
fieldset.scheduler-border {
    border: 1px groove #ddd !important;
    padding: 0 1.4em 1.4em 1.4em !important;
    margin: 0 0 1.5em 0 !important;
    -webkit-box-shadow:  0px 0px 0px 0px #000;
            box-shadow:  0px 0px 0px 0px #000;
}

legend.scheduler-border {
	font-size: 1em !important;
	font-weight: bold !important;
	text-align: left !important;
	width:auto;
	padding:0 10px;
	border-bottom:none;
}
</style>
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
			
			$user = $_SESSION['user'];
			
			$disabled="";
			$display="";
			if((@$case_no>0 || @$case_no!='') || @$sku=="" || ($customer_no!=$user['no_user'])){
				$disabled="disabled";
				$display="style='display:none;'";
			}			
			
		?>
		<form method="post" action="" class="form-horizontal" enctype="multipart/form-data">
			<div class="tab-content">
				<div class="col-6">
					<div class="form-group row">
						<label class="col-sm-3 col-form-label">Date</label>
						<div class="col-sm-5">
							<input class="form-control" type="text" name="created_date" readonly value="<?=set_value('created_date', @$created_date)?>" placeholder="Created Date" required="required"/>
						</div>
					</div>					
					<div class="form-group row">
						<label class="col-sm-3 col-form-label">SKU No</label>
						<div class="col-sm-8">
							<input class="form-control" type="text" name="sku" readonly value="<?=set_value('sku', @$sku)?>" placeholder="SKU" required="required"/>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-3 col-form-label">Product</label>
						<div class="col-sm-8">
							<input class="form-control" type="text" name="nama_produk" readonly value="<?=set_value('nama_produk', @$nama_produk)?>" placeholder="Product" required="required"/>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-3 col-form-label">S/N</label>
						<div class="col-sm-8">
							<input class="form-control" type="text" name="serial_number" readonly value="<?=set_value('serial_number', @$serial_number)?>" placeholder="Serial Number" required="required"/>
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
				</div>
				<div class="col-6">
					<div class="form-group row">
						<label class="col-sm-3 col-form-label">Case No</label>
						<div class="col-sm-3">
							<input class="form-control" type="text" name="case_no" readonly value="<?=set_value('case_no', @$case_no)?>" placeholder="Case No" required="required"/> 
						</div>
					</div>					
					<div class="form-group row">
						<label class="col-sm-3 col-form-label">Customer No</label>
						<div class="col-sm-8">
							<input class="form-control" type="text" name="customer_no" readonly value="<?=set_value('customer_no', @$customer_no)?>" placeholder="Customer No"/>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-3 col-form-label">Faulty Code</label>
						<div class="col-sm-8 form-inline">
							<?php
							$options['0'] = "[Select Faulty Code]";
							if(!empty($faulty)){
								foreach ($faulty as $key => $val) {
									$options[$val['faulty_code']] = $val['faulty_code'];
								}
							}
							if($disabled){
								echo options(['name' => 'faulty_name', 'disabled' => $disabled], $options, set_value('faulty_name', @$faulty_name));
							}else{
								echo options(['name' => 'faulty_name'], $options, set_value('faulty_name', @$faulty_name));
							}
							?>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-3 col-form-label">Faulty Remark</label>
						<div class="col-sm-8">
							<input class="form-control" type="text" name="faulty_remark" <?=$disabled?> value="<?=set_value('faulty_remark', @$faulty_remark)?>" placeholder="Faulty Remark" required="required"/> 
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-3 col-form-label">Photo Product</label>
						<div class="col-sm-5">
							<?php 
							if (!empty($image1) ) {
								echo '<div class="img-choose" style="margin:inherit;margin-bottom:10px">
										<div class="img-choose-container">
											<img src="'.BASE_URL. $config['foto_path'] . $image1 . '?r=' . time() . '"/>
										</div>
									</div>
									';
							}else{
							?>
							<input type="hidden" class="foto-delete-img" name="foto_delete_img1" value="0">
							<input type="file" class="file" name="image1" <?php echo $disabled?> accept="image/jpeg">
							<input type="hidden" class="foto-max-size" name="foto_max_size1" value="300000"/>
							<?php if (!empty($form_errors['image1'])) echo '<small class="alert alert-danger">' . $form_errors['image1'] . '</small>'?>
							<small class="small" style="display:block">Maksimal 300Kb, Minimal 100px x 100px, Tipe file: .JPG, .JPEG</small>
							<div class="upload-img-thumb mb-2"><div class="img-prop mt-2"></div></div>
							<?php }?>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-3 col-form-label">Receipt</label>
						<div class="col-sm-5">
							<?php 
							if (!empty($image1) ) {
								echo '<div class="img-choose" style="margin:inherit;margin-bottom:10px">
										<div class="img-choose-container">
											<img src="'.BASE_URL. $config['foto_path'] . $image2 . '?r=' . time() . '"/>
										</div>
									</div>
									';
							}else{
							?>
							<input type="hidden" class="foto-delete-img" name="foto_delete_img2" value="0">
							<input type="file" class="file" name="image2" <?php echo $disabled?> accept="image/jpeg">
							<input type="hidden" class="foto-max-size" name="foto_max_size2" value="300000"/>
							<?php if (!empty($form_errors['image2'])) echo '<small class="alert alert-danger">' . $form_errors['image2'] . '</small>'?>
							<small class="small" style="display:block">Maksimal 300Kb, Minimal 100px x 100px, Tipe file: .JPG, .JPEG</small>
							<div class="upload-img-thumb mb-2"><div class="img-prop mt-2"></div></div>
							<?php }?>
						</div>
					</div>					
					<fieldset class="scheduler-border">
					<legend class="scheduler-border">Case Result</legend>
					<div class="form-group row">
						<label class="col-sm-3 col-form-label">Faulty Code</label>
						<div class="col-sm-8">
							<input class="form-control" type="text" name="faulty_name_check" disabled value="<?=set_value('faulty_name_check', @$faulty_name_check)?>" placeholder="Faulty Code"/> 
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-3 col-form-label">Faulty Remark</label>
						<div class="col-sm-8">
							<input class="form-control" type="text" name="faulty_remark_check" disabled value="<?=set_value('faulty_remark_check', @$faulty_remark_check)?>" placeholder="Faulty Remark"/> 
						</div>
					</div>
					</fieldset>
					<div class="form-group row">
						<label class="col-sm-3 col-form-label">Product Return Status</label>
						<div class="col-sm-6">
							<?php 
							$options_product_return['0'] = "[Pilih Product Return]";
							foreach ($product_return as $item) {
								$options_product_return[$item['id_product_return']] = $item['product_return'];
							}
							echo options(['name' => 'id_product_return', 'disabled'=>'disabled'], $options_product_return, set_value('id_product_return', @$id_product_return));
							?>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-3 col-form-label">Case Status</label>
						<div class="col-sm-6">
							<?php 
							foreach ($case_status as $item) {
								$options_case[$item['id_case_status']] = "$item[case_status]";
							}
							echo options(['name' => 'id_case_status','id'=> 'id_case_status', 'disabled'=>'disabled'], $options_case, set_value('id_case_status', @$id_case_status));							
							?>
						</div>
					</div>
					<div class="form-group row" id="reject_reason">
						<label class="col-sm-3 col-form-label">Reject Reason</label>
						<div class="col-sm-6">
							<?php 
							foreach ($reject_reason as $item) {
								$options_reject_reason[$item['id_reject_reason']] = $item['reject_reason'];
							}
							echo options(['name' => 'id_reject_reason','id'=> 'id_reject_reason', 'disabled'=>'disabled'], $options_reject_reason, set_value('id_reject_reason', @$id_reject_reason));
							
							?>
						</div>
					</div>
					<div class="form-group row" id="new_sku">
						<label class="col-sm-3 col-form-label">SKU New</label>
						<div class="col-sm-8">
							<input class="form-control" type="text" name="sku_new" <?php echo $disabled?> value="<?=set_value('sku_new', @$sku_new)?>" placeholder="SKU New"/>
						</div>
					</div>
					<div class="form-group row" id="new_sn">
						<label class="col-sm-3 col-form-label">S/N New</label>
						<div class="col-sm-8">
							<input class="form-control" type="text" name="serial_number_new" <?php echo $disabled?> value="<?=set_value('serial_number_new', @$serial_number_new)?>" placeholder="Serial Number New"/>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-3 col-form-label">Case Closed</label>
						<div class="col-sm-6">
							<?php 
							foreach ($data_status as $item) {
								$options_status[$item['id_status']] = "$item[nama_status]";
							}
							echo options(['name' => 'closed', 'disabled'=>'disabled'], $options_status, set_value('closed', @$closed));
							
							?>
						</div>
					</div>
					<div class="form-group row mb-0">
						<div class="col-sm-8">
							<button type="submit" name="submit" value="submit" <?=$display?> class="btn btn-primary">Claim</button>
							<input type="hidden" name="id" value="<?=@$_GET['id']?>"/>
							<input type="hidden" name="customer_no" value="<?=@$customer_no?>"/>
							<input type="hidden" name="full_name" value="<?=@$full_name?>"/>
							<input type="hidden" name="phone" value="<?=@$phone?>"/>
							<input type="hidden" name="dealer_no" value="<?=@$dealer_no?>"/>
							<input type="hidden" name="dealer_name" value="<?=@$dealer_name?>"/>
							<input type="hidden" name="dealer_no" value="<?=@$dealer_no?>"/>
						</div>
					</div>
				</div>
			</div>			
		</form>	
		<?php }?>
	</div>
</div>
<script>
$(document).ready(function(){
	<?php if(@$id_case_status==8){?>
	$('#reject_reason').show();
	<?php }else{?>
	$('#reject_reason').hide();
	<?php }?>
	
	<?php if(@$sku_new!="" || @$serial_number_new!=""){?>
	$('#new_sn').show();
	<?php }else{?>
	$('#new_sn').hide();
	<?php }?>
	
	<?php if(@$sku_new!=""){?>
	$('#new_sku').show();
	<?php }else{?>
	$('#new_sku').hide();
	<?php }?>
});
</script>