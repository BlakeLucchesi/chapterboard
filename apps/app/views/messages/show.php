<div class="heading" id="crumbs">
  <?= html::anchor('messages', 'Inbox') ?> &raquo; <?= $this->title ?>
</div>

<?= message::get(); ?>

<div id="message-details" class="clearfix">
  <div class="split-left">
    <div id="view-topic">
      <!-- View Topic -->
      <div class="view-topic">
        <div class="topic-header"><div class="topic-header-inner clearfix">
          <div class="author-photo">
            <?= html::anchor('profile/'. $this->message->user->id, theme::image('tiny', $this->message->user->picture(), array('class' => 'userphoto'))); ?>
          </div>
          <h3><?= $this->message->subject ?></h3>
          <div class="author-name">
            <span class="post-date">On <?php print date::display($this->message->created, 'M d, Y g:i a')?></span> by <?= $this->message->user->name(TRUE) ?>
          </div>
          <div class="topic-links">
            <?= html::anchor('messages/unread/'. $this->message->id, 'Mark unread') ?>
          </div>
        </div></div>
        <div class="topic-body">
          <p><?= format::html($this->message->body); ?></p>
        </div>

        <?php if ($this->message->files->count()): ?>
          <div class="attachments">
            <h3>Attachments:</h3>
            <?php foreach ($this->message->files as $file): ?>
              <div class="file file-type-<?= $file->extension ?>">
                <?php if (in_array($file->extension, array('jpg', 'jpeg', 'png', 'gif'))): ?>
                  <?= html::thickbox_anchor('file/original/'. $file->filename, $file->name, array('rel' => 'message['. $this->message->id .']')) ?>
                <?php else: ?>
                  <?= html::anchor('file/original/'. $file->filename, $file->name) ?>
                <?php endif ?>
              </div>
            <?php endforeach ?>
          </div>
        <?php endif ?>
      </div>

      <!-- Comments -->
      <?php foreach ($this->message->comments() as $comment): ?>
        <div class="comment">
          <div id="comment-<?= $comment->id ?>" class="comment-header"><div class="comment-header-inner clearfix">
            <div class="author-photo"><?= html::anchor('profile/'. $comment->user->id, theme::image('tiny', $comment->user->picture(), array('class' => 'userphoto'))); ?></div>
            <div class="author-name">
              <?= $comment->user->name(TRUE) ?>
              <div class="post-date">
                <em>On <?php print date::display($comment->created, 'M d, Y g:i a')?></em>
              </div>
            </div>
            <div class="topic-links">
            </div>
          </div></div>
          <div class="comment-body">
            <p><?= format::html($comment->body); ?></p>
          </div>
        </div>
      <?php endforeach ?>

    </div> <!-- /#View-Topic -->

    <!-- Comment Form -->
    <div id="comment-form">
      <h3>Send Response</h3>
      <?= form::open('messages/show/'. $this->message->id, array('id' => 'comment-form-id')); ?>
      <div class="clearfix">
        <?= form::textarea('body', $this->form['body']) ?>
        <?= form::submit('post', 'Send Response'); ?>
        <!-- <div class="right form-help"><?= html::thickbox_anchor('help/general/content', 'Formatting guidelines.') ?></div> -->
      </div>
      <?= form::close(); ?>
    </div>
  </div> <!-- end .split-left -->
  
  <div class="split-right">
    <h3 class="title">Members involved in this conversation</h3>
    <div id="signups" class="clearfix members">
      <?php foreach ($this->message->users as $user): ?>
        <div class="member">
          <div class="member-photo"><?= theme::image('mini', $user->picture(), array('class' => 'userphoto')) ?></div>
          <div class="member-name"><?= $user->name(TRUE) ?></div>
          <div>
            <span class="member-type"><?= $user->type() ?></span>
            <span class="member-phone"><?= $user->phone() ?></span>
          </div>
        </div>
      <?php endforeach ?>
    </div>
  </div>
</div>
