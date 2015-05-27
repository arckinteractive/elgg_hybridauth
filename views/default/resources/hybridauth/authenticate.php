<?php

if (!elgg_get_plugin_setting('public_auth', 'elgg_hybridauth')) {
	gatekeeper();
}

$ha_provider = $provider = get_input('provider');

if (!$ha_provider) {
	forward('', '404');
}

try {
	$ha_providers = unserialize(elgg_get_plugin_setting('providers', 'elgg_hybridauth'));
	$openid_settings = elgg_get_plugin_setting('openid_providers', 'elgg_hybridauth');
	$openid_providers = ($openid_settings) ? unserialize($openid_settings) : array();

	if (!array_key_exists($ha_provider, $ha_providers)) {
		foreach ($openid_providers as $openid_provider) {
			if ($openid_provider['name'] == $provider) {
				$openid = true;
				set_input('openid', true);
				set_input('identifier', urlencode($openid_provider['identifier']));
			}
		}
		if (!$openid) {
			throw new Exception("Provider $provider is not known");
		}
	}

	$ha = (new \Elgg\HybridAuth\Client())->client();
} catch (Exception $e) { // let's catch endpoint exceptions as they are thrown in the constructor
	$title = elgg_echo('error:default');
	$content = $e->getMessage();
	$layout = elgg_view_layout('error', array(
		'title' => $title,
		'content' => $content
	));
	echo elgg_view_page($title, $layout, 'error');
	return;
}

try {
	
	if (get_input('openid')) {
		$ha_provider = 'OpenID';
		$adapter = $ha->authenticate('OpenID', array(
			'openid_identifier' => urldecode(get_input('identifier', ''))
		));
	} else {
		$adapter = $ha->authenticate($ha_provider);
	}

	$profile = $adapter->getUserProfile();
} catch (Exception $e) { // catching authentication exceptions
	// user is likely to have revoked the privileges, while we still have session data stored
	// let's clear the session and try to reauthenticate
	$adapter = $ha->getAdapter($ha_provider);
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
	return;
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
		return;
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

	// User already has an account, save the token and login
	$user_to_login = $users[0];
	elgg_set_plugin_user_setting("$provider:uid", $profile->identifier, $user_to_login->guid, 'elgg_hybridauth');
	elgg_trigger_plugin_hook('hybridauth:authenticate', $provider, array('entity' => $user_to_login));
	$user_to_login->elgg_hybridauth_login = 1;
	login($user_to_login);
	system_message(elgg_echo('hybridauth:login:provider', array($provider)));
	forward();
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
