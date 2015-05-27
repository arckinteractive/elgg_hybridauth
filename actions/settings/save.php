<?php

$plugin = elgg_get_plugin_from_id('elgg_hybridauth');
$plugin_name = $plugin->getManifest()->getName();

$plugin->setSetting('debug_mode', get_input('debug_mode', false));
$plugin->setSetting('providers', serialize(get_input('providers', array())));
$plugin->setSetting('openid_providers', serialize(get_input('openid_providers', array())));
$plugin->setSetting('email_credentials', get_input('email_credentials', 'yes'));
$plugin->setSetting('registration_instructions', get_input('registration_instructions'));
$plugin->setSetting('public_auth', get_input('public_auth', true));
$plugin->setSetting('persistent_session', get_input('persistent_session', false));

system_message(elgg_echo('plugins:settings:save:ok', array($plugin_name)));
forward(REFERER);