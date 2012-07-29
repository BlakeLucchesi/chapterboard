<div id="profile">
  <div class="heading clearfix">
    <h2><?= $this->title; ?></h2>
    <?php if (A2::instance()->allowed($this->account, 'edit')): ?>
      <ul>
        <li><?= html::anchor('profile/edit/'. $this->account->id, 'Edit Profile') ?></li>
      </ul>
    <?php endif ?>
  </div>
  
  <?= message::get() ?>

  <div class="clearfix">
    
    <div class="split-left">
      <fieldset class="clearfix">
        <div class="profile-left">
          <div class="photo"><?= theme::image('profile', $this->account->picture()); ?></div>
        </div>

        <div class="info">
          <div class="block">
            <h3><?= $this->account->name() ?></h3>
            <div><?= $this->account->birthday(); ?></div>
            <div><?= format::phone($this->account->profile->phone); ?></div>
            <div><?= html::mailto($this->account->email()); ?></div>
          </div>

          <div class="block">
            <?php if ($this->account->profile->department): ?>
              <div><b>School:</b> <?= $this->account->profile->department ?></div>
            <?php endif ?>
            <?php if ($this->account->profile->major): ?>
              <div><b>Major:</b> <?= $this->account->profile->major ?></div>
            <?php endif ?>
            <?php if ($this->account->profile->student_id && A2::instance()->allowed($this->account, 'edit')): ?>
              <div><b>Student ID:</b> <?= $this->account->profile->student_id ?></b></div>
            <?php endif ?>
            <?php if ($this->account->profile->school_year): ?>
              <div><b>School Year:</b> <?= $this->account->profile->school_year ?></div>
            <?php endif ?>
            <?php if ($this->account->shirt_size()): ?>
              <div><b>Shirt Size:</b> <?= $this->account->shirt_size(); ?></div>
            <?php endif ?>
            <?php if ($this->account->initiated_in()): ?>
              <div><b>Initiation Year:</b> <?= $this->account->initiated_in() ?></div>
            <?php endif ?>            
          </div>

          <?php if ($address = $this->account->profile->school_address()): ?>
            <div class="block">
              <h4>School Address:</h4>
              <div><?= $address ?></div>
            </div>
          <?php endif ?>
          
          <?php if ($address = $this->account->profile->home_address()): ?>
            <div class="block">
              <h4>Home/Permanent Address:</h4>
              <div><?= $address ?></div>
            </div>
          <?php endif ?>

          <div class="block">
            <h4>Emergency Contacts</h4>
            <div><b><?= $this->account->profile->emergency1_name ?></b> - <?= format::phone($this->account->profile->emergency1_phone) ?></div>
            <div><b><?= $this->account->profile->emergency2_name ?></b> - <?= format::phone($this->account->profile->emergency2_phone) ?></div>
          </div>

        </div>
      </fieldset>
    </div>
    
    <div id="tabs" class="split-right">
      <ul id="activity-tabs" class="tabs clearfix">
        <li><a href="#activity-topics" id="tab-topic">Recent Topics</a></li>
        <li><a href="#activity-comments" id="tab-comments">Recent Comments</a></li>
        <li><a href="#activity-events" id="tab-events">Recent Events</a></li>
      </ul>
      
      <div id="tab_content">
        <div id="activity-topics" class="tab_content">
          <?php if ($this->topics->count()): ?>
            <?php foreach ($this->topics as $topic): ?>
              <?php if ($this->acl->allowed($topic->forum, 'view')): ?>
                <?php $topics = TRUE; ?>
                <div class="item">
                  <div class="title"><?= html::anchor('forum/topic/'. $topic->id, text::limit_chars($topic->title, 55)) ?></div>
                  <div class="meta"><?= date::display($topic->created, 'M j, Y') ?> | <?= $topic->comment_count ?> comments</div>
                </div>
              <?php endif ?>
            <?php endforeach ?>
          <?php endif; ?>
          <?php if ( ! $topics): ?>
            <div class="item">
              <div class="title">This member has not posted any topics.</div>
            </div>
          <?php endif ?>
        </div>
        
        <div id="activity-comments" class="tab_content">
          <?php if ($this->comments->count()): ?>
            <?php foreach ($this->comments as $comment): ?>
              <?php if ($comment->allowed()): ?>
                <?php $comments = TRUE; ?>
                <div class="item">
                  <div class="title"><?= html::anchor($comment->link(), text::limit_chars($comment->title(), 55)) ?></div>
                  <div class="meta"><?= date::display($comment->created, 'M j, Y') ?> | <?= ucwords($comment->object_type) ?> by <?= $comment->node->user->name(TRUE) ?></div>
                </div>
              <?php endif ?>
            <?php endforeach ?>
          <?php endif; ?>
          <?php if ( ! $comments): ?>
            <div class="item">
              <div class="title">This member has not commented on anything.</div>
            </div>
          <?php endif ?>
        </div>
        
        <div id="activity-events" class="tab_content">
          <?php if ($this->events->count()): ?>
            <?php foreach ($this->events as $event): ?>
              <?php if ($this->acl->allowed($event->calendar, 'view')): ?>
                <?php $events = TRUE; ?>
                <div class="item">
                  <div class="title"><?= html::anchor('calendar/event/'. $event->id, text::limit_chars($event->title, 55)) ?></div>
                  <div class="meta"><?= $event->show_date() ?> | <?= format::plural($event->signups->count(), '@count signup', '@count signups') ?></div>
                </div>
              <?php endif ?>
            <?php endforeach ?>  
          <?php endif; ?>
          <?php if ( ! $events): ?>
            <div class="item">
              <div class="title">This member has not added any events.</div>
            </div>
          <?php endif ?>
        </div>
      </div>
    </div>
  </div>
</div>