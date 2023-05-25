<div class="card-body">
	<?php
	// echo '<pre>'; print_r($form_error); die;
	if (!empty($message)) {
		show_message($message);
	}
	// $this->load->library('auth_library');
	// $form_token = $this->auth_library->generateTokenForm();
	helper('form');
	?>
	<p>Already have an Account? <a href="<?=$config['base_url']?>">Sign In</a></p>
	<form action="<?=current_url()?>" method="post" accept-charset="utf-8">	
		<div class="form-group">
			<label>Name</label>
			<div class="form-inline">
			<input type="text" name="name" value="<?=set_value('name')?>" class="form-control register-input" placeholder="Name" aria-label="Name" required>
			</div>
		</div>
		<div class="form-group">
			<label>Phone</label>
			<input type="phone"  name="phone" value="<?=set_value('phone', '')?>" class="form-control register-input" placeholder="Phone" aria-label="Email" required>
		</div>
		<div class="form-group">
			<label>Email</label>
			<input type="email"  name="email" value="<?=set_value('email', '')?>" class="form-control register-input" placeholder="Email" aria-label="Email" required>
		</div>
		<div class="form-group">
			<label>Address</label>
			<textarea class="form-control" name="address"><?=set_value('address', '')?></textarea>
		</div>
		<div class="form-group">
			<label>Password</label>
			<input type="password"  name="password" class="form-control register-input" placeholder="Password" aria-label="Password" required>
			<div class="pwstrength_viewport_progress"></div>
		</div>
		<div class="form-group">
			<label>Confirm Password</label>
			<input type="password"  name="password_confirm" class="form-control register-input" placeholder="Confirm Password" aria-label="Confirm Password" required>
		</div>
		<div class="form-group" style="margin-bottom:0">
			<button type="submit" name="submit" value="submit" class="btn btn-success" style="display:block;width:100%">Create Account</button>
			<?=csrf_field()?>
		</div>
	</form>
</div>