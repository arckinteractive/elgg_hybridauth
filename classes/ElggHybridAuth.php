<?php

class ElggHybridAuth extends Hybrid_Auth {

	public function __construct() {

		$config = array(
			'base_url' => elgg_get_plugin_setting('base_url', 'elgg_hybridauth'),
			'debug_mode' => elgg_get_plugin_setting('debug_mode', 'elgg_hybridauth'),
			'debug_file' => elgg_get_plugin_setting('debug_file', 'elgg_hybridauth'),
			'providers' => unserialize(elgg_get_plugin_setting('providers', 'elgg_hybridauth'))
		);

		parent::__construct($config);
	}

}

?>
