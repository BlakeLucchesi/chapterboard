<p>Current Migration: <?php echo $current_version ?></p>
<p>Last Version: <?php echo $last_version ?></p>
<p>Status:
	<?php if ($current_version == $last_version) : ?>
		Up-to-date
	<?php else : ?>
		Ready for update
	<?php endif; ?>
</p>

<?php if ($current_version != $last_version) : ?>
<?php echo html::anchor('migrations/update', 'Update') ?>
<?php endif; ?>

<div>
	<?php echo form::open('migrations/update') ?>
	<p>
		<?php echo form::label('version', 'Go to version:') ?>
		<?php echo form::input('version') ?>
		<?php echo form::submit('', 'Go') ?>
	</p>
	<?php echo form::close() ?>
</div>