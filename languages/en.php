<?php

$english = array(

	'hybridauth:admin:diagnostics' => 'HybridAuth Diagnostics',

	'hybridauth:admin:diagnostics:requirement' => 'Requirement',
	'hybridauth:admin:diagnostics:status' => 'Status',
	'hybridauth:admin:diagnostics:message' => 'Notes',

	'hybridauth:admin:diagnostics:php_version' => 'PHP 5.2+',
	'hybridauth:admin:diagnostics:php_version:pass' => 'PHP >= 5.2.0 installed.',
	'hybridauth:admin:diagnostics:php_version:fail' => 'PHP >= 5.2.0 not installed.',

	'hybridauth:admin:diagnostics:elgg_oauth' => 'OAuth API 1.8 plugin',
	'hybridauth:admin:diagnostics:elgg_oauth:pass' => 'OAuth API 1.8 plugin is disabled',
	'hybridauth:admin:diagnostics:elgg_oauth:fail' => 'OAuth API plugin enabled.<br /><b>OAuth API plugin</b> is not compatible with this plugin, and many providers like twitter and myspace wont work! Please disabled it!',
	
	'hybridauth:admin:diagnostics:curl' => 'CURL Extension',
	'hybridauth:admin:diagnostics:curl:pass' => 'PHP Curl extension [http://www.php.net/manual/en/intro.curl.php] installed.',
	'hybridauth:admin:diagnostics:curl:fail' => 'PHP Curl extension [http://www.php.net/manual/en/intro.curl.php] not installed.',

	'hybridauth:admin:diagnostics:json' => 'JSON Extension',
	'hybridauth:admin:diagnostics:json:pass' => 'PHP JSON extension [http://php.net/manual/en/book.json.php] installed.',
	'hybridauth:admin:diagnostics:json:fail' => 'PHP JSON extension [http://php.net/manual/en/book.json.php] is disabled.',

	'hybridauth:admin:diagnostics:pecl_oauth' => 'PECL OAuth Extension',
	'hybridauth:admin:diagnostics:pecl_oauth:pass' => 'PECL OAuth extension [http://php.net/manual/en/book.oauth.php] not installed.',
	'hybridauth:admin:diagnostics:pecl_oauth:fail' => 'PECL OAuth extension [http://php.net/manual/en/book.oauth.php] installed. OAuth PECL extension is not compatible with this library.',

	'hybridauth:admin:elgg_social_login' => 'Import user settings from elgg_social_login',
	'hybridauth:admin:elgg_social_login:count' => 'It appears that you have had <i>elgg_social_login</i> installed on your site before enabling <i>elgg_hybridauth</i>.<br />
							We have found <b>%s</b> users that have used their social accounts previously to log in to your site. <br />
							You can import their settings now so that their ability to use social accounts is not disrupted.<br />
							In case of conflicting user records, earlier user records will be preserved',
	'hybridauth:admin:elgg_social_login:action' => '%s records were updated',

	'hybridauth:admin:social_connect' => 'Import user settings from social_connect',
	'hybridauth:admin:social_connect:count' => 'It appears that you have had <i>social_connect</i> installed on your site before enabling <i>elgg_hybridauth</i>.<br />
							We have found <b>%s</b> users that have used their social accounts previously to log in to your site. <br />
							You can import their settings now so that their ability to use social accounts is not disrupted.<br />
							In case of conflicting user records, earlier user records will be preserved',
	'hybridauth:admin:social_connect:action' => '%s records were updated',

	'hybridauth:debug_mode' => 'Debug Mode',
	'hybridauth:debug_mode:enable' => 'On',
	'hybridauth:debug_mode:disable' => 'Off',

	'hybridauth:persistent_session' => 'Persistent User Sessions',
	'hybridauth:persistent_session:help' => 'Do not enable this option if you are only using hybridauth for login and registration. '
							. 'This feature is designed to persist user sessions for better integration with provider APIs',
	'hybridauth:persistent_session:enable' => 'On',
	'hybridauth:persistent_session:disable' => 'Off',

	'hybridauth:provider:enable' => 'Enable this provider',
	'hybridauth:provider:enabled' => 'Enable',
	'hybridauth:provider:disabled' => 'Disable',

	'hybridauth:provider:id' => 'Provider ID',
	'hybridauth:provider:key' => 'Public Key',
	'hybridauth:provider:secret' => 'Private Key',

	'hybridauth:provider:Google:id' => 'Client ID',
	'hybridauth:provider:Google:secret' => 'Client Secret',

	'hybridauth:provider:Facebook:id' => 'App ID',
	'hybridauth:provider:Facebook:secret' => 'App Secret',

	'hybridauth:provider:Twitter:key' => 'Consumer Key',
	'hybridauth:provider:Twitter:secret' => 'Consumer Secret',

	'hybridauth:provider:LinkedIn:key' => 'API Key',
	'hybridauth:provider:LinkedIn:secret' => 'Secret Key',

	'hybridauth:provider:Yahoo:key' => 'Consumer Key',
	'hybridauth:provider:Yahoo:secret' => 'Consumer Secret',

	'hybridauth:provider:Live:id' => 'Client ID',
	'hybridauth:provider:Live:secret' => 'Client Secret',

	'hybridauth:provider:Foursquare:id' => 'Client ID',
	'hybridauth:provider:Foursquare:secret' => 'Client Secret',

	'hybridauth:provider:scope' => 'Permissions scope',
	
	'hybridauth:adapter:pass' => 'Service is active',

	'hybridauth:connect' => 'or connect with:',
	'hybridauth:error' => 'An error has occurred, and has been logged. Please contact the site administrator if the error persists',
	'hybridauth:try_again' => 'Try again',
	'hybridauth:login:provider' => 'You have been logged in using your %s account',
	'hybridauth:link:provider' => 'You have successfully linked your account with %s',
	'hybridauth:link:provider:error' => 'This %s account has already been linked to another profile in the system. Please log in using that profile to deauthorize it, or link this profile to another account',
	'hybridauth:unlink:provider' => 'There was a problem authenticating your account with %s. The link has been removed. If you have deauthorized our site in %s, you can reanable the link by visiting your settings page',
	
	'hybridauth:register' => 'Complete Registration',
	'hybridauth:credentials:instructions' => 'We have autogenerated your username and password. Your credentials will be sent to you via email once you complete the registration.',

	'hybridauth:name' => 'Display Name',
	'hybridauth:name:required' => 'Please enter a display name to be used for this site',
	'hybridauth:username' => 'Username',
	'hybridauth:password' => 'Password',
	'hybridauth:passwordagain' => 'Confirm Password',
	'hybridauth:email' => 'Email Address',
	'hybridauth:email:required' => 'This website requires that you provide a valid email address',

	'hybridauth:login' => 'Login with existing credentials',
	'hybridauth:credentials:login' => 'An account with the email %s already exists. Please login so we can link your %s account to your existing profile.',

	'hybridauth:provider:user:authenticate' => 'Connect now',
	'hybridauth:provider:user:deauthorize' => 'Disconnect now',
	'hybridauth:provider:user:authenticated' => 'You have successfully linked your acount to this provider',
	'hybridauth:provider:user:deauthorized' => 'You have successfully unlinked your acount from this provider',
	'hybridauth:provider:user:deauthorized:error' => 'Your acount could not be disconnected from this provider',
	
	'hybridauth:registration_instructions' => "Registration instructions",
	'hybridauth:registration_instructions:help' => "Any instructions or information you need to relay to users after they have authenticated with an external provider but before they complete their registration",
    'hybridauth:registration:credentials' => "Email elgg credentials to new users who registered using hybridauth?",

	'hybridauth:accounts' => 'Connected social accounts',

	'hybridauth:public_auth' => 'Enable login and registration with social providers (extend the forms) for logged out users',
	'hybridauth:public_auth:disable' => 'Disable',
	'hybridauth:public_auth:enable' => 'Enable',

	'hybridauth:provider:openid:name' => 'OpenID Provider',
	'hybridauth:provider:openid:name:help' => 'Name of the OpenID provider (e.g. StackExchange)',
	'hybridauth:provider:openid:identifier' => 'OpenID Identifier',
	'hybridauth:provider:openid:identifier:help' => 'OpenID URL (e.g. https://openid.stackexchange.com)',
);

add_translation("en", $english);
