<div class="sizes form login">
	<?php echo $this->Form->create('User', array('action' => 'activatePassword')); ?>
	<fieldset>
		<legend><?php echo __('Reset Password'); ?></legend>
		<hr />
	<?php
		echo $this->Form->input('password', array('type' => 'password'));
		echo $this->Form->input('cpassword', array('type' => 'password', 'label' => __('Confirm Password')));
		if (!isset($ident)) {
			$ident = '';
		}
		if (!isset($activate)) {
			$activate = '';
		}
		echo $this->Form->hidden('ident', array('value' => $ident));
		echo $this->Form->hidden('activate', array('value' => $activate));
	?> 
	</fieldset>
	<?php echo $this->Form->end(__('Reset')); ?>
</div>

<script>
	document.getElementById('UserPassword').focus();
</script>
