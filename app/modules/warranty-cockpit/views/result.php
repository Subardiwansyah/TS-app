<div class="card">
	<div class="card-header">
		<h5 class="card-title"><?=$current_module['judul_module']?></h5>
	</div>
	
	<div class="card-body">
		<?php 
		//if (!$result) {
		//	show_message('Data tidak ditemukan', 'error', false);
		//} else {
			if (!empty($msg)) {
				//show_alert($msg);
			}
			
			$column =[
						'ignore_search_urut' => 'No.'
						, 'ignore_search_action' => 'Action'
						, 'case_no' => 'Case ID'
						, 'created_date' => 'Created Datetime'
						, 'case_status' => 'Status'
						, 'type_case' => 'Case'
						, 'full_name' => 'Requester'
						, 'customer_no' => 'Customer ID'
						, 'sku' => 'SKU'
						, 'updated_by' => 'Last Handler'
						, 'day' => 'Day'
						, 'nama_status' => 'Case Closed'
					];
			
			
			$settings['order'] = [2,'desc'];
			$index = 0;
			$th = '';
			foreach ($column as $key => $val) {
				$th .= '<th>' . $val . '</th>'; 
				if (strpos($key, 'ignore_search') !== false) {
					$settings['columnDefs'][] = ["targets" => $index, "orderable" => false];
				}
				$index++;
			}
	
			?>
			
			<table id="table-result" class="table display table-striped table-bordered table-hover" style="width:100%">
			<thead>
				<tr>
					<?=$th?>
				</tr>
			</thead>
			<tfoot style="display: table-header-group">
				<tr>
					<?=$th?>
				</tr>
			</tfoot>
			</table>
			<?php
				foreach ($column as $key => $val) {
					$column_dt[] = ['data' => $key];
				}
			?>
			<span id="dataTables-column" style="display:none"><?=json_encode($column_dt)?></span>
			<span id="dataTables-setting" style="display:none"><?=json_encode($settings)?></span>
			<span id="dataTables-url" style="display:none"><?=current_url() . '?action=getDataDT'?></span>
			<?php 
		//} ?>
	</div>
</div>