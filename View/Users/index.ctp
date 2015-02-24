<div class="users index">
	<h2 class="go-left"><?php echo __('Users'); ?></h2>
	<?php echo $this->Html->link(__('Add New User'), array('controller' => 'users', 'action' => 'addUser'), array('class' => 'go-right button')); ?>
	<div class="clear"></div>
	<table cellpadding="0" cellspacing="0">
		<tr>
			<th width="50"><?php echo $this->Paginator->sort('id'); ?></th>
			<th><?php echo $this->Paginator->sort('first_name', __('Name')); ?></th>
			<!--<th><?php echo $this->Paginator->sort('username', __('Username')); ?></th>-->
			<th><?php echo $this->Paginator->sort('email', __('Email')); ?></th>
			<th><?php echo $this->Paginator->sort('user_group_id', __('Group')); ?></th>
			<th class="actions text-center" width="200"><?php echo __('Actions'); ?></th>
		</tr>
		<?php foreach ($users as $user) : ?>
			<tr>
				<td><?php echo $user['User']['id']; ?></td>
				<td><?php echo $user['User']['first_name'] . ' ' . $user['User']['last_name']; ?></td>
				<!--<td><?php echo $user['User']['username']; ?></td>-->
				<td><?php echo $user['User']['email']; ?></td>
				<td><?php echo $user['UserGroup']['name']; ?></td>
				<td class="actions text-left">
					<?php echo $this->Html->link(__('View'), array('action' => 'viewUser', $user['User']['id'])); ?>
					<?php echo $this->Html->link(__('Edit'), array('action' => 'editUser', $user['User']['id'])); ?>
					<?php if ($user['User']['id'] !== '1' && $user['User']['username'] !== 'admin') : ?>
						<?php echo $this->Form->postLink(__('Delete'), array('action' => 'deleteUser', $user['User']['id']), array('escape' => false, 'confirm' => __('Are you sure you want to delete this user?'))); ?>
					<?php endif; ?> 
				</td>
			</tr>
		<?php endforeach; ?>
	</table>
	<p>
		<?php
		echo $this->Paginator->counter(array(
			'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
		));
		?>	</p>
	<div class="paging">
		<?php
		echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
		?>
	</div>
</div>