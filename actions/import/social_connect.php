<?php

set_time_limit(0);

$providers = unserialize(elgg_get_plugin_setting('providers', 'elgg_hybridauth'));

foreach ($providers as $provider => $settings) {
	// Let's check to see if social_connect was used previoiusly to authenticate users
	$options = array(
		'type' => 'user',
		'plugin_id' => 'social_connect',
		'plugin_user_setting_names' => array("{$provider}/uid"),
		'limit' => 0,
	);

	$users = new ElggBatch('elgg_get_entities_from_plugin_user_settings', $options);

	if ($users) {
		foreach ($users as $user) {

			$uid = elgg_get_plugin_user_setting("{$provider}/uid", $user->guid, 'social_connect');

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
			elgg_unset_plugin_user_setting("{$provider}/uid", $user->guid, 'social_connect');
			elgg_set_plugin_user_setting("social_connect_{$provider}/uid", $uid, 'elgg_hybridauth');

			$i++;
		}
	}
}

system_message(elgg_echo('hybridauth:admin:social_connect:action', array($i)));
forward(REFERER);

