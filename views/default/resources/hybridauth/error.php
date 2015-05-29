<?php

$provider = elgg_extract('provider', $vars);
$error = elgg_extract('error', $vars);

$title = elgg_echo('error:default');
$content = ($error) ? : elgg_echo('hybridauth:error', array($provider));

$footer = '';
if (elgg_extract('retry', $vars, true)) {
	$footer = '<div class="elgg-foot ptl">' . elgg_view('output/url', array(
				'href' => current_page_url(),
				'text' => elgg_echo('hybridauth:try_again'),
				'class' => 'elgg-button elgg-button-action',
			)) . '</div>';
}

$layout = elgg_view_layout('error', array(
	'title' => $title,
	'content' => $content . $footer,
		));
echo elgg_view_page($title, $layout, 'error');
