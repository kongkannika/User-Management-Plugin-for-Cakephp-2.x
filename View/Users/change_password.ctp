<div class="sizes form login">
	<?php echo $this->Form->create('User', array('action' => 'changePassword')); ?>
	<fieldset>
		<legend><?php echo __('Change Password'); ?></legend>
		<hr />
	<?php
		echo $this->Form->input('oldpassword', array('type' => 'password', 'label' => __('Old Password')));
		echo $this->Form->input('password', array('type' => 'password', 'label' => __('New Password')));
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
	<?php echo $this->Form->end(__('Change')); ?>
</div>

<script>
	document.getElementById('UserOldpassword').focus();
</script>
