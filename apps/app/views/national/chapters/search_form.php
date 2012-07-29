<?= form::open('chapters/search', array('method' => 'get')) ?>
  <div class="clearfix">
    <?= form::label('name', 'Member Name:') ?>
    <?= form::input('name', $this->form['name']) ?>
    <span class="error"><?= $this->errors['name'] ?></span>
  </div>
  <div class="clearfix select">
    <?= form::label('type', 'Member Status:')?>
    <?= form::dropdown('type', array('' => '- All -', 'active' => 'Active', 'pledge' => 'New Member', 'alumni' => 'Alumni', 'archive' => 'Archived'), $this->form['type']) ?>
    <span class="error"><?= $this->errors['type'] ?></span>
  </div>
  <div class="clearfix">
    <?= form::dropdown('site_id', array(0 => '- All Chapters -') + $this->chapter_options, $this->form['site_id']) ?>
    <span class="error"><?= $this->errors['site_id'] ?></span>
  </div>
<?= form::submit('search', 'Find Member(s)') ?>
<?= form::close() ?>