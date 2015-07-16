<?php

$username = elgg_extract('username', $vars);
$users = get_user_by_email($username);
if (empty($users)) {
	return;
}

$user = $users[0];

$aux_provider = elgg_extract('provider', $vars);
$aux_provider_uid = elgg_extract('provider_uid', $vars);

$active = array();

$ha_session = new \Elgg\HybridAuth\Session($user);
$providers = $ha_session->getEnabledProviders();

foreach ($providers as $provider) {
	if ($ha_session->isAuthenticated($provider)) {
		$active[] = $provider->getName();
	}
}

if (empty($active)) {
	return;
}

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
