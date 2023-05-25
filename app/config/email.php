<?php
class EmailConfig {
	
	public $provider = 'standard';
	// public $provider = 'Google';
	// public $provider = 'AmazonSES';

	public $client = [	'standard' => [
										'host' => 'smtp.gmail.com', 
										'username' => 'technosolution.notification@gmail.com', 
										'password' => 'Welcome2022'
									]
						,'google' => ['client_id' => '', 
										'client_secret' => '', 
										'refresh_token' => ''
									]
						, 'ses' => ['username' => '', 
									'password' => ''
									]
					];
	
	// Disesuaikan dengan konfigurasi username
	public $from = 'technosolution.notification@gmail.com';
}