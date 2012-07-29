<div id="event">
  
  <h2><?= $this->title ?></h2>

  <?= message::get() ?>

  <div id="event-details" class="content block clear-block">
    <div class="meta clearfix">
      <div><?= $this->event->show_date(); ?></div>
      <?php if ( $this->event->location && ! $this->event->mappable): ?>
        <div><strong>Location:</strong> <?= $this->event->location ?></div>
      <?php endif ?>
      <div class="details">
        <?php if ($this->event->mappable): ?>
          <div class="event-map-links"><?= $this->event->location() ?></div>      
        <?php endif ?>
        
        <?= format::html($this->event->body) ?>
      </div>
    </div>
    <div id="view-comments">
      <p><a href="#comments">Skip to comments</a></p>
    </div>
  </div>


  <div id="signup-form">
    <?= form::open('events/signup') ?>
    <?= form::hidden('event_id', $this->event->id); ?>
    <label><?= form::radio('rsvp', 1, $this->signup->rsvp == 1 ? 1 : 0) ?> Attending</label>
    <label><?= form::radio('rsvp', 2, $this->signup->rsvp == 2 ? 2 : 0) ?> Not Attending</label>
    <?= form::submit('save', 'RSVP') ?>
    <?= form::close() ?>
  </div>
  
  <div id="signups" class="block members">
    <h3><?= format::plural($this->signups->count(), '@count member is going', '@count members are going') ?></h3>
    <div class="clearfix">
      <?php foreach ($this->signups as $signup): ?>
        <div class="member clearfix">
          <div class="member-name"><?= $signup->user->name(); ?></div>
          <div class="member-phone"><?= $signup->user->phone(TRUE); ?></div>
        </div>
      <?php endforeach ?>      
    </div>
  </div>


  <!-- Comments -->
  <div id="comments">
    <?php foreach ($this->event->comments() as $comment): ?>
      <div class="comment depth-<?= strlen(preg_replace('/\./', '', $comment->thread)) ?>">
        <div class="comment-header"><div class="comment-header-inner clearfix">
          <div class="author-photo"><?= html::anchor('profile/'. $comment->user->id, theme::image('tiny', $comment->user->picture(), array('class' => 'userphoto'))); ?></div>
          <div class="author-name">
            <?= $comment->user->name() ?>
            <div class="post-date">On <?php print date::display($comment->created, 'M d, Y g:i a')?></div>
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
                  <?= html::thickbox_anchor('file/original/'. $file->filename, $file->name) ?>
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
      <h3>Add Comment</h3>
      <?= form::open('events/'. $this->event->id, array('id' => 'comment-form-id')); ?>
      <div class="clearfix">
        <div class="clearfix">
          <?= form::textarea('body', $this->form['body']) ?>
        </div>
        <?= form::submit('post', 'Post Comment'); ?>
      </div>
      <?= form::close(); ?>
  </div>
  
</div>