<?php

$providers = unserialize(elgg_get_plugin_setting('providers', 'elgg_hybridauth'));

foreach ($providers as $provider => $settings) {
	// Let's check to see if social_connect was used previoiusly to authenticate users
	$options = array(
		'type' => 'user',
		'plugin_id' => 'social_connect',
		'plugin_user_setting_names' => array("{$provider}/uid"),
		'count' => true
	);

	$count += elgg_get_entities_from_plugin_user_settings($options);
}

if (!$count)
	return;

$title = elgg_echo('hybridauth:admin:social_connect');

$body = '<div class="mam">' . elgg_echo('hybridauth:admin:social_connect:count', array($count)) . '</div>';

$body .= elgg_view('output/url', array(
	'text' => elgg_echo('import'),
	'href' => 'action/hybridauth/import/social_connect',
	'is_action' => true,
	'class' => 'elgg-button elgg-button-action float-alt mam'
		));

echo elgg_view_module('widget', $title, $body);