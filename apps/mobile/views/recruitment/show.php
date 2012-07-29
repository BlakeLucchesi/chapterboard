<div id="recruit">
  
  <!-- View Topic -->
  <div class="heading clearfix">
    <h2><?= $this->recruit->name ?> - <?= $this->recruit->list_name() ?></h2>
    <?php if (A2::instance()->allowed($this->recruit, 'admin')): ?>
      <div class="list-actions actions">
        <strong>Move To List:</strong>
        <ul>
        <?php if ($this->recruit->list == 0): ?>
          <li class="accept-bid"><?= html::anchor('recruitment/list/bidded/'. $this->recruit->id, 'Bidded Members') ?></li>
          <li class="not-recruiting"><?= html::anchor('recruitment/list/not_recruiting/'. $this->recruit->id, 'No Longer Recruiting') ?></li>
        <?php elseif ($this->recruit->list == 1): ?>
          <li class="rest"><?= html::anchor('recruitment/list/recruiting/'. $this->recruit->id, 'Actively Recruiting') ?></li>
          <li class="not-recruiting"><?= html::anchor('recruitment/list/not_recruiting/'. $this->recruit->id, 'No Longer Recruiting') ?></li>
        <?php else: ?>
          <li class="still-recruiting"><?= html::anchor('recruitment/list/recruiting/'. $this->recruit->id, 'Actively Recruiting') ?></li>
          <li class="still-recruiting"><?= html::anchor('recruitment/list/bidded/'. $this->recruit->id, 'Bidded Members') ?></li>
        <?php endif ?>
        </ul>
      </div>
      <?php if ($this->recruit->list == 1): ?>
        <div class="bid-actions actions">
          <strong>Change Bid Status:</strong>
          <ul>
          <?php if ($this->recruit->bid_status == 0): ?>
            <li class="accept-bid"><?= html::anchor('recruitment/bid/accepted/'. $this->recruit->id, 'Accepted') ?></li>
            <li class="not-recruiting"><?= html::anchor('recruitment/bid/declined/'. $this->recruit->id, 'Declined') ?></li>
          <?php elseif ($this->recruit->bid_status == 1): ?>
            <li class="rest"><?= html::anchor('recruitment/bid/pending/'. $this->recruit->id, 'Pending') ?></li>
            <li class="not-recruiting"><?= html::anchor('recruitment/bid/declined/'. $this->recruit->id, 'Declined') ?></li>
          <?php else: ?>
            <li class="still-recruiting"><?= html::anchor('recruitment/bid/pending/'. $this->recruit->id, 'Pending') ?></li>
            <li class="still-recruiting"><?= html::anchor('recruitment/bid/accepted/'. $this->recruit->id, 'Accepted') ?></li>
          <?php endif ?>
          </ul>
        </div>      
      <?php endif ?>
    <?php endif ?>
  </div>
  
  <?= message::get() ?>
  
  <div id="topic" class="view-topic">
    
    <div class="topic-header"><div class="topic-header-inner clearfix">
      <div class="author-name">
        Posted by: <?= $this->recruit->user->name() ?>
        <div class="post-date">On <?php print date::display($this->recruit->created, 'M d, Y g:i a')?></div>
      </div>
    </div></div>
    
    <div class="topic-body clearfix">
      
      <div class="meta clearfix">
        <!-- Photo -->
        <div class="photo">
          <?= theme::image('recruit_mobile', $this->recruit->picture()) ?>
        </div>

        <!-- Name + Details -->
        <div class="details">
          <div class="name"><b><?= $this->recruit->name(TRUE) ?></b></div>
          <?php if ($this->recruit->year): ?>
            <div class="year"><?= $this->recruit->year; ?></div>
          <?php endif ?>
          <?php if ($this->recruit->phone): ?>
            <div class="phone"><p><?= format::phone($this->recruit->phone(TRUE)) ?></p></div>
          <?php endif ?>
          <?php if ($this->recruit->email): ?>
            <div class="email"><p><?= html::email($this->recruit->email) ?></p></div>
          <?php endif ?>
          <?php if ($this->recruit->list == 1): ?>
            <div class="bid-status"><p>Bid Status: <strong><?= $this->recruit->bid_status() ?></strong></p></div>
          <?php endif ?>
          <?php if ($this->recruit->facebook): ?>
            <p><?= html::anchor($this->recruit->facebook, 'View on Facebook') ?></p>
          <?php endif ?>
        </div>
      </div>

      <div class="about"><div class="about-inner">
        <?= format::html($this->recruit->about); ?>
      </div></div>
    </div>
  </div>

  <!-- Comments -->
  <div id="comments">
    <?php foreach ($this->recruit->comments() as $comment): ?>
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
      </div>
    <?php endforeach ?>
  </div>
  
</div> <!-- /#Recruit-Show -->

<!-- Comment Form -->
<div id="comment-form">
  <h3>Add Comment</h3>
  <?= form::open('recruitment/show/'. $this->recruit->id, array('id' => 'comment-form-id')); ?>
  <?= form::textarea('body', $this->form['body']) ?>
  <?= form::submit('post', 'Post Comment'); ?>
  <?= form::close(); ?>
</div>