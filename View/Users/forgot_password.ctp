<div class="sizes form login">
	<?php echo $this->Form->create('User', array('action' => 'forgotPassword')); ?>
	<fieldset>
		<legend><?php echo __('Forgot Password'); ?></legend>
		<hr />
	<?php
		echo $this->Form->input('email', array('label' => __('Enter Email or Username'), 'type' => 'text'));
	?> 
	</fieldset>
	<?php echo $this->Form->end(__('Send Email')); ?>
</div>

<script>
	document.getElementById('UserEmail').focus();
</script>
