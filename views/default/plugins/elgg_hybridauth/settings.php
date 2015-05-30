<?php

elgg_load_css('hybridauth.css');

$entity = elgg_extract('entity', $vars);

$diagnostics = array(
	'php_version' => (version_compare(PHP_VERSION, '5.2.0', '>=')),
	//'elgg_oauth' => (!class_exists('OAuthException')), // @since 1.3 declared as conflict in plugin manifest
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
	'class' => 'hybridauth-setup-instructions',
	'parse_urls' => false
));

echo '<div>';
echo '<label>' . elgg_echo('hybridauth:debug_mode') . '</label>';
echo elgg_view('input/dropdown', array(
	'name' => 'debug_mode',
	'value' => $entity->debug_mode,
	'options_values' => array(
		0 => elgg_echo('hybridauth:debug_mode:disable'),
		1 => elgg_echo('hybridauth:debug_mode:enable')
	)
));
echo '</div>';

echo '<div>';
echo '<label>' . elgg_echo('hybridauth:public_auth') . '</label>';
echo elgg_view('input/dropdown', array(
	'name' => 'public_auth',
	'value' => ($entity->public_auth != false),
	'options_values' => array(
		0 => elgg_echo('hybridauth:public_auth:disable'),
		1 => elgg_echo('hybridauth:public_auth:enable')
	)
));
echo '</div>';

echo '<div>';
echo '<label>' . elgg_echo('hybridauth:persistent_session') . '</label>';
echo '<div class="elgg-text-help">' . elgg_echo('hybridauth:persistent_session:help') . '</div>';
echo elgg_view('input/dropdown', array(
	'name' => 'persistent_session',
	'value' => $entity->persistent_session,
	'options_values' => array(
		0 => elgg_echo('hybridauth:persistent_session:disable'),
		1 => elgg_echo('hybridauth:persistent_session:enable')
	)
));
echo '</div>';

/* send elgg credentials on new user creation through hybriauth? */
echo '<div>';
echo '<label>' . elgg_echo('hybridauth:registration:credentials') . '</label>';
echo elgg_view('input/dropdown', array(
	'name' => 'email_credentials',
	'value' => $entity->email_credentials ? $entity->email_credentials : 'yes',
	'options_values' => array(
		'yes' => elgg_echo('option:yes'),
		'no' => elgg_echo('option:no')
	)
));
echo '</div>';


echo '<div>';
echo '<label>' . elgg_echo('hybridauth:registration_instructions') . '</label>';
echo elgg_view('input/longtext', array(
	'name' => 'registration_instructions',
	'value' => $entity->registration_instructions,
));
echo elgg_view('output/longtext', array(
	'value' => elgg_echo('hybridauth:registration_instructions:help'),
	'class' => 'elgg-subtext'
));
echo '</div>';

$providers = ($entity->providers) ? unserialize($entity->providers) : array();
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
			$mod .= '<label>' . elgg_echo("hybridauth:provider:$provider:$key_name") . '</label>';
			$mod .= elgg_view('input/text', array(
				'name' => "providers[$provider][keys][$key_name]",
				'value' => $key_value,
			));
			$mod .= '</div>';
		}
	}

	$footer = '';
	if ($settings['enabled']) {

		try {

			$scope = (isset($settings['scope'])) ? $settings['scope'] : null;
			if (!$scope) {
				$adapter = (new \Elgg\HybridAuth\Session)->getClient()->getAdapter($provider);
				$scope = ($adapter) ? $adapter->adapter->scope : null;
			}
			
			if ($scope) {
				$mod .= '<div>';
				$mod .= '<label>' . elgg_echo("hybridauth:provider:scope") . '</label>';
				$mod .= elgg_view('input/text', array(
					'name' => "providers[$provider][scope]",
					'value' => $scope,
				));
				$mod .= '</div>';
			}

			if ($provider == 'OpenID') {
				$mod .= '<div class="clearfix">';
				$mod .= '<div class="elgg-col elgg-col-1of3">';
				$mod .= '<label>' . elgg_echo("hybridauth:provider:openid:name") . '</label>';
				$mod .= '<span class="elgg-text-help">' . elgg_echo("hybridauth:provider:openid:name:help") . '</span>';
				$mod .= '</div>';
				$mod .= '<div class="elgg-col elgg-col-2of3">';
				$mod .= '<label>' . elgg_echo("hybridauth:provider:openid:identifier") . '</label>';
				$mod .= '<span class="elgg-text-help">' . elgg_echo("hybridauth:provider:openid:identifier:help") . '</span>';
				$mod .= '</div>';
				$mod .= '</div>';
				$openid_settings = elgg_get_plugin_setting('openid_providers', 'elgg_hybridauth');
				$openid_providers = ($openid_settings) ? unserialize($openid_settings) : array();

				for ($i = 0; $i < 10; $i++) {
					$mod .= '<div class="clearfix">';
					$mod .= '<div class="elgg-col elgg-col-1of3">';
					$mod .= '<div class="pam">';
					$mod .= elgg_view('input/text', array(
						'name' => "openid_providers[$i][name]",
						'value' => (isset($openid_providers[$i])) ? $openid_providers[$i]['name'] : '',
					));
					$mod .= '</div>';
					$mod .= '</div>';
					$mod .= '<div class="elgg-col elgg-col-2of3">';
					$mod .= '<div class="pam">';
					$mod .= elgg_view('input/text', array(
						'name' => "openid_providers[$i][identifier]",
						'value' => (isset($openid_providers[$i])) ? $openid_providers[$i]['identifier'] : ''
					));
					$mod .= '</div>';
					$mod .= '</div>';
					$mod .= '</div>';
				}
			}
			$footer = '<div class="hybridauth-diagnostics-pass pam">' . elgg_echo('hybridauth:adapter:pass') . '</div>';
		} catch (Exception $e) {
			$footer = '<div class="hybridauth-diagnostics-fail pam">' . $e->getMessage() . '</div>';
		}
	}

	echo elgg_view_module('widget', $title, $mod, array('footer' => $footer, 'class' => 'hybridauth-provider-settings'));
}