<?php

/**
 * Elgg HybridAuth
 */
// Composer autoload
require_once __DIR__ . '/vendor/autoload.php';

elgg_register_event_handler('init', 'system', 'elgg_hybridauth_init');
elgg_register_event_handler('upgrade', 'system', 'elgg_hybridauth_upgrade');

/**
 * Initialize the plugin
 * @return void
 */
function elgg_hybridauth_init() {

	elgg_register_page_handler('hybridauth', 'elgg_hybridauth_page_handler');

	elgg_register_action('elgg_hybridauth/settings/save', __DIR__ . '/actions/settings/save.php', 'admin');
	elgg_register_action('hybridauth/register', __DIR__ . '/actions/register.php', 'public');
	elgg_register_action('hybridauth/deauthorize', __DIR__ . '/actions/deauthorize.php');
	elgg_register_action('hybridauth/import/elgg_social_login', __DIR__ . '/actions/import/elgg_social_login.php', 'admin');
	elgg_register_action('hybridauth/import/social_connect', __DIR__ . '/actions/import/social_connect.php', 'admin');

	elgg_extend_view('forms/login', 'hybridauth/login');
	elgg_extend_view('forms/hybridauth/login', 'hybridauth/aux_login');
	elgg_extend_view('forms/register', 'hybridauth/login');

	elgg_register_css('hybridauth.css', elgg_get_simplecache_url('css', 'hybridauth/core'));
	elgg_register_simplecache_view('css/hybridauth/core');

	elgg_register_js('hybridauth.js', elgg_get_simplecache_url('js', 'hybridauth/core'));
	elgg_register_simplecache_view('js/hybridauth/core');

	elgg_register_plugin_hook_handler('public_pages', 'walled_garden', 'elgg_hybridauth_public_pages');

	elgg_register_plugin_hook_handler('config', 'hybridauth', 'elgg_hybridauth_config');

	elgg_register_event_handler('login:before', 'user', 'elgg_hybridauth_aux_provider');
	//elgg_register_event_handler('login', 'user', 'elgg_hybridauth_authenticate_all_providers');

	elgg_register_menu_item('page', array(
		'name' => 'hybridauth:accounts',
		'text' => elgg_echo('hybridauth:accounts'),
		'href' => "hybridauth/accounts",
		'selected' => (elgg_in_context('hybridauth')),
		'contexts' => array('settings'),
	));
}

/**
 * Page handler callback for /hybridauth
 * Used an auth start and endpoint
 *
 * To authenticate a given provider, use the following URL structure
 * /hybridauth/authenticate?provider=<provider>
 *
 * If you are authenticating a provider for a logged in user, and would like to
 * forward the user to a specific page upon successful authentication,
 * pass an encoded URL as a 'elgg_forward_url' URL query parameter, 
 * e.g. /hybridauth/authenticate?provider=<provider>&elgg_forward_to=<url>.
 * This can be helpful if you are implementing an import or sharing tool:
 * you can first check if the user is authenticated with a given provider
 * and then use this handler to avoid duplicating the authentication logic
 *
 * @param array  $segments   URL segments
 * @param string $identifier "hybridauth"
 * @return boolean
 */
function elgg_hybridauth_page_handler($segments, $identifier) {

	if (!isset($_SESSION['hybridauth'])) {
		$_SESSION['hybridauth'] = array(
			'friend_guid' => get_input('friend_guid'),
			'invitecode' => get_input('invitecode')
		);
	}

	switch ($segments[0]) {

		case 'authenticate' :
			echo elgg_view('resources/hybridauth/authenticate');
			return true;

		case 'endpoint' :
			echo elgg_view('resources/hybridauth/endpoint');
			return true;

		case 'accounts' :
			set_input('username', $segments[1]);
			echo elgg_view('resources/hybridauth/accounts');
			return true;
	}

	return false;
}

/**
 * Add hybridauth to allowed walled garden pages
 *
 * @param string $hook   "public_pages"
 * @param string $type   "walled_garden"
 * @param array  $return Public pages
 * @param array  $params Hook params
 * @return array
 */
function elgg_hybridauth_public_pages($hook, $type, $return, $params) {

	$return = (array) $return;
	$return[] = 'hybridauth/.*';
	$return[] = 'action/hybridauth/.*';
	return $return;
}

/**
 * Prepares HybridAuth config
 *
 * @param string $hook   "config"
 * @param string $type   "hybridauth"
 * @param array  $return Config
 * @param array  $params Hook Params
 * @return array
 */
function elgg_hybridauth_config($hook, $type, $return, $params) {

	$return = (array) $return;

	$defaults = array(
		'base_url' => elgg_normalize_url('hybridauth/endpoint'),
		'debug_mode' => (bool) elgg_get_plugin_setting('debug_mode', 'elgg_hybridauth'),
		'debug_file' => elgg_get_config('dataroot') . 'elgg_hybridauth_debug',
		'providers' => unserialize(elgg_get_plugin_setting('providers', 'elgg_hybridauth'))
	);

	$return = array_merge($defaults, $return);

	// Replace default LinkedIn lib
	$return['providers']['LinkedIn']['wrapper'] = array(
		'path' => elgg_get_plugins_path() . 'elgg_hybridauth/deps/Hybrid/Providers/LinkedIn.php',
		'class' => 'Hybrid_Providers_LinkedIn'
	);

	// This was included into the vendor lib and composer update removes it
	$return['providers']['Instagram']['wrapper'] = array(
		'path' => elgg_get_plugins_path() . '/elgg_hybridauth/vendor/hybridauth/hybridauth/additional-providers/hybridauth-instagram/Providers/Instagram.php',
		'class' => 'Hybrid_Providers_Instagram',
	);

	return $return;
}

/**
 * Add an additional provider to the list of providers the user is authenticated with
 *
 * @param string   $event "login"
 * @param string   $type  "user"
 * @param ElggUser $user  User entity
 * @return boolean
 */
function elgg_hybridauth_aux_provider($event, $type, $user) {

	$aux_provider = get_input('aux_provider');
	$aux_provider_uid = get_input('aux_provider_uid');

	if ($aux_provider && $aux_provider_uid) {
		$ha_session = new \Elgg\HybridAuth\Session($user);
		$provider = $ha_session->getProvider($aux_provider);
		if ($provider) {
			$ha_session->addAuthRecord($provider, $aux_provider_uid);
			system_message(elgg_echo('hybridauth:link:provider', array($provider->getName())));
		}
	}

	return true;
}

/**
 * Authenticate all providers the user has previously authenticated with
 * This callback is not currently in use. It's added here for illustration purposes
 *
 * @param string   $event "login"
 * @param string   $type  "user"
 * @param ElggUser $user  User entity
 * @return boolean
 */
function elgg_hybridauth_authenticate_all_providers($event, $type, $user) {

	$ha_session = new \Elgg\HybridAuth\Session($user);
	$providers = $ha_session->getProviders();

	foreach ($providers as $provider) {

		if (!$provider->isEnabled()) {
			continue;
		}

		if (!$ha_session->isAuthenticated($provider) || $ha_session->isConnected($provider)) {
			continue;
		}

		if (!$ha_session->authenticate($provider, false)) {
			register_error(elgg_echo('hybridauth:unlink:provider', array($provider)));
			$ha_session->removeAuthRecord($provider);
		}
	}

	return true;
}

/**
 * Upgrade scripts go here
 * @return type
 */
function elgg_hybridauth_upgrade() {
	if (!elgg_is_admin_logged_in()) {
		return;
	}

	if (!elgg_get_plugin_setting('__upgrade_20150530', 'elgg_hybridauth')) {

		$providers = unserialize(elgg_get_plugin_setting('providers', 'elgg_hybridauth'));

		// Linked upgrade is breaking things
		if (empty($providers['LinkedIn']['scope'])) {
			$providers['LinkedIn']['scope'] = 'r_basicprofile+w_share+rw_company_admin';
		}

		elgg_set_plugin_setting('__upgrade_20150530', true, 'elgg_hybridauth');
		elgg_set_plugin_setting('providers', serialize($providers), 'elgg_hybridauth');
	}
}
