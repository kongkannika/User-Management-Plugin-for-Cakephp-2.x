<div class="users form">
	<?php echo $this->Form->create('User', array('action' => 'editUser')); ?>
	<fieldset>
		<legend><?php echo __('Edit User'); ?></legend>
		<?php
		echo $this->Form->input('id');
		echo $this->Form->input('user_group_id');
		echo $this->Form->input('username', array('required' => true, 'div' => 'input text required'));
		echo $this->Form->input('first_name', array('required' => true, 'div' => 'input text required'));
		echo $this->Form->input('last_name', array('required' => true, 'div' => 'input text required'));
		echo $this->Form->input('email', array('required' => true, 'div' => 'input text required'));
		?>
	</fieldset>
	<?php echo $this->Form->end(__('Submit')); ?>
</div>
