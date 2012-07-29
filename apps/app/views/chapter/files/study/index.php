<div class="heading clearfix">
  <h2><?= $this->title ?></h2>
  <ul>
    <li><?= html::anchor('files/study/course/add', 'Add a Course') ?></li>
  </ul>
</div>

<?= message::get() ?>

<div class="clearfix">
  <div id="files" class="split-left">
    <h3 class="title"><?= $this->course_title ?></h3>
    <div class="files clearfix courses">
      <?php if ($this->courses->count()): ?>
        <?php foreach ($this->courses as $course): ?>
          <div class="course file">
            <h3><?= html::anchor('files/study/course/'. $course->id, $course->title()); ?></h3>
            <div>Department: <?= $course->department ?></div>
            <div class="updated"><?= date::ago($course->updated) ?></div>
          </div>
        <?php endforeach ?>
      <?php else: ?>
        <div class="course file empty">
          <?php if ($this->search): ?>
            <p>Your search returned no results. Please try again.</p>
          <?php else: ?>
            <p>Get started by <?= html::anchor('files/study/course/add', 'adding a course') ?>.</p>
          <?php endif ?>
        </div>
      <?php endif ?>
    </div>
    <?= $this->pagination ?>
  </div>
  
  <div class="split-right">
    <div class="block">
      <h3 class="title">Course Search</h3>
      <fieldset>
        <?= form::open('files/study', array('method' => 'get')) ?>

          <div class="clearfix">
            <?= form::label('title', 'Title:')?>
            <?= form::input('title', $this->form['title']) ?>
            <span class="error"><?= $this->errors['title'] ?></span>
          </div>
          <div class="clearfix">
            <?= form::label('code', 'Course Code:')?>
            <?= form::input('code', $this->form['code']) ?>
            <span class="error"><?= $this->errors['code'] ?></span>
          </div>
          <div class="clearfix">
            <?= form::label('department', 'Department:')?>
            <?= form::dropdown('department', $this->departments, $this->form['department']) ?>
            <span class="error"><?= $this->errors['department'] ?></span>
          </div>
          <div class="clearfix">
            <?= form::label('professor', 'Professor:')?>
            <?= form::dropdown('professor', $this->professors, $this->form['professor']) ?>
            <span class="error"><?= $this->errors['professor'] ?></span>
          </div>
          <div class="clearfix">
            <label>&nbsp;</label>
            <?= form::submit('search', 'Search') ?>
            <?php if ($this->search): ?>
              <?= html::anchor('files/study', 'clear search') ?>
            <?php endif ?>
          </div>
        <?= form::close() ?>
      </fieldset>
    </div>
  </div>
</div>