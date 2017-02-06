<?php
/**
 * Elgg register form
 *
 * @package Elgg
 * @subpackage Core
 */
$provider = elgg_extract('provider', $vars);
$profile = elgg_extract('profile', $vars);

$password = $password2 = generate_random_cleartext_password();
$username = get_input('u');
$email = get_input('e');
$name = get_input('n');
$require_auth = get_input('require_auth');

if (elgg_is_sticky_form('hybridauth_register')) {
	extract(elgg_get_sticky_values('hybridauth_register'));
	elgg_clear_sticky_form('hybridauth_register');
}
?>

<?php if (!$require_auth): ?>
	<div class="hybridauth-credentials">
		<?php
		echo elgg_format_element('p', [
			'class' => 'hybridauth-autogen-instructions',
				], elgg_echo('hybridauth:credentials:instructions'));
		?>
	</div>
<?php endif; ?>

<div id="hybridauth-autogen" class="hidden">
	<?php
	if (!$username) {
		$username = str_replace(' ', '', $profile->displayName);
		if (!$username) {
			$username = $provider . '_user_' . rand(1000, 9999);
		}
		while (get_user_by_username($username)) {
			$username = str_replace(' ', '', $profile->displayName) . '_' . rand(1000, 9999);
		}
	}

	echo elgg_view_field([
		'#type' => 'text',
		'#label' => elgg_echo('hybridauth:username'),
		'name' => 'username',
		'value' => $username,
	]);

	echo elgg_view_field([
		'#type' => 'password',
		'#label' => elgg_echo('hybridauth:password'),
		'name' => 'password',
		'value' => $password,
	]);

	echo elgg_view_field([
		'#type' => 'password',
		'#label' => elgg_echo('hybridauth:passwordagain'),
		'name' => 'password2',
		'value' => $password2,
	]);
	?>
</div>

<?php
echo elgg_view_field([
	'#type' => 'hidden',
	'name' => 'provider',
	'value' => $provider
]);

echo elgg_view_field([
	'#type' => 'hidden',
	'name' => 'provider_uid',
	'value' => $profile->identifier
]);

// Check to see if we have the display name
if ($profile->displayName) {
	echo elgg_view('input/hidden', array(
		'name' => 'name',
		'value' => $profile->displayName
	));
} else {
	echo elgg_view_field([
		'#type' => 'text',
		'#label' => elgg_echo('hybridauth:name'),
		'#help' => elgg_echo('hybridauth:name:required'),
		'name' => 'name',
		'value' => $name,
		'required' => true,
		'field_class' => 'hybridauth-autogen-instructions',
	]);
}

// Check to see if we have a verified email address
if ($profile->emailVerified) {
	echo elgg_view_field([
		'#type' => 'hidden',
		'name' => 'email_verified',
		'value' => $profile->emailVerified
	]);
} else if ($profile->email) {
	echo elgg_view_field([
		'#type' => 'hidden',
		'name' => 'email',
		'value' => $profile->email
	]);
} else {

	echo '<div class="hybridauth-autogen-instructions">';

	if ($require_auth) {
		echo '<p>' . elgg_echo('hybridauth:credentials:login', array($email, $provider)) . '</p>';
	} else {
		echo '<p>' . elgg_echo('hybridauth:email:required') . '</p>';
	}

	echo elgg_view_field([
		'#type' => 'text',
		'#label' => elgg_echo('hybridauth:email'),
		'name' => 'email',
		'value' => $email,
	]);

	if ($require_auth) {
		echo elgg_view_field([
			'#type' => 'password',
			'#label' => elgg_echo('password'),
			'name' => 'authpass',
		]);
	}

	echo '</div>';
}
?>

<?php
$profile_info = array(
	'profile_url' => $profile->profileURL,
	'website_url' => $profile->websiteURL,
	'photo_url' => $profile->photoURL,
	'description' => $profile->description,
	'first_name' => $profile->firstName,
	'last_name' => $profile->lastName,
	'gender' => $profile->gender,
	'language' => $profile->language,
	'age' => $profile->age,
	'birthday' => $profile->birthDay,
	'birthmonth' => $profile->birthMonth,
	'birthyear' => $profile->birthYear,
	'contactemail' => $profile->email,
	'phone' => $profile->phone,
	'address' => $profile->address,
	'country' => $profile->country,
	'region' => $profile->region,
	'city' => $profile->city,
	'zip' => $profile->zip,
	$provider => $profile->displayName
);

foreach ($profile_info as $key => $value) {
	echo elgg_view_field([
		'#type' => 'hidden',
		'name' => $key,
		'value' => $value
	]);
}

$instructions = elgg_get_plugin_setting('registration_instructions', 'elgg_hybridauth');

if ($instructions) {
	// view for compatibility with profile manager
	echo '<div class="elgg-hybridauth-instructions">';
	echo '<p>';
	echo $instructions;
	echo '</p>';
	echo '</div>';
}

// view to extend to add more fields to the registration form
if (!$require_auth) {
	echo elgg_view('register/extend', $vars);
}

// Add captcha hook
echo elgg_view('input/captcha', $vars);

echo elgg_view('input/hidden', array('name' => 'friend_guid', 'value' => $vars['friend_guid']));
echo elgg_view('input/hidden', array('name' => 'invitecode', 'value' => $vars['invitecode']));

$footer = elgg_view_field([
	'#type' => 'submit',
	'value' => $require_auth ? elgg_echo('login') : elgg_echo('hybridauth:register'),
		]);

elgg_set_form_footer($footer);


if (elgg_is_active_plugin('profile_manager') && is_callable('elgg_require_js')) {
	echo elgg_format_element('script', [], 'require(["profile_manager/register"]);');
}

