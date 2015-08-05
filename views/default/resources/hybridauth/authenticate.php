<?php

if (!elgg_get_plugin_setting('public_auth', 'elgg_hybridauth')) {
	gatekeeper();
}

$session_owner_guid = get_input('session_owner_guid');
$session_owner = get_entity($session_owner_guid);
$session_name = get_input('session_name');
$session_handle = get_input('session_handle');

$user = ($session_owner) ? : elgg_get_logged_in_user_entity();

$ha_session = new Elgg\HybridAuth\Session($user, $session_name, $session_handle);

$provider_name = get_input('provider');
$provider = $ha_session->getProvider($provider_name);

if (!$provider) {
	forward(REFERRER, '400'); // bad request
}

$scope = get_input('scope');
if ($scope) {
	$uid = (int) $ha_session->isAuthenticated($provider);

	if (empty($_SESSION["HA:$provider_name:last_scope:$uid"])) {
		$_SESSION["HA:$provider_name:last_scope:$uid"] = 'default';
	}

	if ($_SESSION["HA:$provider_name:last_scope:$uid"] != $scope) {
		// Check if scope has been explicitly required
		// Logout the user, and reauthenticate with the requested scope
		$config = $ha_session->getConfig();
		if (!empty($config['providers'][$provider_name])) {
			$config['providers'][$provider_name]['scope'] = urldecode($scope);
		}
		$ha_session->setConfig($config);
		$ha_session->getAdapter($provider)->logout();
		$ha_session->save();
		$_SESSION["HA:$provider_name:last_scope:$uid"] = $scope;
	}
	else {
		$_SESSION["HA:$provider_name:last_scope:$uid"] = 'default';
	}
}

$save_auth = $user ? true : false;
$profile = $ha_session->authenticate($provider, $save_auth);
if (!$profile) {
	echo elgg_view('resources/hybridauth/error', array(
		'provider' => $provider->getName(),
		'error' => get_input('error'),
	));
	return;
}

$elgg_forward_url = get_input('elgg_forward_url', $_SESSION['last_forward_from']);
if ($elgg_forward_url) {
	$forward_url = urldecode($elgg_forward_url);
} else {
	$query = parse_url(current_page_url(), PHP_URL_QUERY);
	if ($user) {
		$forward_url = "settings/user/{$user->username}?{$query}";
	} else {
		$forward_url = "?{$query}";
	}
}

if ($session_handle && $session_handle != \Elgg\HybridAuth\Session::DEFAULT_HANDLE) {
	forward($forward_url);
}

// Does this user profile exist?
$options = array(
	'type' => 'user',
	'plugin_id' => 'elgg_hybridauth',
	'plugin_user_setting_name_value_pairs' => array(
		'name' => $ha_session->getAuthRecordName($provider),
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
		$ha_session->addAuthRecord($provider, $profile);
		system_message(elgg_echo('hybridauth:link:provider', array($provider->getName())));
		forward($forward_url);
	} else {
		// Another user has already linked this profile
		$ha_session->deauthenticate($provider, false);
		echo elgg_view('resources/hybridauth/error', array(
			'provider' => $provider->getName(),
			'error' => elgg_echo('hybridauth:link:provider:error', array($provider->getName())),
			'retry' => false,
		));
		return;
	}
}

if (!$users) {
	// try one more time to match a user with plugin setting
	$testusers = get_user_by_email($profile->email);
	foreach ($testusers as $t) {
		$users = array();
		if ($profile->identifier == elgg_get_plugin_user_setting($ha_session->getAuthRecordName($provider), $t->guid, 'elgg_hybridauth')) {
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
		system_message(elgg_echo('hybridauth:login:provider', array($provider->getName())));
		forward($forward_url);
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

	// User already has an account, save the token and login
	$user_to_login = $users[0];
	$ha_session->setUser($user_to_login);
	$ha_session->addAuthRecord($provider, $profile);

	$user_to_login->elgg_hybridauth_login = 1;
	login($user_to_login);
	system_message(elgg_echo('hybridauth:login:provider', array($provider->getName())));
	forward($forward_url);
} else {

	$title = elgg_echo('hybridauth:register');
	$content = elgg_view_form('hybridauth/register', array(), array(
		'provider' => $provider->getName(),
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
