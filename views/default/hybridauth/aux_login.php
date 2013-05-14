<?php

$username = elgg_extract('username', $vars);
$aux_provider = elgg_extract('provider', $vars);
$aux_provider_uid = elgg_extract('provider_uid', $vars);

$providers = unserialize(elgg_get_plugin_setting('providers', 'elgg_hybridauth'));

foreach ($providers as $provider => $settings) {

	if ($settings['enabled']) {

		$adapter = false;

		$ha = new ElggHybridAuth();

		try {
			$adapter = $ha->getAdapter($provider);
		} catch (Exception $e) {
			// do nothing
		}

		if ($adapter) {
			$users = get_user_by_email($username);
			if ($users && elgg_get_plugin_user_setting("$provider:uid", $users[0]->guid, 'elgg_hybridauth')) {
				$active[] = $provider;
			}
		}
	}
}

if ($active) {

	elgg_load_css('hybridauth.css');

	echo '<div class="hybridauth-form">';
	echo '<label class="hybridauth-form-label">' . elgg_echo('hybridauth:connect') . '</label>';
	echo '<ul class="hybridauth-form-icons">';
	foreach ($active as $provider) {
		echo '<li>';
		echo elgg_view('output/url', array(
			'text' => elgg_view_icon(strtolower("auth-$provider-large")),
			'href' => "hybridauth/authenticate?provider=$provider&aux_provider=$aux_provider&aux_provider_uid=$aux_provider_uid",
			'title' => $provider,
			'class' => 'hybridauth-start-authentication'
		));
		echo '</li>';
	}
	echo '</ul>';
	echo '</div>';
}
