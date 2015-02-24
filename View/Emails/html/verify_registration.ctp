<div>
	<p>
		<?php echo __('Hello %s,', $name); ?>
	</p>
	<p>
		<?php echo __('Here is the information that you have requested on %s.', EMAIL_FROM_NAME); ?>
	</p>
	<p>
		<?php echo __('Verify your registration, click on this link : %s', $this->Html->link($link, $link)); ?>
	</p>
	<p>
		<?php echo __('See you soon on our site!'); ?>
	</p>
	<p>
		<?php echo EMAIL_FROM_NAME; ?>
	</p>
</div>