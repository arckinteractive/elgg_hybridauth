<?php

elgg_load_css('hybridauth.css');

$user = elgg_get_page_owner_entity();

$ha_session = new \Elgg\HybridAuth\Session($user);
$providers = $ha_session->getProviders();

foreach ($providers as $ha_provider) {

	if (!$ha_provider->isEnabled()) {
		continue;
	}

	$provider = $ha_provider->getName();
	$openid = $ha_provider->getOpenId();

	$icon_classes = array(strtolower("auth-$provider"));
	if ($openid) {
		$icon_classes[] = "auth-openid";
	}

	$title = elgg_view_image_block(elgg_view_icon(implode(' ', $icon_classes)), $provider);

	if ($ha_session->isAuthenticated($ha_provider)) {

		$deauth_url = elgg_http_add_url_query_elements("action/hybridauth/deauthorize", array(
			'provider' => $provider,
			'guid' => $user->guid,
		));

		$mod = '<p class="hybridauth-diagnostics-success">' . elgg_echo('hybridauth:provider:user:authenticated') . '</p>';
		$mod .= elgg_view('output/url', array(
			'href' => $deauth_url,
			'is_action' => true,
			'text' => elgg_echo('hybridauth:provider:user:deauthorize'),
			'class' => 'elgg-button elgg-button-action'
		));
		$mod .= elgg_view("hybridauth/accounts/$provider");

	} else {

		$auth_url = elgg_http_add_url_query_elements('hybridauth/authenticate', array(
			'provider' => $provider,
			'elgg_forward_url' => urlencode(elgg_normalize_url("hybridauth/accounts/$user->username")),
		));
		
		$mod = elgg_view('output/url', array(
			'href' => $auth_url,
			'text' => elgg_echo('hybridauth:provider:user:authenticate'),
			'class' => 'elgg-button elgg-button-action'
		));
	}
	echo elgg_view_module('info', $title, $mod);
}