<div class="sizes form login" style="width: 650px !important;">
	<?php echo $this->Form->create('User'); ?>
	<fieldset>
		<legend class="go-left" style="margin: -16px 0 10px;"><?php echo __('My Profile'); ?></legend>
		<?php echo $this->Html->link(__('Change Password'), array('action' => 'changePassword'), array('class' => 'go-right', 'style' => 'margin-top: -10px;')); ?>
		<div class="clear"></div>
		<hr />
		<?php if (!empty($user)) : ?>
			<dl>
				<dt><?php echo __('Username'); ?></dt>
				<dd><?php echo h($user['User']['username']) ?>&nbsp;</dd>
				<dt><?php echo __('First Name'); ?></dt>
				<dd><?php echo h($user['User']['first_name']) ?>&nbsp;</dd>
				<dt><?php echo __('Last Name'); ?></dt>
				<dd><?php echo h($user['User']['last_name']) ?>&nbsp;</dd>
				<dt><?php echo __('Email'); ?></dt>
				<dd><?php echo h($user['User']['email']) ?>&nbsp;</dd>
				<dt><?php echo __('User Group'); ?></dt>
				<dd><?php echo h($user['UserGroup']['name']) ?>&nbsp;</dd>

				<dt><?php echo __('Facebook'); ?></dt>
				<dd>
					<?php
					if ($user['User']['facebook_id']) :
						echo $this->Html->link($user['User']['facebook_id'], 'https://www.facebook.com/' . $user['User']['facebook_id'], array('target' => '_blank'));
					else :
						echo __('N/A');
					endif;
					?>&nbsp;
				</dd>
				<dt><?php echo __('Photo'); ?></dt>
				<dd>
					<?php
					if ($user['User']['photo']) :
						echo $this->Html->image($user['User']['photo'], array('class' => 'image-thumbnail'));
					else :
						echo __('N/A');
					endif;
					?>&nbsp;
				</dd>
				<dt><?php echo __('Gender'); ?></dt>
				<dd>
					<span style="text-transform: capitalize;"><?php echo $user['User']['gender']; ?></span>
					&nbsp;
				</dd>
				<dt><?php echo __('Birthday'); ?></dt>
				<dd>
					<?php echo $this->Html->toHumanDate($user['User']['birthday']); ?>
					&nbsp;
				</dd>
				<dt><?php echo __('Telephone'); ?></dt>
				<dd>
					<?php echo $user['User']['telephone']; ?>
					&nbsp;
				</dd>
				<dt><?php echo __('Address'); ?></dt>
				<dd>
					<?php echo $user['User']['address']; ?>
					&nbsp;
				</dd>
				<dt><?php echo __('Nationality'); ?></dt>
				<dd>
					<?php echo $user['User']['nationality']; ?>
					&nbsp;
				</dd>
				<dt><?php echo __('Workplace'); ?></dt>
				<dd>
					<?php echo $user['User']['workplace']; ?>
					&nbsp;
				</dd>
				<dt><?php echo __('Position'); ?></dt>
				<dd>
					<?php echo $user['User']['position']; ?>
					&nbsp;
				</dd>
				<dt><?php echo __('Created'); ?></dt>
				<dd><?php echo $this->Html->toHumanDateTime($user['User']['created']) ?>&nbsp;</dd>
			</dl>
			<?php
		else :
			echo '<tr><td colspan="2"><br/><br/>No Data</td></tr>';
		endif;
		?> 
	</fieldset>
	<?php echo $this->Form->end(); ?>
</div>
