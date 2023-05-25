<div class="card">
	<div class="card-header">
		<h5 class="card-title"><?=$title?></h5>
	</div>
	
	<div class="card-body">
		<?php 
		helper ('html');
		if (!empty($msg)) {
			// echo '<pre>'; print_r($msg); die;
			show_alert($msg);
		}
		
		echo btn_label(['class' => 'btn btn-success btn-xs',
			'url' => module_url() . '?action=add',
			'icon' => 'fa fa-plus',
			'label' => 'Tambah Data'
		]);
		
		echo btn_label(['class' => 'btn btn-light btn-xs',
			'url' => module_url(),
			'icon' => 'fa fa-arrow-circle-left',
			'label' => $current_module['judul_module']
		]);
		?>
		<hr/>
		<form method="post" action="" id="form-container" enctype="multipart/form-data">
			<div class="tab-content" id="myTabContent">
				<div class="form-group row">
					<label class="col-sm-3 col-md-2 col-lg-3 col-xl-2 col-form-label">Question</label>
					<div class="col-sm-8">
						<input class="form-control" type="text" name="question" disabled value="<?=set_value('question', @$question)?>" placeholder="Question" required="required"/>
						<input type="hidden" name="question_old" value="<?=set_value('question', @$question)?>">
					</div>
				</div>
				<div class="form-group row">
					<label class="col-sm-3 col-md-2 col-lg-3 col-xl-2 col-form-label">Answer</label>
					<div class="col-sm-8">
						<textarea class="form-control" type="text" name="answer" disabled placeholder="Answer" required="required"><?=set_value('answer', @$answer)?></textarea>
					</div>
				</div>
				<div class="form-group row">
					<label class="col-sm-3 col-md-2 col-lg-3 col-xl-2 col-form-label">Enabled</label>
					<div class="col-sm-5 form-inline">
						<?php 
						foreach ($status as $item) {
							$options[$item['id_status']] = $item['nama_status'];
						}
						echo options(['name' => 'enabled','disabled'=>'disabled'], $options, set_value('enabled', @$enabled));?>
					</div>
				</div>
				<?php 
				$id = '';
				if (!empty($_GET['id'])) {
					$id = $_GET['id'];
				} elseif (!empty($msg['id_faq'])) { // ADD Auto Increment
					$id = $msg['id_faq'];
				} ?>
				<input type="hidden" name="id" value="<?=$id?>"/>
			</div>
		</form>
	</div>
</div>