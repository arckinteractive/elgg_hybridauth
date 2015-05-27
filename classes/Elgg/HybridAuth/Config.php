<?php

namespace Elgg\HybridAuth;

class Config {

	/**
	 * HybridAuth config
	 * @var array
	 */
	private $params;

	/**
	 * Constructor
	 * @param array $params HybridAuth config
	 */
	public function __construct($params = array()) {
		$this->params = $params;
	}

	/**
	 * Constructs a config object from plugin settings
	 * @return Config
	 */
	public static function factory() {

		$params = array(
			'base_url' => elgg_normalize_url('hybridauth/endpoint'),
			'debug_mode' => (bool) elgg_get_plugin_setting('debug_mode', 'elgg_hybridauth'),
			'debug_file' => elgg_get_plugins_path() . 'elgg_hybridauth/debug.info',
			'providers' => unserialize(elgg_get_plugin_setting('providers', 'elgg_hybridauth'))
		);

		return new Config($params);
	}

	/**
	 * Returns config values
	 *
	 * @param string $key Optional key
	 * @return mixed
	 */
	public function get($key = null) {
		if (!$key) {
			return $this->params;
		}
		if (isset($this->params[$key])) {
			return $this->params[$key];
		}
		return false;
	}
}
