<div class="card">
	<div class="card-header">
		<h5 class="card-title"><?=$current_module['judul_module']?></h5>
	</div>
	
	<div class="card-body">
		<div class="row clearfix">
		<?php 
		if (!$result) {
			show_message('Data tidak ditemukan', '', false);
		} else {
			
			?>
			
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
				<?php
					// echo $user; die;
					$no=1;
					foreach ($result as $key => $val)
					{
				?>
				<div class="panel panel-success">
					<div class="panel-heading" role="tab" id="headingOne_<?php echo $val['id_faq']?>">
						<h4 class="panel-title">
							<a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne_<?php  echo $val['id_faq']?>" aria-expanded="true" aria-controls="collapseOne_<?php echo $val['id_faq']?>">
								<p style="color:#fff"><?php echo "$no. ".$val['question']?></p>
							</a>
						</h4>
					</div>
					<div id="collapseOne_<?php echo $val['id_faq']?>" class="panel-collapse collapse in <? if($no==1)echo 'show'?>" role="tabpanel" aria-labelledby="headingOne_<?php echo $val['id_faq']?>">
						<div class="panel-body">
							<p style="padding:5px"><?php echo $val['answer']?></p>
						</div>
					</div>
				</div>
				<?php $no++;}?>
			</div>
		</div>
		<?php }?>
		</div>
	</div>
</div>