<?php
	class EmailNotifier {
		private $config;

		public function __construct($config) {
			$this->config = $config;
		}

		public function send($to, $subject, $message) {
			$headers = 'From: ' . $this->config['username'] . "\r\n" .
					   'Reply-To: ' . $this->config['username'] . "\r\n" .
					   'X-Mailer: PHP/' . phpversion();

			// This is a simple mail function for demonstration purposes.
			// In a real application, you should use a library like PHPMailer or SwiftMailer.
			return mail($to, $subject, $message, $headers);
		}
	}

?>