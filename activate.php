<?php

$providers = elgg_get_plugin_setting('providers', 'elgg_hybridauth');

if (is_null($providers)) {
	$providers = array(
		"OpenID" => array(
			"enabled" => false
		),
		"Yahoo" => array(
			"enabled" => false,
			"keys" => array("key" => "", "secret" => ""),
		),
		"AOL" => array(
			"enabled" => false
		),
		"Google" => array(
			"enabled" => false,
			"keys" => array("id" => "", "secret" => ""),
		),
		"Facebook" => array(
			"enabled" => false,
			"keys" => array("id" => "", "secret" => ""),
		),
                "Instagram" => array (
                        "enabled" => false,
                        "keys"    => array ( "id" => "", "secret" => "" )
                ),
		"Twitter" => array(
			"enabled" => false,
			"keys" => array("key" => "", "secret" => "")
		),
		// windows live
		"Live" => array(
			"enabled" => false,
			"keys" => array("id" => "", "secret" => "")
		),
//		"MySpace" => array(
//			"enabled" => false,
//			"keys" => array("key" => "", "secret" => "")
//		),
		"LinkedIn" => array(
			"enabled" => false,
			"keys" => array("key" => "", "secret" => "")
		),
		"Foursquare" => array(
			"enabled" => false,
			"keys" => array("id" => "", "secret" => "")
		)
	);
} else {

	$providers = unserialize($providers);

	// Update Yahoo! to use 'key' instead of 'id'
	if (!isset($providers['Yahoo']['keys']['key'])) {
		$key = $providers['Yahoo']['keys']['id'];
		$providers['Yahoo']['keys']['key'] = $key;
		unset($providers['Yahoo']['keys']['id']);
	}

	// Remove MySpace from Providers
	if (isset($providers['MySpace'])) {
		unset($providers['MySpace']);
	}
}

elgg_set_plugin_setting('providers', serialize($providers), 'elgg_hybridauth');

elgg_set_plugin_setting('base_url', elgg_normalize_url('hybridauth/endpoint'), 'elgg_hybridauth');
elgg_set_plugin_setting('debug_mode', false, 'elgg_hybridauth');
elgg_set_plugin_setting('debug_file', elgg_get_plugins_path() . 'elgg_hybridauth/debug.info', 'elgg_hybridauth');

if (is_null(elgg_get_plugin_setting('public_auth', 'elgg_hybridauth'))) {
	elgg_set_plugin_setting('public_auth', true, 'elgg_hybridauth');
}
