<div id="search" class="right">
  <?= form::open('chapters/search', array('method' => 'get')) ?>
  <?= form::input('name', $_GET['name']) ?>
  <?= form::submit('search', 'Go') ?>
  <?= form::close() ?>
</div>
<ul>
	<li><?= html::secondary_anchor('', 'Dashboard'); ?></li>
</ul>
