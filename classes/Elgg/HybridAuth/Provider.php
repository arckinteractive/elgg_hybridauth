<?php

namespace Elgg\HybridAuth;

class Provider {

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

	public function __construct($name, $settings = array(), $openid = null) {
		$this->name = $name;
		$this->settings = $settings;
		$this->openid = $openid;
	}

	public function getName() {
		return $this->name;
	}

	public function getSettings() {
		return $this->settings;
	}

	public function getOpenId() {
		return $this->openid;
	}
	
	public function getId() {
		return ($this->getOpenId()) ? 'OpenID' : $this->getName();
	}

	public function getAdapterParams() {
		return ($this->getOpenId()) ? array('openid_identifier' => $this->getOpenId()) : null;
	}

	public function isEnabled() {
		return (!empty($this->settings['enabled']));
	}

}
