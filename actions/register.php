<?php

/**
 * Elgg registration action
 *
 * @package Elgg.Core
 * @subpackage User.Account
 */
elgg_make_sticky_form('hybridauth_register');

// Get variables
$username       = get_input('username');
$password       = get_input('password', null, false);
$password2      = get_input('password2', null, false);
$email          = get_input('email');
$email_verified = get_input('email_verified');
$authpass       = get_input('authpass', null, false);

if ($email_verified) {
	$email = $email_verified;
	$verified = true;
}

$name         = get_input('name');
$friend_guid  = (int) get_input('friend_guid', 0);
$invitecode   = get_input('invitecode');
$provider_uid = get_input('provider_uid');
$provider     = get_input('provider');
$photo_url    = get_input('photo_url');

// The user has an existing account. Lets prompt for their password
if ($users = get_user_by_email($email)) {

    $return_url = elgg_get_site_url() . "hybridauth/authenticate?provider={$provider}&require_auth=true&e=" . urlencode($email);

    if ($authpass) {

        // Authenticate the user
        $result = elgg_authenticate($users[0]->username, $authpass);

        if ($result !== true) {
            register_error($result);
    	    forward($return_url . '&auth_fail=1');
        } 

        // We have a successful authentication
        forward("hybridauth/authenticate?provider=$provider&email=$email");
    } 

    // Go back to the registration screen and request a password
    forward($return_url);
}

if (elgg_get_config('allow_registration')) {
	try {
		if (trim($password) == "" || trim($password2) == "") {
			throw new RegistrationException(elgg_echo('RegistrationException:EmptyPassword'));
		}

		if (strcmp($password, $password2) != 0) {
			throw new RegistrationException(elgg_echo('RegistrationException:PasswordMismatch'));
		}
		if ($verified) {
			elgg_unregister_plugin_hook_handler('register', 'user', 'uservalidationbyemail_disable_new_user');
		}
		$guid = register_user($username, $password, $name, $email, false, $friend_guid, $invitecode);

		if ($guid) {
			$new_user = get_entity($guid);

			// allow plugins to respond to self registration
			// note: To catch all new users, even those created by an admin,
			// register for the create, user event instead.
			// only passing vars that aren't in ElggUser.
			$params = array(
				'user' => $new_user,
				'password' => $password,
				'friend_guid' => $friend_guid,
				'invitecode' => $invitecode,
				'photo_url' => $photo_url,
				'provider' => $provider,
				'provider_uid' => $provider_uid
			);

			$metadata = array(
				'description' => get_input('description'),
				'website' => get_input('website_url'),
				'first_name' => get_input('first_name'),
				'last_name' => get_input('last_name'),
				'gender' => get_input('gender'),
				'age' => get_input('age'),
				'birthdate' => mktime(0, 0, 0, get_input('birthmonth'), get_input('birthday'), get_input('birthyear')),
				'contactemail' => get_input('contactemail'),
				'phone' => get_input('phone'),
				'address' => get_input('address'),
				'country' => get_input('country'),
				'region' => get_input('region'),
				'city' => get_input('city'),
				'zip' => get_input('zip'),
			);

			foreach (array('address', 'country', 'region', 'city', 'zip') as $location_element) {
				if (get_input($location_element, false)) {
					$location[] = get_input($location_element);
				}
			}
			if ($location) {
				$metadata['location'] = implode(', ', $location);
			}

			// we have received a verified email from a provider
			if ($verified) {
				elgg_set_user_validation_status($new_user->guid, true, 'hybridauth');
			}

			$import_mapping = elgg_get_plugin_setting('import_mapping', 'elgg_hybridauth');
			if (!isset($import_mapping)) {
				// default settings
				$fields = [
					'description',
					'website',
					'first_name',
					'last_name',
					'gender',
					'age',
					'birthdate',
					'contactemail',
					'phone',
				];
				$import_mapping = array_combine($fields, $fields);
			} else {
				$import_mapping = unserialize($import_mapping);
			}

			$metadata["{$provider}_url"] = get_input('profile_url');
			$metadata["$provider"] = get_input($provider);

			$access_id = (int) elgg_get_plugin_setting('import_mapping_access_id', 'elgg_hybridauth', ACCESS_PRIVATE);

			foreach ($metadata as $md_name => $md_value) {
				$mapped_name = $import_mapping[$md_name];
				if (empty($mapped_name)) {
					continue;
				}
				create_metadata($new_user->guid, $mapped_name, $md_value, '', $new_user->guid, $access_id, true);
			}

			if ($photo_url) {
				$tmp = new ElggFile();
				$tmp->owner_guid = $new_user->guid;
				$tmp->setFilename('tmp/icon.jpg');
				$tmp->open('write');
				$tmp->write(file_get_contents($photo_url));
				$tmp->close();

				$new_user->saveIconFromElggFile($tmp);

				$tmp->delete();
			}

			if ($provider && $provider_uid) {
				elgg_set_plugin_user_setting("$provider:uid", $provider_uid, $new_user->guid, 'elgg_hybridauth');
				elgg_trigger_plugin_hook('hybridauth:authenticate', $provider, array('entity' => $new_user));
			}

			$params = array_merge($params, $metadata);

			// @todo should registration be allowed no matter what the plugins return?
			if (!elgg_trigger_plugin_hook('register', 'user', $params, TRUE)) {
				$ia = elgg_set_ignore_access(true);
				$new_user->delete();
				elgg_set_ignore_access($ia);
				// @todo this is a generic messages. We could have plugins
				// throw a RegistrationException, but that is very odd
				// for the plugin hooks system.
				throw new RegistrationException(elgg_echo('registerbad'));
			}

			$subject = elgg_echo('useradd:subject');
			$body = elgg_echo('useradd:body', array(
				$name,
				elgg_get_site_entity()->name,
				elgg_get_site_entity()->url,
				$username,
				$password,
					));

			$notify_setting = elgg_get_plugin_setting('email_credentials', 'elgg_hybridauth');
			
			if ($notify_setting != 'no') {
				notify_user($new_user->guid, elgg_get_site_entity()->guid, $subject, $body);
			}

			elgg_clear_sticky_form('hybridauth_register');

			unset($_SESSION['hybridauth']);

			system_message(elgg_echo("registerok", array(elgg_get_site_entity()->name)));

			// if exception thrown, this probably means there is a validation
			// plugin that has disabled the user
			try {
				login($new_user);
			} catch (LoginException $e) {
				// do nothing
			}

			// Forward on success, assume everything else is an error...
			forward();
		} else {
			register_error(elgg_echo("registerbad"));
		}
	} catch (RegistrationException $r) {
		register_error($r->getMessage());
	}
} else {
	register_error(elgg_echo('registerdisabled'));
}

forward(REFERER);
