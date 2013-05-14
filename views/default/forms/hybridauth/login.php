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
	echo '<p class="hybridauth-login-instructions">' . elgg_echo('hybridauth:credentials:login', array($vars['username'], $vars['provider'])) . '</p>';
	?>
</div>
<div>
	<label><?php echo elgg_echo('loginusername'); ?></label>
	<?php echo elgg_view('input/text', array(
		'name' => 'username',
		'class' => 'elgg-autofocus',
		'value' => $vars['username']
		));
	?>
</div>
<div>
	<label><?php echo elgg_echo('password'); ?></label>
	<?php echo elgg_view('input/password', array('name' => 'password')); ?>
</div>

<?php echo elgg_view('login/extend', $vars); ?>

<div class="elgg-foot">
	<label class="mtm float-alt">
		<input type="checkbox" name="persistent" value="true" />
		<?php echo elgg_echo('user:persistent'); ?>
	</label>
	
	<?php echo elgg_view('input/submit', array('value' => elgg_echo('login'))); ?>
	<ul class="elgg-menu elgg-menu-general mtm">
		<li><a class="forgot_link" href="<?php echo elgg_get_site_url(); ?>forgotpassword">
			<?php echo elgg_echo('user:password:lost'); ?>
		</a></li>
	</ul>
</div>
