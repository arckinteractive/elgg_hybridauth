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
elgg_require_js("hybridauth/login");

echo '<div class="hybridauth-form">';
echo '<label class="hybridauth-form-label">' . elgg_echo('hybridauth:connect') . '</label>';
echo '<ul class="hybridauth-form-icons">';

foreach ($providers as $provider) {
	$name = $provider->getName();
	
	$url = elgg_normalize_url("hybridauth/authenticate");
	$elements = [
		'provider' => $name
	];
	if (get_input('friend_guid')) {
		$elements['friend_guid'] = get_input('friend_guid');
	}
	if (get_input('invitecode')) {
		$elements['invitecode'] = get_input('invitecode');
	}
	
	echo '<li>';
	echo elgg_view('output/url', array(
		'text' => elgg_view_icon(strtolower("auth-$name-large")),
		'href' => elgg_http_add_url_query_elements($url, $elements),
		'title' => $name,
		'class' => 'hybridauth-start-authentication'
	));
	echo '</li>';
}

echo '</ul>';
echo '</div>';
