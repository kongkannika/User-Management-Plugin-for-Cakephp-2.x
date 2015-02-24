<div class="sizes form login">
	<?php echo $this->Form->create('User', array('action' => 'login')); ?>
	<fieldset>
		<legend><?php echo __('Sign In'); ?></legend>
		<hr/>
		<?php
		echo $this->Form->input('email', array('label' => __('Email or Username'), 'type' => 'text'));
		echo $this->Form->input('password', array('type' => 'password'));
		if (!isset($this->request->data['User']['remember'])) :
			$this->request->data['User']['remember'] = true;
		endif;
		echo $this->Form->input('remember', array('label' => __('Remember me'), 'type' => 'checkbox', 'div' => 'checkbox go-left'));
		?>
		<div class="go-right">
			<?php
			echo $this->Html->link(__("Forgot Password?", true), "/forgotPassword");
			?>
		</div>
		<div class="clear"></div>
	</fieldset>
	<?php echo $this->Form->end(__('Sign In')); ?>
</div>

<script>
	document.getElementById('UserEmail').focus();
</script>