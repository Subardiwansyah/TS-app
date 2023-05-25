<div class="card-body">
	<?php
	if (@$message) {
		show_message($message);
	} ?>
	<p>We will send you a link to reset your password.</p>
	<form method="post" action="<?=current_url()?>">
	<div class="form-group">
		<input type="email"  name="email" value="<?=set_value('email')?>" class="form-control register-input" placeholder="Email" aria-label="Email" required>
	</div>
	<div class="form-group" style="margin-bottom:0">
		<button type="submit" name="submit" value="submit" class="btn btn-success" style="display:block;width:100%">Submit</button>
		<?=csrf_field()?>
	</div>
	</form>
</div>