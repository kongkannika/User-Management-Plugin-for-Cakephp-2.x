<div class="sizes form login" style="width: 650px !important;">
	<?php echo $this->Form->create('User'); ?>
	<fieldset>
		<legend><?php echo __('Access Denied'); ?></legend>
		<hr />
		<p style="margin: 1em 0;">
			<?php echo __('Sorry, You don\'t have permission to view that page. go to %s', 
				$this->Html->link(__('Profile'), array('controller' => 'users', 'action' => 'myprofile')));
			?> 
		</p>
	</fieldset>
	<?php echo $this->Form->end(); ?>
</div>
