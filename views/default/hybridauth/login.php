<?php

if (!HYBRIDAUTH_PUBLIC_AUTH) {
	return;
}

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
			$active[] = $provider;
		}
	}
}

if ($active) {

	elgg_load_css('hybridauth.css');

	echo '<div class="hybridauth-form">';
	echo '<label class="hybridauth-form-label">' . elgg_echo('hybridauth:connect') . '</label>';
	echo '<ul class="hybridauth-form-icons">';
	foreach ($active as $provider) {
		if ($provider == 'OpenID') {
			$openid_settings = elgg_get_plugin_setting('openid_providers', 'elgg_hybridauth');
			$openid_providers = ($openid_settings) ? unserialize($openid_settings) : array();

			foreach ($openid_providers as $openid_provider) {
				$provider = elgg_extract('name', $openid_provider);
				$identifier = elgg_extract('identifier', $openid_provider);
				if (!$provider || !$identifier) {
					continue;
				}
				echo '<li>';
				echo elgg_view('output/url', array(
					'text' => elgg_view_icon('auth-openid-large ' . strtolower("openid-$name-large")),
					'href' => elgg_http_add_url_query_elements("hybridauth/authenticate", array(
						'openid' => true,
						'provider' => $provider,
						'identifier' => urlencode($identifier),
					)),
					'title' => $provider,
					'class' => 'hybridauth-start-authentication'
				));
				echo '</li>';
			}
		} else {
			echo '<li>';
			echo elgg_view('output/url', array(
				'text' => elgg_view_icon(strtolower("auth-$provider-large")),
				'href' => "hybridauth/authenticate?provider=$provider",
				'title' => $provider,
				'class' => 'hybridauth-start-authentication'
			));
			echo '</li>';
		}
	}

	echo '</ul>';
	echo '</div>';
}
