<?php

$ha_provider = $provider = get_input('provider');
if (get_input('openid')) {
	$ha_provider = 'OpenID';
}

$guid = get_input('guid');
$user = get_entity($guid);

if (!$provider || !$user) {
	forward('', '404');
}

$ha = new ElggHybridAuth();

try {
	$adapter = $ha->getAdapter($ha_provider);
	if ($adapter->isUserConnected()) {
		$adapter->logout();
	}
	elgg_unset_plugin_user_setting("$provider:uid", $user->guid, 'elgg_hybridauth');
	elgg_trigger_plugin_hook('hybridauth:deauthenticate', $provider, array('entity' => $user));
	system_message(elgg_echo('hybridauth:provider:user:deauthorized'));
} catch (Exception $e) {
	register_error($e->getMessage());
}

forward(REFERER);
