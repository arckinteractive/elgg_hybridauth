<?php

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
		echo '<li>';
		echo elgg_view('output/url', array(
			'text' => elgg_view_icon(strtolower("auth-$provider-large")),
			'href' => "hybridauth/authenticate?provider=$provider",
			'title' => $provider,
			'class' => 'hybridauth-start-authentication'
		));
		echo '</li>';
	}
	echo '</ul>';
	echo '</div>';
}
