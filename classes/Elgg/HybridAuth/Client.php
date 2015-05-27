<?php

namespace Elgg\HybridAuth;

class Client {

	/**
	 * Config
	 * @var \Elgg\HybridAuth\Config
	 */
	private $_config;

	/**
	 * Session
	 * @var \Elgg\HybridAuth\Session
	 */
	private $_session;

	/**
	 * HybridAuth client
	 * @var \Hybrid_Auth 
	 */
	private $_client;

	/**
	 * Providers
	 * @var Provider[] 
	 */
	static $providers;

	/**
	 * Constructor
	 *
	 * @param array  $params       An array of HybridAuth config parameters
	 * @param string $session_data Previously stored session data
	 */
	public function __construct($params = null, $session_data = null) {

		$this->_config = ($params) ? new Config($params) : Config::factory();
		$this->_session = ($session_data) ? new Session($session_data) : Session::factory();
		$this->_client = new \Hybrid_Auth($this->_config->get());
		//$this->_client->restoreSessionData($this->_session->getSessionData());
	}

	/**
	 * Returns config
	 * @return \Elgg\HybridAuth\Config
	 */
	public function config() {
		return $this->_config;
	}

	/**
	 * Returns client
	 * @return \Hybrid_Auth
	 */
	public function client() {
		return $this->_client;
	}

	/**
	 * Returns a provider
	 *
	 * @param string $name Provider id
	 * @return Provider|false
	 */
	public function getProvider($name) {
		$providers = $this->getProviders();
		return (!empty($providers[$name])) ? $providers[$name] : false;
	}

	/**
	 * Returns configured providers
	 * @return Provider[]
	 */
	public function getProviders() {

		if (isset(self::$providers)) {
			return self::$providers;
		}

		$providers = array();
		$config = $this->config()->get('providers');

		foreach ($config as $n => $s) {
			if ($n == 'OpenID') {
				$providers = array_merge($providers, $this->getOpenIdProviders());
			} else {
				$providers[$n] = (new Provider($this->client()))->setName($n)->setSettings($s);
			}
		}
		
		self::$providers = $providers;
		return $providers;
	}

	/**
	 * Returns OpenId providers
	 * @return Provider[]
	 */
	public function getOpenIdProviders() {

		$providers = array();

		$openid_settings = elgg_get_plugin_setting('openid_providers', 'elgg_hybridauth');
		$openid_providers = ($openid_settings) ? unserialize($openid_settings) : array();

		$s = elgg_extract('OpenID', $this->config()->get('providers'));

		foreach ($openid_providers as $openid_provider) {
			$oin = elgg_extract('name', $openid_provider);
			$oii = elgg_extract('identifier', $openid_provider);
			if ($oin && $oii) {
				$providers[$oin] = (new Provider($this->client()))->setName($oin)->setSettings($s)->setOpenId($oii);
			}

		}

		return $providers;
	}

}
