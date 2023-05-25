	<div class="copyright">
		<?php $footer = $setting_web['footer_login'] ? str_replace('{{YEAR}}', date('Y'), $setting_web['footer_login']) : '';
		echo $footer;
		?>
	</div>
	</div><!-- login container -->
</body>
</html>