<div class="card">
	<div class="card-header">
		<h5 class="card-title"><?=$title?></h5>
	</div>
	
	<div class="card-body">
		<?php
		helper(['html', 'format']);
		if (!empty($msg)) {
			show_message($msg['content'], $msg['status']);
		}
		?>
		<form method="post" action="" class="form-horizontal" enctype="multipart/form-data">
			<div class="tab-content" id="myTabContent">
				<div class="form-group row">
					<label class="col-sm-3 col-md-2 col-lg-3 col-xl-2 col-form-label">Pilih File Excel</label>
					<div class="col-sm-5">
						<input type="file" class="file" name="file_excel">
							<?php if (!empty($form_errors['file_excel'])) echo '<small class="alert alert-danger">' . $form_errors['file_excel'] . '</small>'?>
							<div class="mt-1">Contoh file: <a title="<?=$tabel['warranty']['file_excel']['title']?>" href="<?=$tabel['warranty']['file_excel']['url']?>"><?=$tabel['warranty']['file_excel']['display']?></a></div>
							<div class="mt-1">Contoh Data: Barcode/SKU: 61505, SN: 6150541613462002316, Tgl Jual (tgl.bln.thn): 27.01.2021, Nama Toko: TOKPED</div>
							<small class="small" style="display:block;color:red">File ekstensi harus .xlsx, Mohon isi data sesuai template (tanpa ada kolom merge)</small>
						<div class="upload-img-thumb"><span class="img-prop"></span></div>
					</div>
				</div>
				<div class="form-group row mb-0">
					<div class="col-sm-5">
						<button type="submit" name="submit" value="submit" class="btn btn-primary">Submit</button>
						<input type="hidden" name="id" value="<?=@$_GET['id']?>"/>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
<script type="text/javascript">
function downloadCSVFile(csv, filename) {
    var csv_file, download_link;
    csv_file = new Blob([csv], {type: "text/csv"});
    download_link = document.createElement("a");
    download_link.download = filename;
    download_link.href = window.URL.createObjectURL(csv_file);
    download_link.style.display = "none";
    document.body.appendChild(download_link);
    download_link.click();
}

document.getElementById("download-button").addEventListener("click", function () {
    var html = document.querySelector("table").outerHTML;
	htmlToCSV(html, "report_upload.csv");
});

function htmlToCSV(html, filename) {
	var data = [];
	var rows = document.querySelectorAll("table tr");
			
	for (var i = 0; i < rows.length; i++) {
		var row = [], cols = rows[i].querySelectorAll("td, th");
				
		 for (var j = 0; j < cols.length; j++) {
		        row.push(cols[j].innerText);
                 }
		        
		data.push(row.join(","));		
	}
	//data.shift()
	//console.log(data);
	//return false;
	downloadCSVFile(data.join("\n"), filename);
}
</script>