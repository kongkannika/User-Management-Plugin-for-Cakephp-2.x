<div class="users form">
	<?php echo $this->Form->create('User', array('action' => 'addUser')); ?>
	<fieldset>
		<legend><?php echo __('Add User'); ?></legend>
		<?php
		echo $this->Form->input('user_group_id', array('default' => 1));
		echo $this->Form->input('username', array('required' => true, 'div' => 'input text required'));
		echo $this->Form->input('first_name', array('required' => true, 'div' => 'input text required'));
		echo $this->Form->input('last_name', array('required' => true, 'div' => 'input text required'));
		echo $this->Form->input('email', array('required' => true, 'div' => 'input text required'));
		echo $this->Form->input('password', array('required' => true, 'div' => 'input text required', 'value' => ''));
		echo $this->Form->input('cpassword', array('label' => __('Confirm Password'), 'type' => 'password', 'value' => '', 'required' => true, 'div' => 'input text required'));
		?> 
	</fieldset>
	<?php echo $this->Form->end(__('Submit')); ?>
</div>
