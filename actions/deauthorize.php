<?php

$provider = get_input('provider');
$guid = get_input('guid');
$user = get_entity($guid);

if (!$provider || !$user) {
	forward('', '404');
}

$ha = new ElggHybridAuth();

try {
	$adapter = $ha->getAdapter($provider);
	if ($adapter->isUserConnected()) {
		$adapter->logout();
	}
	elgg_unset_plugin_user_setting("$provider:uid", $user->guid, 'elgg_hybridauth');
	system_message(elgg_echo('hybridauth:provider:user:deauthorized'));
} catch (Exception $e) {
	register_error($e->getMessage());
}

forward(REFERER);