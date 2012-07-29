<div id="comment-edit">

  <?= message::get(); ?>

  <!-- Comment Form -->
  <div id="comment-form">
      <h3>Edit Comment</h3>
      <?= form::open(Router::$current_uri, array('id' => 'comment-form-id')); ?>
      
      <div class="clearfix">
        <div class="split-left">
          <div class="clearfix">
            <?= form::textarea('body', $this->form['body']) ?>
            <?= form::submit('post', 'Save Comment'); ?> or <?= html::anchor('forum/topic/'. $this->topic->id, 'Cancel') ?>
            <!-- <div class="right form-help"><?= html::thickbox_anchor('help/general/content', 'Formatting guidelines.') ?></div> -->
          </div>
        </div>
      </div>

      <?= form::close(); ?>
  </div>

  <div class="heading clearfix">
    <h2>Original Comment</h2>
  </div>
  <!-- Original Comment -->
  <div class="comment depth-<?= strlen(preg_replace('/\./', '', $this->comment->thread)) ?>">
    <div class="comment-header"><div class="comment-header-inner clearfix">
      <div class="author-photo"><?= html::anchor('profile/'. $this->comment->user->id, theme::image('tiny', $this->comment->user->picture(), array('class' => 'userphoto'))); ?></div>
      <div class="author-name">
        <?= $this->comment->user->name(TRUE) ?>
        <div class="post-date">On <?php print date::display($this->comment->created, 'M d, Y g:i a')?></div>
      </div>
      <div class="topic-links">
        <?php if (A2::instance()->allowed($comment, 'delete') || A2::instance()->allowed($this->topic->forum, 'admin')): ?>
          <?= html::anchor('forum/comment/delete/'. $this->comment->id, 'Unpublish') ?>
        <?php endif; ?>
      </div>
    </div></div>
    <div class="comment-body">
      <p><?= format::html($this->comment->body); ?></p>
    </div>
    <?php if ($this->comment->files->count()): ?>
      <div class="attachments">
        <h3>Attachments:</h3>
        <?php foreach ($this->comment->files as $file): ?>
          <div class="file file-type-<?= $file->extension ?>">
            <?php if (in_array($file->extension, array('jpg', 'jpeg', 'png', 'gif'))): ?>
              <?= html::thickbox_anchor('file/original/'. $file->filename, $file->name) ?> - <?= html::anchor('upload/remove', 'Remove', array('class' => 'remove-file', 'filehash' => upload::filehash($file->filepath))) ?>
            <?php else: ?>
              <?= html::anchor('file/original/'. $file->filename, $file->name) ?> - <?= html::anchor('upload/remove', 'Remove', array('class' => 'remove-file', 'filehash' => upload::filehash($file->filepath))) ?>
            <?php endif ?>
          </div>
        <?php endforeach ?>
      </div>
    <?php endif ?>
  </div>

</div>

