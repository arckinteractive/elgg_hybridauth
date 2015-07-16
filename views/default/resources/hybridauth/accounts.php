<?php

gatekeeper();

$username = get_input('username');
$user = get_user_by_username($username);

if (!elgg_instanceof($user, 'user')) {
	$user = elgg_get_logged_in_user_entity();
	if ($user->username !== $username) {
		forward("hybridauth/accounts/$user->username");
	}
}

if (!$user->canEdit()) {
	register_error(elgg_echo('noaccess'));
	forward('', '403');
}

elgg_set_page_owner_guid($user->guid);

elgg_set_context('settings');

$title = elgg_echo('hybridauth:accounts');
$content = elgg_view('hybridauth/accounts');

$layout = elgg_view_layout('content', array(
	'title' => $title,
	'content' => $content,
	'filter' => false
		));

echo elgg_view_page($title, $layout);
