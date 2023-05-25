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

#overlay{position:fixed;top:20%;left:20%;width:70%;height:70%;background:rgba(0,0,0,.8) none 50%/contain no-repeat;cursor:pointer;transition:.3s;visibility:hidden;opacity:0}#overlay.open{visibility:visible;opacity:1}#overlay:after{content:"\2715";position:absolute;color:#fff;top:10px;right:20px;font-size:2em}
</style>
<div class="card">
	<div class="card-header">
		<h5 class="card-title"><?=$title?></h5>
	</div>
	
	<div class="card-body">
		<ul class="nav nav-pills mb-3" id="myTab" role="tablist">
			<li class="nav-item">
				<a class="nav-link active" id="detail-wty-tab" data-toggle="tab" href="#detail-wty" role="tab" aria-controls="detail-wty" aria-selected="true">WTY Claim</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" id="detail-history-tab" data-toggle="tab" href="#detail-history" role="tab" aria-controls="detail-history" aria-selected="false">History</a>
			</li>
		</ul>
		<hr/>
		<div class="tab-content produk-edit" id="pills-tabContent">
			<div class="tab-pane fade show active" id="detail-wty" role="tabpanel" aria-labelledby="wty-tab">
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
			if ($result==0) {
				show_message('Data tidak ditemukan', 'Not Found', false);
			} else {
				if (!empty($msg)) {
					show_message($msg);
				}
			
				$disabled="disabled";
				$disabled_sn="";
				$display="";
				
				
				
				if($serial_number_new!="" || @$closed==1){
					$disabled_sn="disabled";
				}
				
				if(@$locked==0 || ($user['nama']==$locked_by)){
					if(@$closed==1){
						$disabled="disabled";
						$display="style='display:none;'";
					}else{
						$disabled="";
					}				
				}else{
					if(@$closed==1){
						$disabled="disabled";
						$display="style='display:none;'";
					}
				}
				if(@$case_no==''){
					$disabled="disabled";
					$display="style='display:none;'";
				}
			?>
			<form method="post" action="" class="form-horizontal" enctype="multipart/form-data">
				<div class="tab-content">
					<div class="col-6">
						<div class="form-group row">
							<label class="col-sm-3 col-form-label">Case-ID</label>
							<div class="col-sm-5">
								<input class="form-control" type="text" name="case_no" disabled="disabled" value="<?=set_value('case_no', @$case_no)?>" placeholder="Case-ID" required="required"/>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-sm-3 col-form-label">Last Handler</label>
							<div class="col-sm-5">
								<input class="form-control" type="text" name="updated_by" disabled="disabled" value="<?=set_value('updated_by', @$updated_by)?>" placeholder="Updated By" required="required"/>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-sm-3 col-form-label">Created Date</label>
							<div class="col-sm-5">
								<input class="form-control" type="text" name="created_date" disabled="disabled" value="<?=set_value('created_date', format_tanggal_indo(@$created_date))?>" placeholder="Created Date" required="required"/>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-sm-3 col-form-label">Updated Date</label>
							<div class="col-sm-5">
								<input class="form-control" type="text" name="updated_date" disabled="disabled" value="<?=set_value('updated_date', format_tanggal_indo(@$updated_date))?>" placeholder="Updated Date" required="required"/>
							</div>
						</div>						
						<div class="form-group row">
							<label class="col-sm-3 col-form-label">Customer-ID</label>
							<div class="col-sm-6">
								<input class="form-control" type="text" name="customer_no" disabled value="<?=set_value('customer_no', @$customer_no)?>" placeholder="Customer-ID" required="required"/>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-sm-3 col-form-label">Full Name</label>
							<div class="col-sm-6">
								<input class="form-control" type="text" name="full_name" disabled value="<?=set_value('full_name', @$full_name)?>" placeholder="Full Name" required="required"/>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-sm-3 col-form-label">Phone No.</label>
							<div class="col-sm-6">
								<input class="form-control" type="text" name="phone" disabled value="<?=set_value('phone', @$phone)?>" placeholder="Phone" required="required"/>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-sm-3 col-form-label">Email Address</label>
							<div class="col-sm-8">
								<input class="form-control" type="text" name="email" disabled value="<?=set_value('email', @$email)?>" placeholder="Email Address" required="required"/>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-sm-3 col-form-label">Store</label>
							<div class="col-sm-8">
								<input class="form-control" type="text" name="nama_store" disabled value="<?=set_value('nama_store', @$nama_store)?>" placeholder="Store" required="required"/>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-sm-3 col-form-label">SKU No</label>
							<div class="col-sm-8">
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
							<label class="col-sm-3 col-form-label">WTY Period</label>
							<div class="col-sm-2">
								<input class="form-control" type="text" name="wty_period" disabled value="<?=set_value('wty_period', @$wty_period)?>" placeholder="WTY Period" required="required"/> 
							</div> <small id="wtyinline" class="text-muted">Months</small>
						</div>
						<div class="form-group row">
							<label class="col-sm-3 col-form-label">WTY End</label>
							<div class="col-sm-5">
								<input class="form-control" type="text" name="wty_end" disabled value="<?=set_value('wty_end', format_tanggal_indo(@$wty_end))?>" placeholder="WTY End" required="required"/>
							</div>
						</div>
					</div>
					<div class="col-6">
						<div class="form-group row">
							<label class="col-sm-3 col-form-label">Unit Status</label>
							<div class="col-sm-5">
								<input class="form-control" type="text" name="unit_status" disabled="disabled" value="<?=set_value('unit_status', @$unit_status)?>" placeholder="Unit Status" required="required"/>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-sm-3 col-form-label">Baught Date</label>
							<div class="col-sm-5">
								<input class="form-control" type="text" name="bought_date" disabled="disabled" value="<?=set_value('bought_date', format_tanggal_indo(@$bought_date))?>" placeholder="Bought Date" required="required"/>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-sm-3 col-form-label">Faulty Code</label>
							<div class="col-sm-6">
								<input class="form-control" type="text" name="faulty_name" disabled value="<?=set_value('faulty_name', @$faulty_name)?>" placeholder="Faulty Code" required="required"/>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-sm-3 col-form-label">Faulty Remark</label>
							<div class="col-sm-6">
								<input class="form-control" type="text" name="faulty_remark" disabled value="<?=set_value('faulty_remark', @$faulty_remark)?>" placeholder="Faulty Remark" required="required"/>
							</div>
						</div>						
						<div class="form-group row">
							<label class="col-sm-3 col-form-label">Photo Product</label>
							<div class="col-sm-5">
							<?php 
							if (!empty($image1)) {
								echo '<div class="img-choose" style="margin:inherit;margin-bottom:10px">
										<div class="img-choose-container">
											<img class="img" src="'.BASE_URL. $config['foto_path'] . $image1 . '?r=' . time() . '"/>
										</div>
									</div>
									';
							}
							?>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-sm-3 col-form-label">Receipt</label>
							<div class="col-sm-5">
							<?php 
							if (!empty($image2) ) {
								echo '<div class="img-choose" style="margin:inherit;margin-bottom:10px">
								<div class="img-choose-container">
											<img class="img" src="'.BASE_URL. $config['foto_path'] . $image2 . '?r=' . time() . '"/>
										</div>
									</div>
									';
							}
							?>
							</div>
						</div>
						<fieldset class="scheduler-border" id="case_result">
						<legend class="scheduler-border">Case Result</legend>				
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
								if($disabled=="disabled"){
									echo options(['name' => 'faulty_name_check', 'disabled' => $disabled], $options, set_value('faulty_name', @$faulty_name));
								}else{
									echo options(['name' => 'faulty_name_check'], $options, set_value('faulty_name_check', @$faulty_name_check));
								}
								?>
							<span style="color:red;font-size:10px">* Tambah master data faulty code <a href="https://rma.techno-solution.biz/faulty-code" target="_blank">klik disini</a></span>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-sm-3 col-form-label">Faulty Remark</label>
							<div class="col-sm-8">
								<input class="form-control" type="text" name="faulty_remark_check" <?=$disabled?> value="<?=set_value('faulty_remark_check', @$faulty_remark_check)?>" placeholder="Faulty Remark"/> 
							</div>
						</div>	
						</fieldset>
						<div class="form-group row" id="product_return">
							<label class="col-sm-3 col-form-label">Product Return Status</label>
							<div class="col-sm-6">
								<?php 
								$options_product_return['0'] = "[Pilih Product Return]";
								foreach ($product_return as $item) {
									$options_product_return[$item['id_product_return']] = $item['product_return'];
								}
								if($disabled=="disabled"){
									echo options(['name' => 'id_product_return', 'disabled'=>'disabled'], $options_product_return, set_value('id_product_return', @$id_product_return));
								}else{
									echo options(['name' => 'id_product_return'], $options_product_return, set_value('id_product_return', @$id_product_return));
								}
								?>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-sm-3 col-form-label">Case Status</label>
							<div class="col-sm-6">
								<?php 
								foreach ($case_status as $item) {
									$options_case[$item['id_case_status']] = "$item[id_case_status]. $item[case_status]";
								}
								if($disabled=="disabled"){
									echo options(['name' => 'id_case_status','id'=> 'id_case_status', 'disabled'=>'disabled'], $options_case, set_value('id_case_status', @$id_case_status));
								}else{
									echo options(['name' => 'id_case_status','id'=> 'id_case_status'], $options_case, set_value('id_case_status', @$id_case_status));
								}
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
								if($disabled=="disabled"){
									echo options(['name' => 'id_reject_reason','id'=> 'id_reject_reason', 'disabled'=>'disabled'], $options_reject_reason, set_value('id_reject_reason', @$id_reject_reason));
								}else{
									echo options(['name' => 'id_reject_reason','id'=> 'id_reject_reason'], $options_reject_reason, set_value('id_reject_reason', @$id_reject_reason));
								}
								?>
							</div>
						</div>
						<div class="form-group row" id="new_sku">
							<label class="col-sm-3 col-form-label">SKU New</label>
							<div class="col-sm-6">
								<?php
								$options_sku[0] = "[Pilih SKU No.]";
								if(!empty(@$data_sku)){
									foreach ($data_sku as $key => $val) {
										$options_sku[$val['sku']] = $val['sku']." - ".$val['nama_produk'];
									}		
								}
								if($disabled_sn=="disabled"){
									echo options(['name' => 'sku_new', 'disabled'=>'disabled'], $options_sku, set_value('sku_new', @$sku_new));
								}else{
									echo options(['name' => 'sku_new'], $options_sku, set_value('sku_new', @$sku_new));
								}
							?>
							<span style="color:red;font-size:10px">* Tambah master data sku <a href="https://rma.techno-solution.biz/sku" target="_blank">klik disini</a></span>
							</div>							
						</div>
						<div class="form-group row" id="new_sn">
							<label class="col-sm-3 col-form-label">S/N New</label>
							<div class="col-sm-8">
								<input class="form-control" type="text" name="serial_number_new" <?php echo $disabled_sn?> value="<?=set_value('serial_number_new', @$serial_number_new)?>" placeholder="Serial Number"/>
								<input class="form-control" type="text" name="serial_number_new_confirm" <?php echo $disabled_sn?> placeholder="Serial Number Confirmation"/>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-sm-3 col-form-label">Case Closed</label>
							<div class="col-sm-6">
								<?php 
								foreach ($data_status as $item) {
									$options_status[$item['id_status']] = "$item[nama_status]";
								}
								if($disabled=="disabled"){
									echo options(['name' => 'closed', 'disabled'=>'disabled'], $options_status, set_value('closed', @$closed));
								}else{
									echo options(['name' => 'closed'], $options_status, set_value('closed', @$closed));
								}
								?>
							</div>
						</div>
						<div class="form-group row mb-0">
							<div class="col-sm-8">
								<button type="submit" name="submit" value="submit" class="btn btn-primary" <?=$display?>>Submit</button>
								<input type="hidden" name="id" value="<?=@$_GET['id']?>"/>
							</div>
						</div>
					</div>
				</div>
				
			</form>
			<?php }?>
			</div>
			<div class="tab-pane fade" id="detail-history" role="tabpanel" aria-labelledby="history-tab">
				<div class="table-responsive">
					<table class="table table-striped table-bordered table-hover data-tables">
						<thead>
						<tr>
							<th>Created Date/Time</th>
							<th>Created By</th>
							<th>Status</th>
						</tr>
						</thead>
						<tbody>
							<?php
							helper ('html');
							$no = 1;
							global $list_action;
							$keterangan="";
							foreach ($history as $key => $val) {
								if($val['keterangan']!=""){
									$keterangan=" ($val[keterangan])";
								}
								echo '<tr>
										<td>' . format_tanggal_indo($val['created_date']) . '</td>
										<td>' . $val['created_by'].'</td>
										<td>' . $val['case_status'].' '.$keterangan. '</td>
									</tr>';
								$no++;
							}
							$settings['order'] = [0,'asc'];
							$settings['columnDefs'][] = ['targets' => 1, 'orderable' => false];
							?>
						</tbody>
					</table>
					<span id="dataTables-setting" style="display:none"><?=json_encode($settings)?></span>
				</div>					
			</div>
		</div>
	</div>
</div>
<script>
$(document).ready(function(){
	<?php if(@$id_case_status==8){?>
	$('#reject_reason').show();
	<?php }else{?>
	$('#reject_reason').hide();
	<?php }?>
	
	<?php if(@$id_case_status==6 || @$id_case_status==7 || @$serial_number_new!=""){?>
	$('#new_sn').show();
	<?php }else{?>
	$('#new_sn').hide();
	<?php }?>
	
	<?php if(@$id_case_status==7 || @$sku_new!=""){?>
	$('#new_sku').show();
	<?php }else{?>
	$('#new_sku').hide();
	<?php }?>
	
	<?php if(@$id_case_status>4){?>
	$('#case_result').show();
	<?php }else{?>
	$('#case_result').hide();
	<?php }?>
	
	<?php if(@$id_case_status>5){?>
	$('#product_return').show();
	<?php }else{?>
	$('#product_return').hide();
	<?php }?>
	
	$('#id_case_status').val(<?php echo @$id_case_status?>);
	$('#id_case_status').on('change', function() {
		if($('#id_case_status').val()=='8'){
			$('#reject_reason').show();
		}else{
			$('#reject_reason').hide();
		}
		
		if($('#id_case_status').val()=='6' || $('#id_case_status').val()=='7'){
			$('#new_sn').show();
		}else{
			$('#new_sn').hide();
		}
		
		if($('#id_case_status').val()=='7'){
			$('#new_sku').show();
		}else{
			$('#new_sku').hide();
		}
		
		if($('#id_case_status').val()>4){
			$('#case_result').show();
		}else{
			$('#case_result').hide();
		}
		
		if($('#id_case_status').val()>5){
			$('#product_return').show();
		}else{
			$('#product_return').hide();
		}
		
	});
});
</script>
<script>
// Image to Lightbox Overlay 
$('.img').on('click', function() {
  $('#overlay')
    .css({backgroundImage: `url(${this.src})`})
    .addClass('open')
    .one('click', function() { $(this).removeClass('open'); });
});
</script>
<div id="overlay"></div>