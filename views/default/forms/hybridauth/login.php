<?php
/**
 * Elgg login form
 *
 * @package Elgg
 * @subpackage Core
 */
echo elgg_view('input/hidden', array(
	'name' => 'aux_provider',
	'value' => $vars['provider']
));
echo elgg_view('input/hidden', array(
	'name' => 'aux_provider_uid',
	'value' => $vars['provider_uid']
));
?>
<div class="hybridauth-credentials">
	<?php
	echo elgg_format_element('p', [
		'class' => 'hybridauth-login-instructions',
			], elgg_echo('hybridauth:credentials:login', array(
		$vars['username'],
		$vars['provider'],
	)));
	?>
</div>
<?php
echo elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('loginusername'),
	'name' => 'username',
	'value' => elgg_extract('username', $vars),
]);
echo elgg_view_field([
	'#type' => 'password',
	'#label' => elgg_echo('password'),
	'name' => 'password',
]);

echo elgg_view('login/extend', $vars);

if (isset($vars['returntoreferer'])) {
	echo elgg_view_field([
		'#type' => 'hidden',
		'name' => 'returntoreferer',
		'value' => 'true'
	]);
}

ob_start();
?>
<div class="elgg-foot">
	<label class="mtm float-alt">
		<input type="checkbox" name="persistent" value="true" />
		<?php echo elgg_echo('user:persistent'); ?>
	</label>

	<?php
	echo elgg_view('input/submit', array('value' => elgg_echo('login')));

	echo elgg_view_menu('login', array(
		'sort_by' => 'priority',
		'class' => 'elgg-menu-general elgg-menu-hz mtm',
	));
	?>
</div>
<?php
$footer = ob_get_clean();
elgg_set_form_footer($footer);
