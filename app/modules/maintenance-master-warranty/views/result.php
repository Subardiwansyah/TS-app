<div class="card">
	<div class="card-header">
		<h5 class="card-title"><?=$current_module['judul_module']?></h5>
	</div>
	
	<div class="card-body">
		<!--
		<a href="<?=current_url()?>/add" class="btn btn-success btn-xs"><i class="fa fa-plus pr-1"></i> Add Warranty</a>
		<hr/>
		-->
		<?php 

			if (!empty($msg)) {
				//show_alert($msg);
			}
			
			$column =[
						'ignore_search_urut' => 'No.'
						, 'id_wty' => 'Id WTY'
						, 'sku' => 'SKU'
						, 'nama_kategori' => 'Brand'
						, 'nama_subkategori' => 'Produk'
						, 'nama_produk' => 'Deskripsi Produk'
						, 'serial_number' => 'Serial Number'										
						, 'nama_store' => 'Nama Store'
						, 'wty_period' => 'WTY Period'
						, 'wty_start' => 'WTY Start'
						, 'wty_end' => 'WTY End'		
						, 'wty_status' => 'WTY Status'
						, 'unit_status' => 'Unit Status'
						, 'jml_claim' => 'Claim'						
						, 'case_no' => 'Case No'
						, 'ignore_search_action' => 'Action'
					];
			
			$settings['order'] = [1,'asc'];
			
			$settings['lengthMenu'] = [[10, 25, 50, -1], [10, 25, 50, "all"]];			
			$settings['dom'] = 'lBfrtip';
			$settings['buttons'][] = [["extend" => 'pdfHtml5', "exportOptions" => ["modifier" => ["order" => "index","page" => "all","search" => "none"], 'columns'=> [ 0, 2, 3, 4, 5, 6, 7, 8 ]]],["extend" => 'excelHtml5', "exportOptions" => ["modifier" => ["order" => "index","page" => "all","search" => "none"], 'columns'=> [ 0, 2, 3, 4, 5, 6, 7, 8 ]]]];
			
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
		?>
	</div>
</div>