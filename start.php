<?php

define('HYBRIDAUTH_PUBLIC_AUTH', elgg_get_plugin_setting('public_auth', 'elgg_hybridauth'));

/**
 * Elgg HybridAuth
 */
elgg_register_event_handler('init', 'system', 'elgg_hybridauth_init');

/**
 * Initialize the plugin
 */
function elgg_hybridauth_init() {

	elgg_register_class('Hybrid_Auth', elgg_get_plugins_path() . 'elgg_hybridauth/classes/Hybrid/Auth.php');
	elgg_register_class('Hybrid_Endpoint', elgg_get_plugins_path() . 'elgg_hybridauth/classes/Hybrid/Endpoint.php');
	elgg_register_class('ElggHybridAuth', elgg_get_plugins_path() . 'elgg_hybridauth/classes/ElggHybridAuth.php');

	elgg_register_page_handler('hybridauth', 'elgg_hybridauth_page_handler');

	elgg_register_action('elgg_hybridauth/settings/save', elgg_get_plugins_path() . 'elgg_hybridauth/actions/settings/save.php', 'admin');
	elgg_register_action('hybridauth/register', elgg_get_plugins_path() . 'elgg_hybridauth/actions/register.php', 'public');
	elgg_register_action('hybridauth/deauthorize', elgg_get_plugins_path() . 'elgg_hybridauth/actions/deauthorize.php');
	elgg_register_action('hybridauth/import/elgg_social_login', elgg_get_plugins_path() . 'elgg_hybridauth/actions/import/elgg_social_login.php', 'admin');
	elgg_register_action('hybridauth/import/social_connect', elgg_get_plugins_path() . 'elgg_hybridauth/actions/import/social_connect.php', 'admin');

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
}

/**
 * Page handler callback for /hybridauth
 * Used an auth start and endpoint
 *
 * To authenticate a given provider, use the following URL structure
 * /hybridauth/authenticate?provider=$provider
 *
 * If you are authenticating a provider for a logged in user, and would like to
 * forward the user to a specific page upon successful authentication,
 * pass an encoded URL as a 'elgg_forward_url' URL query parameter, e.g.
 * /hybridauth/authenticate?provider=$provider&elgg_forward_to=$url.
 * This can be helpful if you are implementing an import or sharing tool:
 * you can first check if the user is authenticated with a given provider
 * and then use this handler to avoid duplicating the authentication logic
 *
 * @param type $page
 * @return boolean
 */
function elgg_hybridauth_page_handler($page) {

	if (!HYBRIDAUTH_PUBLIC_AUTH) {
		gatekeeper();
	}
	
	$action = elgg_extract(0, $page);

	if (!isset($_SESSION['hybridauth'])) {
		$_SESSION['hybridauth'] = array(
			'friend_guid' => get_input('friend_guid'),
			'invitecode' => get_input('invitecode')
		);
	}

	switch ($action) {

		case 'authenticate' :
			$provider = get_input('provider');

			if (!$provider) {
				return false;
			}

			try {
				$ha = new ElggHybridAuth();
			} catch (Exception $e) { // let's catch endpoint exceptions as they are thrown in the constructor
				$title = elgg_echo('error:default');
				$content = $e->getMessage();
				$layout = elgg_view_layout('error', array(
					'title' => $title,
					'content' => $content
				));
				echo elgg_view_page($title, $layout, 'error');
				return true;
			}

			try {
				$adapter = $ha->authenticate($provider);
				$profile = $adapter->getUserProfile();
			} catch (Exception $e) { // catching authentication exceptions
				// user is likely to have revoked the privileges, while we still have session data stored
				// let's clear the session and try to reauthenticate
				$adapter = $ha->getAdapter($provider);
				if ($adapter->isUserConnected()) {
					$adapter->logout();
					header("Location: " . $_SERVER['REQUEST_URI']);
				}
				$title = elgg_echo('error:default');
				$content = $e->getMessage();
				$layout = elgg_view_layout('error', array(
					'title' => $title,
					'content' => $content
				));
				echo elgg_view_page($title, $layout, 'error');
				return true;
			}

			// Does this user profile exist?
			$options = array(
				'type' => 'user',
				'plugin_id' => 'elgg_hybridauth',
				'plugin_user_setting_name_value_pairs' => array(
					'name' => "$provider:uid",
					'value' => $profile->identifier
				),
				'limit' => 0
			);

			$users = elgg_get_entities_from_plugin_user_settings($options);

			if (elgg_is_logged_in()) {
				$logged_in = elgg_get_logged_in_user_entity();
				if (!$users || $users[0]->guid == $logged_in->guid) {
					// User already has an account
					// Linking provider profile to an existing account
					elgg_set_plugin_user_setting("$provider:uid", $profile->identifier, $logged_in->guid, 'elgg_hybridauth');
					elgg_trigger_plugin_hook('hybridauth:authenticate', $provider, array('entity' => $logged_in));
					system_message(elgg_echo('hybridauth:link:provider', array($provider)));

					if ($elgg_forward_url = get_input('elgg_forward_url')) {
						forward(urldecode($elgg_forward_url));
					} else {
						$query = parse_url(current_page_url(), PHP_URL_QUERY);
						forward("settings/user/" . elgg_get_logged_in_user_entity()->username . '?' . $query);
					}
				} else {
					// Another user has already linked this profile
					$adapter->logout();
					$title = elgg_echo('error:default');
					$content = elgg_echo('hybridauth:link:provider:error', array($provider));
					$layout = elgg_view_layout('error', array(
						'title' => $title,
						'content' => $content
					));
					echo elgg_view_page($title, $layout, 'error');
					return true;
				}
			}

			if (!$users) {
				// try one more time to match a user with plugin setting
				$testusers = get_user_by_email($profile->email);
				foreach ($testusers as $t) {
					$users = array();
					if ($profile->identifier == elgg_get_plugin_user_setting("$provider:uid", $t->guid, 'elgg_hybridauth')) {
						// they do have an account, but for some reason egef_plugin_settings didn't work...
						// we've had a few cases of it
						$users[] = $t;
					}
				}
			}


			if ($users) {
				if (count($users) > 1) {
					// find the user that was created first
					foreach ($users as $u) {
						if (empty($user_to_login) || $u->time_created < $user_to_login->time_created) {
							$user_to_login = $u;
						}
					}
				} else if (count($users) == 1) {
					$user_to_login = $users[0];
				}

				// Profile for this provider exists
				if (!elgg_is_logged_in()) {
					$user_to_login->elgg_hybridauth_login = 1;
					login($user_to_login);
					system_message(elgg_echo('hybridauth:login:provider', array($provider)));
					forward();
				}
			}

			// Let's see what data we have received from the provider and request the user to complete the registration process
			elgg_push_context('register');

			if ($profile->emailVerified) {
				$email = $profile->emailVerified;
			} else if ($profile->email) {
				$email = $profile->email;
			} else if (get_input('email')) {
				$email = urldecode(get_input('email'));
			}

			if ($email && $users = get_user_by_email($email)) {

				$title = elgg_echo('hybridauth:login');
				$content = elgg_view_form('hybridauth/login', array(
					'action' => 'action/login'
						), array(
					'username' => $email,
					'provider' => $provider,
					'provider_uid' => $profile->identifier
				));
			} else {

				$title = elgg_echo('hybridauth:register');
				$content = elgg_view_form('hybridauth/register', array(), array(
					'provider' => $provider,
					'profile' => $profile,
					'invitecode' => $_SESSION['hybridauth']['invitecode'],
					'friend_guid' => $_SESSION['hybridauth']['friend_guid']
				));
			}

			$layout = elgg_view_layout('one_column', array(
				'title' => $title,
				'content' => $content
			));

			echo elgg_view_page($title, $layout);

			return true;
			break;

		case 'endpoint' :
			Hybrid_Endpoint::process();
			break;

		case 'accounts' :
			
			gatekeeper();
			
			$username = $page[1];
			$user = get_user_by_username($username);
			
			if (!elgg_instanceof($user, 'user')) {
				$user = elgg_get_logged_in_user_entity();
				if ($user->username !== $username) {
					forward("hybridauth/accounts/$user->username");
				}
			}
			
			if (!$user->canEdit()) {
				return false;
			}
			
			elgg_set_page_owner_guid($user->guid);
			
			elgg_set_context('settings');
			
			$title = elgg_echo('hybridauth:accounts');
			$content = elgg_view('hybridauth/accounts');

			$layout = elgg_view_layout('content', array(
				'title' => $title,
				'content' => $content,
				'filter' => false
			));

			echo elgg_view_page($title, $layout);
			return true;
			break;
}


	return false;
}

/**
 * Add hybridauth to allowed walled garden pages
 */
function elgg_hybridauth_public_pages($hook, $type, $return, $params) {

	$return[] = 'hybridauth/.*';
	$return[] = 'action/hybridauth/.*';
	return $return;
}

/**
 * Add an additional provider to the authenticated providers list
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
