<div id="event-detail">

  <div class="clearfix heading">
    <h2 class="title"><?= $this->event->title ?></h2>
    <?php if (A2::instance()->allowed($this->event, 'edit')): ?>
        <ul>
          <li><?= html::anchor('calendar/event/edit/'. $this->event->id, 'Edit Event') ?></li>
          <li><?= html::anchor('calendar/event/delete/'. $this->event->id, 'Delete Event', array('class' => 'alert', 'title' => 'Are you sure you want to delete this event?')) ?></li>
          <?php if ($this->event->repeats): ?>
            <li><?= html::anchor('calendar/event/delete_all/'. $this->event->id, 'Delete All Repeating Events', array('class' => 'alert', 'title' => 'Are you sure you want to delete this event and all related repeating events.')) ?></li>
          <?php endif ?>
        </ul>
    <?php endif ?>
  </div>
  
  <?= message::get() ?>
  
  <div class="clear-block">
  
    <!-- Left Side -->
    <div class="split-left">
      <div id="event-details" class="block clear-block">
        <div>Calendar: <?= $this->event->calendar->title ?></div>
        <div>Posted by: <?= $this->event->user->name(TRUE) ?></div>
        <div class="meta clearfix">
          <div><?= $this->event->show_date(); ?></div>
          <?php if ( $this->event->location && ! $this->event->mappable): ?>
            <div><strong>Location:</strong> <?= $this->event->location ?></div>
          <?php endif ?>
          <div class="details">
            <?php if ($this->event->mappable && $map = $this->event->map()): ?>
              <div class="event-map block">
                <?= $this->event->location("<img src='$map' />") ?>
                <?php if ($this->event->mappable): ?>
                  <div class="event-map-links">
                    <?= $this->event->location() ?>
                  </div>      
                <?php endif ?>
              </div>      
            <?php endif ?>
            
            <?= format::html($this->event->body) ?>
          </div>
        </div>
      </div>

      <!-- Comments -->
      <div id="comments">
        <?php foreach ($this->event->comments() as $comment): ?>
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
                      <?= html::anchor('calendar/comment/unlike/'. $comment->id, 'Unlike') ?>
                    <?php else: ?>
                      <?= html::anchor('calendar/comment/like/'. $comment->id, 'Like') ?>
                    <?php endif; ?>
                </div>
              </div>
              <div class="topic-links">
                <?php if (A2::instance()->allowed($comment, 'edit') || A2::instance()->allowed($this->event->calendar, 'admin')): ?>
                  <?= html::anchor('calendar/comment/edit/'. $comment->id, 'Edit Comment') ?>
                <?php endif ?>
                <?php if (A2::instance()->allowed($comment, 'delete') || A2::instance()->allowed($this->event->calendar, 'admin')): ?>
                  <?= html::anchor('calendar/comment/delete/'. $comment->id, 'Unpublish', array('class' => 'alert', 'title' => 'Are you sure you want to delete this comment?')) ?>
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
          <h3>Add Comment</h3>
          <?= form::open('calendar/event/'. $this->event->id, array('id' => 'comment-form-id')); ?>
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
  
    <!-- Right Side -->
    <div class="split-right">
      <div id="signup-form">
        <?= form::open('calendar/signups/toggle') ?>
        <?= form::hidden('event_id', $this->event->id); ?>
        <label><?= form::radio('rsvp', 1, $this->signup->rsvp == 1 ? 1 : 0) ?> Attending</label>
        <label><?= form::radio('rsvp', 2, $this->signup->rsvp == 2 ? 2 : 0) ?> Not Attending</label>
        <?= form::submit('save', 'RSVP') ?>
        <?= form::close() ?>
      </div>
      <div id="signups" class="block members clearfix sorting-<?= $this->sort ?>">
        <div class="sorting right"><label>Sort by:</label> <?= html::anchor(Router::$routed_uri .'?sort=name', 'Name', array('class' => $this->sort == 'name' ? 'active' : '')) ?> · <?= html::anchor(Router::$routed_uri .'?sort=timestamp', 'Signup Order', array('class' => $this->sort == 'timestamp' ? 'active' : '')) ?></div>
        <h3><?= format::plural($this->signups->count(), '@count member is going', '@count members are going') ?></h3>
        <?php foreach ($this->signups as $signup): ?>
          <?php $i++; ?>
          <div class="member clearfix">
            <div class="member-signup-number hidden">#<?= $i ?></div>
            <div class="member-photo">
              <?= theme::image('mini', $signup->user->picture()); ?>
            </div>
            <div class="member-info">
              <div class="member-name"><?= $signup->user->name(TRUE); ?></div>
              <div>
                <?php if ($this->sort == 'timestamp'): ?>
                  <span class="member-date"><?= $signup->created ? sprintf('%s · ', date::display($signup->created, 'M jS, Y g:ia')) : '' ?></span>
                <?php endif; ?>
                <span class="member-type"><?= $signup->user->type() ?></span>
                <span class="member-phone"><?= $signup->user->phone(); ?></span>
              </div>
            </div>
          </div>
        <?php endforeach ?>
      </div>
      <div id="not-attending" class="block members clearfix">
        <h3><?= format::plural($this->not_attending->count(), '@count member is not going', '@count members are not going') ?></h3>
        <?php foreach ($this->not_attending as $signup): ?>
          <div class="member clearfix">
            <div class="member-signup-number hidden"></div>
            <div class="member-photo">
              <?= theme::image('mini', $signup->user->picture()); ?>
            </div>
            <div class="member-info">
              <div class="member-name"><?= $signup->user->name(TRUE); ?></div>
              <div>
                <span class="member-type"><?= $signup->user->type() ?></span>
                <span class="member-phone"><?= $signup->user->phone(); ?></span>
              </div>
            </div>
          </div>
        <?php endforeach ?>
      </div>
    </div>
    
  </div>
</div>