<?php

class ElggHybridAuth {

	/**
	 * Constructor
	 * @deprecated since version 1.3 Use \Elgg\HybridAuth\Client
	 */
	public function __construct($params = null, $session_data = null) {
		$this->client = (new \Elgg\HybridAuth\Client($params, $session_data))->client();
	}

	public function __call($name, $arguments) {
		return call_user_func_array(array($this->client, $name), $arguments);
	}
}
