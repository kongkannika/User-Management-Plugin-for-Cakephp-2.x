<div>
	<p>
		<?php echo __('Welcome %s, let\'s help you get signed in', $name); ?>
	</p>
	<p>
		<?php echo __('You have requested to have your password reset on %s. Please click the link below to reset your password now :', EMAIL_FROM_NAME); ?>
	</p>
	<p>
		<?php echo $this->Html->link($link, $link); ?>
	</p>
	<p>
		<?php echo __('If above link does not work please copy and paste the URL link (above) into your browser address bar to get to the Page to reset password'); ?>
	</p>
	<p>
		<?php echo __('Choose a password you can remember and please keep it secure.'); ?>
	</p>
	<p>
		<?php echo __('Thanks,'); ?>
	</p>
	<p>
		<?php echo EMAIL_FROM_NAME; ?>
	</p>
</div>