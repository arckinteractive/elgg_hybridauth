<?php

/**
 * Elgg HybridAuth
 */
// Composer autoload
require_once __DIR__ . '/vendor/autoload.php';

elgg_register_event_handler('init', 'system', 'elgg_hybridauth_init');

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

	elgg_register_event_handler('login', 'user', 'elgg_hybridauth_aux_provider');
	//elgg_register_event_handler('login', 'user', 'elgg_hybridauth_authenticate_all_providers');

	elgg_register_menu_item('page', array(
		'name' => 'hybridauth:accounts',
		'text' => elgg_echo('hybridauth:accounts'),
		'href' => "hybridauth/accounts",
		'selected' => (elgg_in_context('hybridauth')),
		'contexts' => array('settings'),
	));

	elgg_register_plugin_hook_handler('hybridauth:authenticate', 'all', 'simplur_register_store_hybridauth_session');
	elgg_register_plugin_hook_handler('hybridauth:deauthenticate', 'all', 'simplur_register_store_hybridauth_session');
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
		elgg_set_plugin_user_setting("$aux_provider:uid", $aux_provider_uid, $user->guid, 'elgg_hybridauth');
		elgg_trigger_plugin_hook('hybridauth:authenticate', $aux_provider, array('entity' => $user));
		system_message(elgg_echo('hybridauth:link:provider', array($aux_provider)));
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

	$providers = unserialize(elgg_get_plugin_setting('providers', 'elgg_hybridauth'));

	foreach ($providers as $provider => $settings) {

		if ($settings['enabled']) {

			$adapter = false;

			$ha = new ElggHybridAuth();

			try {
				$adapter = $ha->getAdapter($provider);
			} catch (Exception $e) {
				// do nothing
			}

			if ($adapter) {
				if (elgg_get_plugin_user_setting("$provider:uid", $user->guid, 'elgg_hybridauth')) {
					try {
						$ha->authenticate($provider);
					} catch (Exception $e) {
						register_error($e->getMessage());
						register_error(elgg_echo('hybridauth:unlink:provider', array($provider)));
						elgg_unset_plugin_user_setting("$provider:uid", $user->guid, 'elgg_hybridauth');
					}
				}
			}
		}
	}

	return true;
}

/**
 * Store hybridauth session information, so that it can be restored at a later point
 *
 * @param string $hook     "hybridauth:authenticate",
 * @param string $provider "all"
 * @param mixed  $return   No return expected
 * @param array  $params   Hook params
 * @return mixed
 */
function simplur_register_store_hybridauth_session($hook, $provider, $return, $params) {

	if (!elgg_get_plugin_setting('persistent_session', 'elgg_hybridauth')) {
		return $return;
	}

	$entity = elgg_extract('entity', $params);

	try {

		$ha = new ElggHybridAuth();
		$hybridauth_session_data = $ha->getSessionData();
		elgg_set_plugin_user_setting('hybridauth_session_data', $hybridauth_session_data, $entity->guid, 'elgg_hybridauth');
		elgg_set_plugin_user_setting('hybridauth_session_id', session_id(), $entity->guid, 'elgg_hybridauth');
	} catch (Exception $e) {
		// error_log($e->getMessage());
		// Something is wrong, but whatever
	}

	return $return;
}
