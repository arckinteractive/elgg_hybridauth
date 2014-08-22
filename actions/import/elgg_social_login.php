<?php

set_time_limit(0);

$options = array(
	'type' => 'user',
	'plugin_id' => 'elgg_social_login',
	'plugin_user_setting_names' => array('uid'),
	'limit' => 0
);

$users = new ElggBatch('elgg_get_entities_from_plugin_user_settings', $options);

foreach ($users as $user) {

	$setting = elgg_get_plugin_user_setting('uid', $user->guid, 'elgg_social_login');

	list($provider, $uid) = explode('_', $setting);

	// Check to see if another record has been created with elgg_hybridauth
	$elgg_hybridauth_options = array(
		'type' => 'user',
		'plugin_id' => 'elgg_hybridauth',
		'plugin_user_setting_name_value_pairs' => array(
			"$provider:uid" => $uid
		),
		'limit' => 0
	);
	$elgg_hybridauth_users = elgg_get_entities_from_plugin_user_settings($elgg_hybridauth_options);

	if ($elgg_hybridauth_users) {

		$elgg_hybridauth_user = $elgg_hybridauth_users[0];

		if ($user->time_created < $elgg_hybridauth_user->time_created) {

			// elgg_social_login user was created earlier, so give that user the ability to login in with this provider uid
			elgg_unset_plugin_user_setting("$provider:uid", $elgg_hybridauth_user->guid, 'elgg_hybridauth');
		}

	} else {
		elgg_set_plugin_user_setting("$provider:uid", $uid, $user->guid, 'elgg_hybridauth');
	}

	// keep a backup record
	elgg_unset_plugin_user_setting('uid', $user->guid, 'elgg_social_login');
	elgg_set_plugin_user_setting('elgg_social_login_uid', "{$provider}_{$uid}", 'elgg_hybridauth');

	$i++;
}

system_message(elgg_echo('hybridauth:admin:elgg_social_login:action', array($i)));
forward(REFERER);

