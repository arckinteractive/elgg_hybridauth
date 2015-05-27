<?php

elgg_load_css('hybridauth.css');

$user = elgg_get_page_owner_entity();

$client = new \Elgg\HybridAuth\Client();
$providers = $client->getProviders();

foreach ($providers as $provider) {

	if (!$provider->isEnabled()) {
		continue;
	}

	$openid = $provider->getOpenId();

	if ($openid) {

		$title = elgg_view_image_block(elgg_view_icon('auth-openid ' . strtolower("auth-$provider")), $provider);

		if ($provider->isAuthenticated($user)) {
			$mod = '<p class="hybridauth-diagnostics-success">' . elgg_echo('hybridauth:provider:user:authenticated') . '</p>';
			$mod .= elgg_view('output/url', array(
				'href' => elgg_http_add_url_query_elements("action/hybridauth/deauthorize", array(
					'openid' => true,
					'provider' => "$provider",
					'guid' => $user->guid,
				)),
				'is_action' => true,
				'text' => elgg_echo('hybridauth:provider:user:deauthorize'),
				'class' => 'elgg-button elgg-button-action'
			));

			$mod .= elgg_view("hybridauth/accounts/$provider");
		} else {
			$forward_url = urlencode(elgg_normalize_url("hybridauth/accounts/$user->username"));
			$mod = elgg_view('output/url', array(
				'href' => elgg_http_add_url_query_elements("hybridauth/authenticate", array(
					'provider' => "$provider",
					'elgg_forward_url' => $forward_url,
				)),
				'text' => elgg_echo('hybridauth:provider:user:authenticate'),
				'class' => 'elgg-button elgg-button-action'
			));
		}
		echo elgg_view_module('info', $title, $mod);
	} else {

		$title = elgg_view_image_block(elgg_view_icon(strtolower("auth-$provider")), $provider);

		if ($provider->isAuthenticated($user)) {
			$mod = '<p class="hybridauth-diagnostics-success">' . elgg_echo('hybridauth:provider:user:authenticated') . '</p>';
			$mod .= elgg_view('output/url', array(
				'href' => "action/hybridauth/deauthorize?provider=$provider&guid=$user->guid",
				'is_action' => true,
				'text' => elgg_echo('hybridauth:provider:user:deauthorize'),
				'class' => 'elgg-button elgg-button-action'
			));

			$mod .= elgg_view("hybridauth/accounts/$provider");
		} else {
			$forward_url = urlencode(elgg_normalize_url("hybridauth/accounts/$user->username"));
			$mod = elgg_view('output/url', array(
				'href' => "hybridauth/authenticate?provider=$provider&elgg_forward_url=$forward_url",
				'text' => elgg_echo('hybridauth:provider:user:authenticate'),
				'class' => 'elgg-button elgg-button-action'
			));
		}
		echo elgg_view_module('info', $title, $mod);
	}
}