<div id="comment-edit">
  <div id="breadcrumbs">
    <div>
      <?= html::anchor('files', 'Files') ?> &raquo;
      <?= html::anchor('files/photos', 'Chapter Photos') ?> &raquo;
      <?= html::anchor('files/photos/album/'. $this->photo->album->id, $this->photo->album->title) ?> &raquo;
      <?= html::anchor('files/photos/album/photo/'. $this->photo->id, 'Photo') ?> &raquo;
      Edit Comment
    </div>    
  </div>

  <?= message::get(); ?>

  <!-- Comment Form -->
  <div id="comment-form">
    <h3>Edit Comment</h3>
    <?= form::open(Router::$current_uri, array('id' => 'comment-form-id')); ?>
    <?= form::hidden('key', $this->form['key']) ?>
    <div class="clearfix">
      <div class="split-left">
        <?= form::textarea('body', $this->form['body']) ?>
        <?= form::submit('post', 'Save Comment'); ?> or <?= html::anchor('files/photos/album/photo/'. $this->photo->id, 'Cancel') ?>
        <!-- <div class="right form-help"><?= html::thickbox_anchor('help/general/content', 'Formatting guidelines.') ?></div> -->
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
        <?php if (A2::instance()->allowed($comment, 'delete') || A2::instance()->allowed($this->site, 'admin')): ?>
          <?= html::anchor('files/photos/comment/delete/'. $this->comment->id, 'Unpublish') ?>
        <?php endif; ?>
      </div>
    </div></div>
    <div class="comment-body">
      <p><?= format::html($this->comment->body); ?></p>
    </div>
  </div>

</div>

