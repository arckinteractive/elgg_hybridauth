<?php

class ElggHybridAuth {

	/**
	 * Constructor
	 * @deprecated since version 1.3 Use \Elgg\HybridAuth\Client
	 */
	public function __construct() {
		$this->client = (new \Elgg\HybridAuth\Session())->getClient();
	}

	/**
	 * Magic method to make calls on a HybridAuth client
	 *
	 * @param string $name      Method name
	 * @param array  $arguments Method arguments
	 * @return mixed
	 */
	public function __call($name, $arguments) {
		return call_user_func_array(array($this->client, $name), $arguments);
	}
}
