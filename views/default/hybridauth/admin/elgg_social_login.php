<?php

// Let's check to see if elgg_social_login was used previoiusly to authenticate users
$options = array(
	'type' => 'user',
	'plugin_id' => 'elgg_social_login',
	'plugin_user_setting_names' => array('uid'),
	'count' => true
);

$count = elgg_get_entities_from_plugin_user_settings($options);

if (!$count)
	return;

$title = elgg_echo('hybridauth:admin:elgg_social_login');

$body = '<div class="mam">' . elgg_echo('hybridauth:admin:elgg_social_login:count', array($count)) . '</div>';

$body .= elgg_view('output/url', array(
	'text' => elgg_echo('import'),
	'href' => 'action/hybridauth/import/elgg_social_login',
	'is_action' => true,
	'class' => 'elgg-button elgg-button-action float-alt mam'
));

echo elgg_view_module('widget', $title, $body);