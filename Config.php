<?php


	class Config {
		private $settings = [];

		public function __construct(array $settings) {
			$this->settings = $settings;
		}

		public function get($key) {
			return $this->settings[$key] ?? null;
		}
	}

?>
