<?php if (Router::$routed_uri != 'chapters/search'): ?>
  <div class="right">
    <span id="search">
      <?= form::open('chapters/search', array('method' => 'get')) ?>
      <?= form::input('name', $_GET['name']) ?>
      <?= form::submit('search', 'Go') ?>
      <?= form::close() ?>
    </span>
  </div>  
<?php endif ?>
<ul>
  <li><?= html::secondary_anchor('chapters', 'Chapter Rosters'); ?></li>
  <li><?= html::secondary_anchor('chapters/search', 'Member Search'); ?></li>
</ul>
