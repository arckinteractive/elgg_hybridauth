<?php

namespace Elgg\HybridAuth;

class Session {

	/**
	 * Serialized HybridAuth session data
	 * @var string
	 */
	private $session_data;

	/**
	 * Constructor
	 *
	 * @param string $session_data Serialized session data
	 */
	public function __construct($session_data = null) {
		$this->session_data = is_string($session_data) ? $session_data : null;
	}

	/**
	 * Constructs a new Session from data previously stored for the logged in user
	 * @return \Elgg\HybridAuth\Session
	 */
	public static function factory() {
		$session_data = false;
		if (elgg_get_plugin_setting('persistent_session', 'elgg_hybridauth')) {
			$session_data = elgg_get_plugin_user_setting('hybridauth_session_data', null, 'elgg_hybridauth');
		}
		return new Session($session_data);
	}

	/**
	 * Returns session data
	 * @return string|null
	 */
	public function getSessionData() {
		return $this->session_data;
	}
}
