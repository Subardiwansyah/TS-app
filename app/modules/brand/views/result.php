<div class="card">
	<div class="card-header">
		<h5 class="card-title"><?=$current_module['judul_module']?></h5>
	</div>
	
	<div class="card-body">
		<a href="<?=current_url()?>/add" class="btn btn-success btn-xs"><i class="fa fa-plus pr-1"></i> Tambah Data</a>
		<hr/>
		<?php 
		if (!$result) {
			show_message('Data tidak ditemukan', 'error', false);
		} else {
			if (!empty($msg)) {
				show_alert($msg);
			}
			
			$column =[
						'ignore_search_urut' => 'No.'
						, 'nama_brand' => 'Nama Brand'
						, 'ignore_search_action' => 'Action'
					];
			
			
			$settings['order'] = [1,'desc'];
			
			$settings['lengthMenu'] = [[10, 25, 50, -1], [10, 25, 50, "all"]];			
			$settings['dom'] = 'lBfrtip';
			$settings['buttons'][] = [["extend" => 'pdfHtml5', "exportOptions" => ["modifier" => ["order" => "index","page" => "all","search" => "none"], 'columns'=> [ 0, 1 ]]],["extend" => 'excelHtml5', "exportOptions" => ["modifier" => ["order" => "index","page" => "all","search" => "none"], 'columns'=> [ 0, 1 ]]]];
			
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
			<tfoot>
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
		} ?>
	</div>
</div>