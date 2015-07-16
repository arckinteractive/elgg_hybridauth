<?php

if (!elgg_get_plugin_setting('public_auth', 'elgg_hybridauth')) {
	return;
}

$ha_session = new \Elgg\HybridAuth\Session($user);
$providers = $ha_session->getEnabledProviders();

if (empty($providers)) {
	return;
}

elgg_load_css('hybridauth.css');

echo '<div class="hybridauth-form">';
echo '<label class="hybridauth-form-label">' . elgg_echo('hybridauth:connect') . '</label>';
echo '<ul class="hybridauth-form-icons">';

foreach ($providers as $provider) {
	$name = $provider->getName();
	echo '<li>';
	echo elgg_view('output/url', array(
		'text' => elgg_view_icon(strtolower("auth-$name-large")),
		'href' => "hybridauth/authenticate?provider=$name",
		'title' => $name,
		'class' => 'hybridauth-start-authentication'
	));
	echo '</li>';
}

echo '</ul>';
echo '</div>';
