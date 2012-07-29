<div id="event-detail">
  
  <div class="clearfix heading">
    <div class="right">
      <p>* Files and comments are listed newest first.</p>
    </div>
    <h2 class="title"><?= $this->title ?></h2>
    <?php if ($this->is_admin): ?>
        <ul>
          <li><?= html::anchor('files/study/course/edit/'. $this->course->id, 'Edit Course') ?></li>
          <li><?= html::anchor('files/study/course/delete/'. $this->course->id, 'Delete Course', array('class' => 'alert', 'title' => 'Are you sure you want to delete this course?')) ?></li>
        </ul>
    <?php endif ?>
  </div>
  
  <?= message::get() ?>
  
  <div class="clear-block">
    <!-- Left Side -->
    <div class="split-left">
      <div class="block clear-block">
        <div class="clearfix"><label>Course Code:</label> <?= $this->course->code ?></div>
        <div class="clearfix"><label>Department:</label> <?= $this->course->department ?></div>
        <div class="clearfix"><label>Professor:</label> <?= $this->course->professor ?></div>
        <div class="clearfix"><label>Posted by:</label> <?= $this->course->user->name(TRUE) ?></div>
        <?php if ($this->course->description): ?>
          <strong>Course Description/Details:</strong><br />
          <?= format::html($this->course->description) ?>
        <?php endif ?>
      </div>      
    </div>
  
    <!-- Right Side -->
    <div class="split-right">    
      <!-- Comments -->
      <div id="comments">
        <?php foreach ($this->course->comments() as $comment): ?>
          <div class="comment depth-<?= strlen(preg_replace('/\./', '', $comment->thread)) ?>">
            <div id="comment-<?= $comment->id ?>" class="comment-header"><div class="comment-header-inner clearfix">
              <div class="author-photo"><?= html::anchor('profile/'. $comment->user->id, theme::image('tiny', $comment->user->picture(), array('class' => 'userphoto'))); ?></div>
              <div class="author-name">
                <?= $comment->user->name(TRUE) ?>
                <div class="post-date">
                  On <?php print date::display($comment->created, 'M d, Y g:i a')?>
                  <?php if ($comment->like_count): ?>
                  · <span class="likes"><?= html::thickbox_anchor('votes/show/comment/'. $comment->id, format::plural($comment->like_count, '@count like', '@count likes')) ?></span>
                  <?php else: ?>
                  · <span class="likes"><?= format::plural($comment->like_count, '@count like', '@count likes') ?></span>
                  <?php endif ?>
                  · <?php if ($comment->liked): ?>
                      <?= html::anchor('files/study/comment/unlike/'. $comment->id, 'Unlike') ?>
                    <?php else: ?>
                      <?= html::anchor('files/study/comment/like/'. $comment->id, 'Like') ?>
                    <?php endif; ?>
                </div>
              </div>
              <div class="topic-links">
                <?php if ($this->is_admin || A2::instance()->allowed($comment, 'edit')): ?>
                  <?= html::anchor('files/study/comment/edit/'. $comment->id, 'Edit Comment') ?>
                <?php endif ?>
                <?php if ($this->is_admin || A2::instance()->allowed($comment, 'delete')): ?>
                  <?= html::anchor('files/study/comment/delete/'. $comment->id, 'Unpublish', array('class' => 'alert', 'title' => 'Are you sure you want to delete this comment?')) ?>
                <?php endif; ?>
              </div>
            </div></div>
            <div class="comment-body">
              <p><?= format::html($comment->body); ?></p>
            </div>
            <?php if ($comment->files->count()): ?>
              <div class="attachments">
                <h3>Attachments:</h3>
                <?php foreach ($comment->files as $file): ?>
                  <div class="file file-type-<?= $file->extension ?>">
                    <?php if (in_array($file->extension, array('jpg', 'jpeg', 'png', 'gif'))): ?>
                      <?= html::thickbox_anchor('file/original/'. $file->filename, $file->name, array('rel' => 'comment['. $comment->id .']')) ?>
                    <?php else: ?>
                      <?= html::anchor('file/original/'. $file->filename, $file->name) ?>
                    <?php endif ?>
                  </div>
                <?php endforeach ?>
              </div>
            <?php endif ?>
          </div>
        <?php endforeach ?>
      </div>
      
      <!-- Comment Form -->
      <div id="comment-form">
          <h3>Leave a Comment and Upload Course Files</h3>
          <?= form::open('files/study/course/'. $this->course->id, array('id' => 'comment-form-id')); ?>
          <div class="clearfix">
            <div class="clearfix">
              <?= form::textarea('body', $this->form['body']) ?>
            </div>
            
            <div id="attachment" class="block">
              <?= form::hidden('key', $this->form['key']) ?>
              <div class="clearfix">
                <h3>Attachments</h3>
              </div>
              <div id="upload-wrapper" class="clearfix">
                <div id="upload-status"><?= html::image('images/loadingAnimation.gif') ?></div>
                <div id="upload-form"><?= form::upload(array('name' => 'attach')); ?> <?= form::button('upload', 'Attach', 'class="unbound"') ?> <!-- <em><?= html::thickbox_anchor('help/general/upload', 'File upload help.') ?></em> --></div>
              </div>
              <div id="attachments" class="attachments" class="uploaded-files pp_attachment">
                <?php if ($this->upload_error): ?>
                  <div class="upload-error"><?= $this->upload_error ?></div>
                <?php endif ?>
                <?php foreach ($this->uploads as $upload): ?>
                  <div class="file file-type-<?= $upload['extension'] ?>">
                    <?= $upload['name'] ?>
                  </div>
                <?php endforeach ?>
              </div>
            </div>
            <?= form::submit('post', 'Post Comment'); ?>
          </div>
          <?= form::close(); ?>
      </div>
    </div>
  </div>
</div>