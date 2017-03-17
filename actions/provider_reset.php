<?php

$ha_session = new Elgg\HybridAuth\Session();

$provider = $ha_session->getProvider(get_input('provider'));

if (!$provider) {
	register_error(elgg_echo('elgg_hybridauth:error:invalid:provider'));
	forward(REFERER);
}

$provider_name = sanitize_string($provider->getName());

$dbprefix = elgg_get_config('dbprefix');
delete_data("DELETE FROM {$dbprefix}private_settings WHERE name LIKE 'plugin:user_setting:elgg_hybridauth:{$provider_name}:%'");

system_message(elgg_echo('elgg_hybridauth:success:provider_reset'));
forward(REFERER);