<div id="recruit-show">

  <div id="breadcrumbs">
    <?= html::anchor('recruitment', 'Recruitment Lists') ?> &raquo; <?= html::anchor('recruitment/'. $this->recruit->list_name(TRUE), $this->recruit->list_name()) ?> &raquo; <?= $this->recruit->name ?>
  </div>

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

  <!-- View Topic -->
  <div class="heading clearfix">
    <h2><?= $this->recruit->name ?></h2>
  </div>
  
  <?= message::get() ?>
  
  <div class="view-topic">
    <div class="topic-header"><div class="topic-header-inner clearfix">
      <div class="author-photo">
        <?= html::anchor('profile/'. $this->recruit->user->id, theme::image('tiny', $this->recruit->user->picture(), array('class' => 'userphoto'))); ?>
      </div>
      <div class="author-name">
        Posted by: <?= $this->recruit->user->name(TRUE) ?>
        <div class="post-date">On <?php print date::display($this->recruit->created, 'M d, Y g:i a')?></div>
      </div>

      <div class="topic-links">
        <?php if (A2::instance()->allowed($this->recruit, 'edit')): ?>
          <?= html::anchor('recruitment/edit/'. $this->recruit->id, 'Edit Recruit') ?>
        <?php endif ?>
        <?php if (A2::instance()->allowed($this->recruit, 'delete')): ?>
          <?= html::anchor('recruitment/delete/'. $this->recruit->id, 'Unpublish', array('class' => 'alert', 'title' => 'Are you sure you want to delete this recruit?')) ?>
        <?php endif ?>
      </div>
    </div></div>
    <div class="topic-body clearfix">
      <div class="meta clearfix">
        <!-- Photo -->
        <div class="recruit-photo">
          <?= theme::image('recruit', $this->recruit->picture()) ?>
          <?php if ($this->recruit->facebook): ?>
            <div class="facebook"><?= html::anchor($this->recruit->facebook, 'Facebook Profile') ?></div>
          <?php endif ?>

        </div>
        
        <!-- Name + Details -->
        <div class="name"><h3><?= $this->recruit->name(TRUE) ?></h3></div>
        <?php if ($this->recruit->year || $this->recruit->major): ?>
          <div class="year"><em><?= $this->recruit->year ?><?= $this->recruit->year && $this->recruit->major ? ',' : ''; ?> <?= $this->recruit->major ?></em></div>
        <?php endif ?>
        <?php if ($this->recruit->phone): ?>
          <div class="phone"><?= format::phone($this->recruit->phone) ?></div>
        <?php endif ?>
        <?php if ($this->recruit->email): ?>
          <div class="email"><?= html::email($this->recruit->email) ?></div>
        <?php endif ?>
        <?php if ($this->recruit->hometown || $this->recruit->high_school): ?>
          <div class="meta-extra">
          <?php if ($this->recruit->hometown): ?>
            <div class="hometown">Hometown: <?= $this->recruit->hometown ?></div>
          <?php endif ?>
          <?php if ($this->recruit->high_school): ?>
            <div class="high-school">H.S.: <?= $this->recruit->high_school ?></div>
          <?php endif ?>
          </div>
        <?php endif ?>
        <?php if ($this->recruit->list == 1): ?>
          <div class="bid-status">Bid Status: <strong><?= $this->recruit->bid_status() ?></strong></div>
        <?php endif ?>
        
        <div id="recruit-voting">
          <?php if ($this->recruit->is_liked()): ?>
            <div class="count">
              <?php if ($this->recruit->like_count == 1): ?>
                <?= html::thickbox_anchor('votes/show/recruit/'. $this->recruit->id, Kohana::lang('recruitment.good_fit.voted')) ?>
              <?php elseif ($this->recruit->like_count == 0): ?>
                <?= format::plural($this->recruit->like_count - 1, Kohana::lang('recruitment.good_fit.voted_singular'), Kohana::lang('recruitment.good_fit.voted_plural')) ?>
              <?php else: ?>
                <?= html::thickbox_anchor('votes/show/recruit/'. $this->recruit->id, format::plural($this->recruit->like_count - 1, Kohana::lang('recruitment.good_fit.voted_singular'), Kohana::lang('recruitment.good_fit.voted_plural'))) ?>                  
              <?php endif ?>
            </div>
            <div><?= html::anchor('recruitment/vote/remove/'. $this->recruit->id, 'Remove my vote') ?></div>
          <?php else: ?>
            <div class="count">
              <?php if ($this->recruit->like_count == 0): ?>
                <?= format::plural($this->recruit->like_count, Kohana::lang('recruitment.good_fit.singular'), Kohana::lang('recruitment.good_fit.plural')) ?>
              <?php else: ?>
                <?= html::thickbox_anchor('votes/show/recruit/'. $this->recruit->id, format::plural($this->recruit->like_count, Kohana::lang('recruitment.good_fit.singular'), Kohana::lang('recruitment.good_fit.plural'))) ?>
              <?php endif ?>
            </div>
            <span class="button"><?= html::anchor('recruitment/vote/up/'. $this->recruit->id, Kohana::lang('recruitment.good_fit.button')) ?></span>
          <?php endif ?>
        </div>
      </div>

      <div class="about"><div class="about-inner">
        <?= format::html($this->recruit->about); ?>
      </div></div>
    </div>
  </div>

  <!-- Comments -->
  <?php foreach ($this->recruit->comments() as $comment): ?>
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
                <?= html::anchor('recruitment/comment/unlike/'. $comment->id, 'Unlike') ?>
              <?php else: ?>
                <?= html::anchor('recruitment/comment/like/'. $comment->id, 'Like') ?>
              <?php endif; ?>
          </div>
        </div>
        <div class="topic-links">
          <?php if (A2::instance()->allowed($comment, 'edit') || A2::instance()->allowed($this->recruit, 'admin')): ?>
            <?= html::anchor('recruitment/comment/edit/'. $comment->id, 'Edit Comment') ?>
          <?php endif ?>
          <?php if (A2::instance()->allowed($comment, 'delete') || A2::instance()->allowed($this->recruit, 'admin')): ?>
            <?= html::anchor('recruitment/comment/delete/'. $comment->id, 'Unpublish', array('class' => 'delete alert', 'title' => 'Are you sure you want to delete this comment?')) ?>
          <?php endif; ?>
        </div>
      </div></div>
      <div class="comment-body">
        <p><?= format::html($comment->body); ?></p>
      </div>
    </div>
  <?php endforeach ?>
  
</div> <!-- /#Recruit-Show -->

<!-- Comment Form -->
<div id="comment-form">
  <h3>Add Comment</h3>
  <?= form::open('recruitment/show/'. $this->recruit->id, array('id' => 'comment-form-id')); ?>
  <div class="clearfix">
    <div class="split-left">
      <div class="clearfix">
        <?php echo form::textarea('body', $this->form['body']) ?>
        <?= form::submit('post', 'Post Comment'); ?>
        <!-- <div class="right form-help"><?= html::thickbox_anchor('help/general/content', 'Formatting guidelines.') ?></div> -->
      </div>
    </div>
    <div class="split-right">
    </div>
  </div>
  <?php echo form::close(); ?>
</div>