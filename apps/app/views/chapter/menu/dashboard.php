<ul>
	<li><?= html::secondary_anchor('', 'Dashboard'); ?></li>
  <?php if (A2::instance()->allowed('sms', 'send')): ?>
    <li><?= html::secondary_anchor('sms', 'Text Messages') ?></li>
  <?php endif ?>
  <li><?= html::secondary_anchor('dues', 'Pay My Dues'); ?></li>
</ul>