<?php

namespace Elgg\HybridAuth;

class Session {

	const DEFAULT_SESSION_NAME = 'hybridauth_session_data';
	const DEFAULT_HANDLE = 'uid';

	/**
	 * HybridAuth cient
	 * @var \Hybrid_Auth 
	 */
	private $client;

	/**
	 * User entity
	 * @var \ElggUser 
	 */
	protected $user;

	/**
	 * Session name
	 * @var string
	 */
	protected $name;

	/**
	 * HybridAuth config
	 * @var array 
	 */
	protected $config;

	/**
	 * Persistent session flag
	 * @var bool
	 */
	protected $persistent;

	/**
	 * Constructor
	 * @param \ElggUser $user   User entity
	 * @param string    $name   Session name
	 * @param string    $handle Handle used to store auth records
	 */
	public function __construct(\ElggUser $user = null, $name = null, $handle = null) {
		$this->user = ($user) ? : elgg_get_logged_in_user_entity();
		$this->name = ($name) ? : self::DEFAULT_SESSION_NAME;
		$this->handle = ($handle) ? : self::DEFAULT_HANDLE;
		$this->persistent = (bool) elgg_get_plugin_setting('persistent_session', 'elgg_hybridauth');
	}

	/**
	 * Sets HybridAuth config
	 * 
	 * @param array $config Configuration array
	 * @return \Elgg\HybridAuth\Session
	 */
	public function setConfig(array $config = array()) {
		$this->config = $config;
		return $this;
	}

	/**
	 * Returns HybridAuth config
	 * @return array
	 */
	public function getConfig() {
		return elgg_trigger_plugin_hook('config', 'hybridauth', array('session' => $this), $this->config);
	}

	/**
	 * Sets session user
	 * 
	 * @param \ElggUser $user User entity
	 * @return bool
	 */
	public function setUser(\ElggUser $user) {
		if (!$this->user instanceof \ElggUser) {
			$this->user = $user;
			return true;
		}
		return false;
	}

	/**
	 * Returns session user
	 * @return \ElggUser
	 */
	public function getUser() {
		return $this->user;
	}

	public function getSessionName() {
		return $this->session_name;
	}

	public function getSessionHandle() {
		return $this->handle;
	}
	
	/**
	 * Returns an instanceof Hybrid_Auth client
	 * 
	 * @param array $config Optional params to override the defaults
	 * @return \Hybrid_Auth
	 */
	public function getClient() {
		return new \Hybrid_Auth($this->getConfig());
	}

	/**
	 * Returns a provider
	 *
	 * @param string $name Provider name
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

		$providers = array();
		$config = elgg_extract('providers', $this->getConfig(), array());

		foreach ($config as $n => $s) {
			if ($n == 'OpenID') {
				$providers = array_merge($providers, $this->getOpenIdProviders($s));
			} else {
				$providers[$n] = new Provider($n, $s);
			}
		}
		return $providers;
	}

	/**
	 * Returns all enabled providers
	 * @return Provider[]
	 */
	public function getEnabledProviders() {

		$providers = $this->getProviders();
		return array_filter($providers, function(Provider $p) {
			return ($p->isEnabled());
		});
	}

	/**
	 * Returns OpenId providers
	 *
	 * @param array $settings Provider settings
	 * @return Provider[]
	 */
	public function getOpenIdProviders($settings = array()) {

		$providers = array();

		$openid_settings = elgg_get_plugin_setting('openid_providers', 'elgg_hybridauth');
		$openid_providers = ($openid_settings) ? unserialize($openid_settings) : array();

		foreach ($openid_providers as $openid_provider) {
			$oin = elgg_extract('name', $openid_provider);
			$oii = elgg_extract('identifier', $openid_provider);
			if ($oin && $oii) {
				$providers[$oin] = new Provider($oin, $settings, $oii);
			}
		}

		return $providers;
	}

	/**
	 * Constructs a new Session from data previously stored for the logged in user
	 * @return bool
	 */
	public function restore() {
		if (!$this->persistent) {
			return true;
		}
		$id = elgg_get_plugin_user_setting("$this->name:id", $this->user->guid, 'elgg_hybridauth');
		$session_data = elgg_get_plugin_user_setting($this->name, $this->user->guid, 'elgg_hybridauth');
		if (!$session_data) {
			return false;
		}
		if (($id && $id != session_id()) || !$this->getClient()->getSessionData()) {
			elgg_set_plugin_user_setting("$this->name:id", session_id(), $this->user->guid, 'elgg_hybridauth');
			return $this->getClient()->restoreSessionData($session_data);
		}
		return false;
	}

	/**
	 * Saves session data
	 *
	 * @param string    $session_data Session data
	 * @param \ElggUser $user         User owning the session
	 * @param string    $name         Session name
	 * @return \Elgg\HybridAuth\Session
	 */
	public function save() {
		if (!$this->persistent) {
			return true;
		}
		$session_data = $this->getClient()->getSessionData();
		elgg_set_plugin_user_setting("$this->name:id", session_id(), $this->user->guid, 'elgg_hybridauth');
		return elgg_set_plugin_user_setting($this->name, $session_data, $this->user->guid, 'elgg_hybridauth');
	}

	/**
	 * Returns HybridAuth adapter for the provider
	 *
	 * @param \Elgg\HybridAuth\Provider $provider Provider
	 * @return \Hybrid_Provider_Adapter|false
	 */
	public function getAdapter(Provider $provider) {
		try {
			$this->restore();
			return $this->getClient()->getAdapter($provider->getId(), $provider->getAdapterParams());
		} catch (\Exception $ex) {
			elgg_log($ex->getMessage(), 'ERROR');
			return false;
		}
	}

	/**
	 * Checks if the user has previously authenticated with the provider
	 *
	 * @param \Elgg\HybridAuth\Provider $provider Provider
	 * @param string                    $handle   Handle for differentiating between multiple ids
	 * @return mixed Provider id or null
	 */
	public function isAuthenticated(Provider $provider) {
		return elgg_get_plugin_user_setting($this->getAuthRecordName($provider), $this->user->guid, 'elgg_hybridauth');
	}

	/**
	 * Checks if the user is currently connected to the provider
	 * 
	 * @param \Elgg\HybridAuth\Provider $provider Provider
	 * @return bool
	 */
	public function isConnected(Provider $provider) {
		try {
			$this->restore();
			$adapter = $this->getAdapter($provider);
			$connected = ($adapter && $adapter->isUserConnected());
			$this->save();

			return $connected;
		} catch (\Exception $ex) {
			elgg_log($ex->getMessage(), 'ERROR');
		}
		return false;
	}

	/**
	 * Returns plugin setting name that used to store provider id
	 * 
	 * @param \Elgg\HybridAuth\Provider $provider Provider
	 * @return string
	 */
	public function getAuthRecordName(Provider $provider) {
		return "{$provider->getName()}:{$this->handle}";
	}

	/**
	 * Adds auth records that signify that user is connected to the provider
	 *
	 * @param \Elgg\HybridAuth\Provider $provider Provider
	 * @param mixed                     $profile  Profile object or id
	 * @return bool
	 */
	public function addAuthRecord(Provider $provider, $profile) {
		if ($this->handle == Session::DEFAULT_HANDLE) {
			elgg_trigger_plugin_hook('hybridauth:authenticate', $provider->getName(), array(
				'provider' => $provider,
				'entity' => $this->user,
				'profile' => $profile,
			));
		} else {
			elgg_trigger_plugin_hook('hybridauth:authenticate:session', $provider->getName(), array(
				'profile' => $profile,
				'provider' => $provider,
				'session' => $this,
			));
		}
		$uid = (is_object($profile)) ? $profile->identifier : $profile;
		return elgg_set_plugin_user_setting($this->getAuthRecordName($provider), $uid, $this->user->guid, 'elgg_hybridauth');
	}

	/**
	 * Removes auth records that signify that user is connected to the provider
	 *
	 * @param \Elgg\HybridAuth\Provider $provider Provider
	 * @return bool
	 */
	public function removeAuthRecord(Provider $provider) {
		if ($this->handle == Session::DEFAULT_HANDLE) {
			elgg_trigger_plugin_hook('hybridauth:deauthenticate', $provider->getName(), array(
				'provider' => $provider,
				'entity' => $this->user
			));
		} else {
			elgg_trigger_plugin_hook('hybridauth:deauthenticate:session', $provider->getName(), array(
				'provider' => $provider,
				'session' => $this
			));
		}
		return elgg_unset_plugin_user_setting($this->getAuthRecordName($provider), $this->user->guid, 'elgg_hybridauth');
	}

	/**
	 * Authenticates the session owner with the provider
	 *
	 * @param \Elgg\HybridAuth\Provider $provider Provider
	 * @param bool                      $addauth  Add auth records
	 * @return \Hybrid_User_Profile|false
	 */
	public function authenticate(Provider $provider, $addauth = true) {
		try {
			$this->restore();
			$adapter = $this->getClient()->authenticate($provider->getId(), $provider->getAdapterParams());
			$profile = ($adapter) ? $adapter->getUserProfile() : false;
			$this->save();
			if ($profile && $addauth) {
				$this->addAuthRecord($provider, $profile);
			}
		} catch (\Exception $e) {
			elgg_log($e->getMessage(), 'ERROR');
			set_input('error', $e->getMessage());
			$this->deauthenticate($provider);
		}

		return $profile;
	}

	/**
	 * Deauthenticate the session owner from the provider
	 *
	 * @param \Elgg\HybridAuth\Provider $provider Provider
	 * @param bool                      $rmauth   Remove auth records
	 * @return bool
	 */
	public function deauthenticate(Provider $provider, $rmauth = true) {

		try {
			$this->restore();
			$adapter = $this->getAdapter($provider);
			if ($adapter && $adapter->isUserConnected()) {
				$adapter->logout();
			}
			$this->save();
			if ($rmauth) {
				$this->removeAuthRecord($provider);
			}
		} catch (\Exception $e) {
			elgg_log($e->getMessage(), 'ERROR');
			return false;
		}

		return true;
	}

}
