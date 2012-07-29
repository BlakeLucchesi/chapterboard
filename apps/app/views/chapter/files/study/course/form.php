<div class="heading clearfix">
  <h2><?= $this->title ?></h2>
</div>

<?= message::get() ?>

<div class="clearfix">
  <fieldset>
    <?= form::open() ?>
    
    <div class="clearfix">
      <?= form::label('title', 'Course Title:*')?>
      <?= form::input('title', $this->form['title'], 'class="medium"') ?> <em>(Asian American Studies, Molecular Physics...)</em>
      <div class="error"><?= $this->errors['title'] ?></div>
    </div>
    
    <div class="clearfix">
      <?= form::label('code', 'Course Code:*')?>
      <?= form::input('code', $this->form['code'], 'class="small"') ?> <em>(Math 100, Soc 1a, Crim 8...)</em>
      <div class="error"><?= $this->errors['code'] ?></div>
    </div>
    
    <div class="clearfix">
      <?= form::label('department', 'Department:*')?>
      <?= form::input('department', $this->form['department'], 'class="medium"') ?> <em>(Physics, Sociology, Math...)</em>
      <div class="error"><?= $this->errors['department'] ?></div>
    </div>
    
    <div class="clearfix">
      <?= form::label('professor', 'Professor:*')?>
      <?= form::input('professor', $this->form['professor'], 'class="medium"') ?>
      <span class="error"><?= $this->errors['professor'] ?></span>
    </div>
    
    <div class="clearfix">
      <?= form::label('description', 'Description:')?>
      <?= form::textarea('description', $this->form['description']) ?>
      <span class="error"><?= $this->errors['description'] ?></span>
    </div>    
    
    <?php if ($this->course->id): ?>
      <?= form::submit('submit', 'Save Changes') ?> or <?= html::anchor('files/study/course/'. $this->course->id, 'cancel') ?>
    <?php else: ?>
      <?= form::submit('submit', 'Add Course') ?> or <?= html::anchor('files/study', 'cancel') ?>
    <?php endif ?>
    <?= form::close() ?>
  </fieldset>
</div>