<?php

elgg_load_css('hybridauth.css');

$diagnostics = array(
	'php_version' => (version_compare(PHP_VERSION, '5.2.0', '>=')),
	'elgg_oauth' => (!class_exists('OAuthException')),
	'curl' => (function_exists('curl_init')),
	'json' => (function_exists('json_decode')),
	'pecl_oauth' => (!extension_loaded('oauth'))
);

foreach ($diagnostics as $requirement => $status) {
	if ($status === false) {
		echo elgg_view('hybridauth/admin/diagnostics', array(
			'diagnostics' => $diagnostics
		));
		return true;
	}
}

echo elgg_view('hybridauth/admin/elgg_social_login');

echo elgg_view('output/longtext', array(
	'value' => elgg_view("hybridauth/setup"),
	'class' => 'hybridauth-setup-instructions'
));

$debug_mode = elgg_get_plugin_setting('debug_mode', 'elgg_hybridauth');
$providers = unserialize(elgg_get_plugin_setting('providers', 'elgg_hybridauth'));

//echo '<div>';
//echo '<label>' . elgg_echo('hybridauth:debug_mode') . '</label>';
//echo elgg_view('input/dropdown', array(
//	'name' => 'debug_mode',
//	'value' => $debug_mode,
//	'options_values' => array(
//		0 => elgg_echo('hybridauth:debug_mode:disable'),
//		1 => elgg_echo('hybridauth:debug_mode:enable')
//	)
//));
//echo '</div>';

foreach ($providers as $provider => $settings) {

	$title = elgg_view_image_block(elgg_view_icon(strtolower("auth-$provider")), $provider);

	$mod = '<div>';
	$mod .= '<label>' . elgg_echo('hybridauth:provider:enable') . '</label>';
	$mod .= elgg_view('input/dropdown', array(
		'name' => "providers[$provider][enabled]",
		'value' => $settings['enabled'],
		'options_values' => array(
			0 => elgg_echo('hybridauth:provider:disabled'),
			1 => elgg_echo('hybridauth:provider:enabled')
		)
			));
	$mod .= '</div>';

	if (isset($settings['keys'])) {

		foreach ($settings['keys'] as $key_name => $key_value) {
			$mod .= '<div>';
			$mod .= '<label>' . elgg_echo("hybridauth:provider:$key_name") . '</label>';
			$mod .= elgg_view('input/text', array(
				'name' => "providers[$provider][keys][$key_name]",
				'value' => $key_value,
					));
			$mod .= '</div>';
		}
	}

	$footer = '';
	if ($settings['enabled']) {

		$ha = new ElggHybridAuth();

		try {
			$ha->getAdapter($provider);
			$footer = '<div class="hybridauth-diagnostics-pass pam">' . elgg_echo('hybridauth:adapter:pass') . '</div>';
		} catch (Exception $e) {
			$footer = '<div class="hybridauth-diagnostics-fail pam">' . $e->getMessage() . '</div>';
		}
	}

	echo elgg_view_module('widget', $title, $mod, array('footer' => $footer, 'class' => 'hybridauth-provider-settings'));
}