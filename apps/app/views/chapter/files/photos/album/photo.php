<?= message::get(); ?>

<div id="photos-fullview">
  <div class="photo-left">
    <div id="crumbs" class="heading clearfix">
      <div id="photo-navigation" class="right">
        <?php if ($this->previous_photo->loaded): ?>
          <?= html::anchor('files/photos/album/photo/'. $this->previous_photo->id, 'Previous', array('id' => 'previous-photo')) ?>
        <?php else: ?>
          <img src="/minis/resultset_previous_faded.png" />
        <?php endif ?>
        
        <?php if ($this->next_photo->loaded): ?>
          <?= html::anchor('files/photos/album/photo/'. $this->next_photo->id, 'Next', array('id' => 'next-photo')) ?>
        <?php else: ?>
          <img src="/minis/resultset_next_faded.png" />
        <?php endif ?>
      </div>
      <?= html::anchor('files/photos', 'Chapter Photos') ?> &raquo;
      <?= html::anchor('files/photos/album/'. $this->photo->album->id, $this->photo->album->title) ?> &raquo;
      <?= $this->album_order ?>
    </div>
    
    <div class="photo clearfix">
      <?php if ($this->next_photo->loaded): ?>
        <?= html::anchor('files/photos/album/photo/'. $this->next_photo->id, theme::image('large', $this->photo->filename)) ?>
      <?php else: ?>
        <?= theme::image('large', $this->photo->filename) ?>
      <?php endif; ?>
      <p>
        <em>Uploaded by <?= $this->photo->user->name(TRUE) ?> <?= date::ago($this->photo->created) ?></em>
        <?php if (A2::instance()->allowed($this->photo, 'delete')): ?>
          &mdash; <?= html::anchor('files/photos/delete/'. $this->photo->id, '[delete photo]') ?>
        <?php endif ?>
      </p>
    </div>
    
    <div id="comments">
      <?php foreach ($this->photo->comments() as $comment): ?>
        <div class="comment depth-<?= strlen(preg_replace('/\./', '', $comment->thread)) ?>">
          <div id="comment-<?= $comment->id ?>" class="comment-header"><div class="comment-header-inner clearfix">
            <div class="author-photo"><?= html::anchor('profile/'. $comment->user->id, theme::image('tiny', $comment->user->picture(), array('class' => 'userphoto'))); ?></div>
            <div class="author-name">
              <?= $comment->user->name(TRUE) ?>
              <div class="post-date">
                <em>On <?php print date::display($comment->created, 'M d, Y g:i a')?></em>
                <?php if ($comment->like_count): ?>
                · <span class="likes"><?= html::thickbox_anchor('votes/show/comment/'. $comment->id, format::plural($comment->like_count, '@count like', '@count likes')) ?></span>
                <?php else: ?>
                · <span class="likes"><?= format::plural($comment->like_count, '@count like', '@count likes') ?></span>
                <?php endif ?>
                · <?php if ($comment->liked): ?>
                    <?= html::anchor('files/photos/comment/unlike/'. $comment->id, 'Unlike') ?>
                  <?php else: ?>
                    <?= html::anchor('files/photos/comment/like/'. $comment->id, 'Like') ?>
                  <?php endif; ?>
              </div>
            </div>
            <div class="topic-links">
              <?php if (A2::instance()->allowed($comment, 'edit') || A2::instance()->allowed($this->site, 'admin')): ?>
                <?= html::anchor('files/photos/comment/edit/'. $comment->id, 'Edit Comment') ?>
              <?php endif ?>
              <?php if (A2::instance()->allowed($comment, 'delete') || A2::instance()->allowed($this->site, 'admin')): ?>
                <?= html::anchor('files/photos/comment/delete/'. $comment->id, 'Unpublish', array('class' => 'alert', 'title' => 'Are you sure you want to delete this comment?')) ?>
              <?php endif; ?>
            </div>
          </div></div>
          <div class="comment-body">
            <p><?= format::html($comment->body); ?></p>
          </div>
        </div>
      <?php endforeach ?>
    </div>
    
    
    <!-- Comment Form -->
    <div id="comment-form">
      <h3>Add Comment</h3>
      <?= form::open('files/photos/album/photo/'. $this->photo->id, array('id' => 'comment-form-id')); ?>
      <div class="clearfix">
        <?= form::textarea('body', $this->form['body']) ?>
        <?= form::submit('post', 'Post Comment'); ?>
      </div>
      <?= form::close(); ?>
    </div>
    
  </div><!-- .photo-left -->
  <div class="photo-right">

  </div><!-- .photo-right -->
</div>
