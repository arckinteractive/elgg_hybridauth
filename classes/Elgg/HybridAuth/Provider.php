<?php

namespace Elgg\HybridAuth;

class Provider {

	/**
	 * Client used to invoke the adapter
	 * @var \Hybrid_Auth
	 */
	protected $client;

	/**
	 * Provider name
	 * @var string
	 */
	protected $name;

	/**
	 * Provider settings
	 * @var array
	 */
	protected $settings;

	/**
	 * OpenId identifier
	 * @var string 
	 */
	protected $openid;

	public function __construct(\Hybrid_Auth $client) {
		$this->client = $client;
	}

	public function __toString() {
		return $this->getName();
	}
	
	public function setName($name) {
		$this->name = $name;
		return $this;
	}

	public function getName() {
		return $this->name;
	}

	public function setSettings($settings = array()) {
		$this->settings = (array) $settings;
		return $this;
	}

	public function getSettings() {
		return $this->settings;
	}

	public function setOpenId($identifier) {
		$this->openid = $identifier;
		return $this;
	}

	public function getOpenId() {
		return $this->openid;
	}

	public function isEnabled() {
		return (!empty($this->settings['enabled']));
	}

	public function getAdapter() {
		try {
			$adapter = $this->client->getAdapter($this->getName());
		} catch (Exception $ex) {
			elgg_log($ex->getMessage(), 'ERROR');
			$adapter = false;
		}
		return $adapter;
	}

	public function isAuthenticated(\ElggEntity $user = null, $setting_name = null) {
		if (!$user && !elgg_is_logged_in()) {
			return false;
		}
		if (!$setting_name) {
			$setting_name = "{$this}:uid";
		}
		return elgg_get_plugin_user_setting($setting_name, $user->guid, 'elgg_hybridauth');
	}
	
}
