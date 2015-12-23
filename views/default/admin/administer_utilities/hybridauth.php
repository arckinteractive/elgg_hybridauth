<?php

$providers = unserialize(elgg_get_plugin_setting('providers', 'elgg_hybridauth'));

foreach ($providers as $provider => $settings) {

	if (!$settings['enabled']) {
		continue;
	}

	echo '<h2>' . $provider . '</h2>';

	$options = array(
		'selects' => array('ps.value as uid'),
		'type' => 'user',
		'plugin_id' => 'elgg_hybridauth',
		'plugin_user_setting_names' => "$provider:uid",
		'limit' => 0,
		'order_by' => 'ps.value ASC',
	);

	$users = new ElggBatch('elgg_get_entities_from_plugin_user_settings', $options);

	echo '<table class="elgg-table-alt">';
	foreach ($users as $user) {
		echo '<tr>';

		echo '<td width="10%">';
		echo elgg_get_plugin_user_setting("$provider:uid", $user->guid, 'elgg_hybridauth');
		echo '</td>';

		echo '<td width="10%">';
		echo elgg_view('output/url', array(
			'text' => $user->name,
			'href' => $user->getURL(),
			'target' => '_blank',
		));
		echo '</td>';

		echo '<td width="80%">';
		$info = '';
		$data = elgg_get_plugin_user_setting("hybridauth_session_data", $user->guid, 'elgg_hybridauth');
		if ($data) {
			$ha_session_data = unserialize($data);
			foreach ($ha_session_data as $key => $val) {
				$info .= '<strong>' . $key . '</strong> ' . unserialize($val) . '<br />';
			}
		}
		if ($info) {
			echo elgg_view('output/url', array(
				'href' => "#{$provider}-{$user->guid}",
				'text' => elgg_echo('hybridauth:session_data'),
				'rel' => 'toggle',
			));
			echo "<div id='{$provider}-{$user->guid}' class='hidden' style='word-break:break-all'>$info</div>";
		}
		echo '</td>';
		echo '</tr>';
	}
	echo '</table>';
}