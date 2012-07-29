<?= $this->user->help('home') ?>

<div class="clearfix">
  <div class="split-left">
    <?= message::get(); ?>

    <div id="dashboard-announcements" class="announcements block">
      <div class="clearfix">
        <?php if (A2::instance()->allowed('announcement', 'manage')): ?>
          <div class="post-announcement right"><?= html::anchor('announcements/add', '+ Post new announcement') ?></div>
        <?php endif ?>
        <h3 class="title">Chapter Announcements</h3>
      </div>
      <ul>
        <?php if (count($this->announcements)): ?>
          <?php foreach ($this->announcements as $announcement): ?>
            <li class="hoverable clearfix">
              <span class="icon"></span>
              <span class="date">posted <?= date::ago($announcement->created) ?></span>
              <div class="title"><?= html::thickbox_anchor('announcements/show/'. $announcement->id, $announcement->title) ?></div>
            </li>
          <?php endforeach ?>
        <?php else: ?>
          <li><span class="icon"></span><div class="title">No announcements.</div></li>
        <?php endif ?>
      </ul>
    </div>
    <?php if ($this->events): ?>
      <div id="dashboard-events" class="block">
        <div class="right">
          <?= html::anchor('calendar', 'View calendar &raquo;') ?>
        </div>
        <h3 class="title">Upcoming Events</h3>
        <ul>
          <?php foreach ($this->events as $event): ?>
            <li class="hoverable clearfix">
              <span class="time"><?= $event->all_day ? 'all day' : 'starts at '. date::time($event->start) ?></span>
              <div class="date"><?= date::display($event->start, 'D') ?></div>
              <div class="title"><?= html::anchor('calendar/event/'. $event->id, $event->title) ?></div>
              <div class="attendees">
                <?= $event->attendees() ?>
              </div>
            </li>
          <?php endforeach ?>
        </ul>
      </div>
    <?php endif ?>
    
    <?php if (A2::instance()->allowed('recruit', 'access') && $this->recruits->count()): ?>
      <div id="dashboard-recruits" class="block">
        <div class="right">
          <?= html::anchor('recruitment', 'View all &raquo;') ?>
        </div>
        <h3 class="title">Recently Updated Recruits</h3>
        <ul>
          <?php foreach ($this->recruits as $recruit): ?>
            <li class="hoverable clearfix">
              <span class="date"><?= $recruit->comment_count ? 'updated' : 'added' ?> <?= date::ago($recruit->updated) ?></span>
              <div class="photo"><?= theme::image('small', $recruit->picture()) ?></div>
              <div class="title"><strong><?= html::anchor('recruitment/show/'. $recruit->id, $recruit->name()) ?></strong></div>
              <div class="meta">
                <?= $recruit->year ?> <?= ($recruit->year && $recruit->phone) ? '·' : '' ?> <?= format::phone($recruit->phone) ?>
              </div>
            </li>
          <?php endforeach ?>
        </ul>
      </div>
    <?php endif ?>

    <div id="dashboard-forum" class="block">
      <div class="right">
        <?= html::anchor('forum/topic/add', '+ Post New Topic') ?>
      </div>
      <h3 class="title">Recent Forum Topics</h3>
      <ul>
      <?php if ($this->topics->count()): ?>
        <?php foreach ($this->topics as $topic): ?>
          <li class="hoverable clearfix <?= $topic->is_new() ? 'new' : '' ?>">
            <span class="date">last comment <?= date::ago($topic->updated) ?></span>
            <span class="icon"></span>
            <div class="title"><?= html::anchor('forum/topic/'. $topic->id .'#new', ucfirst($topic->title)) ?></div>
            <div class="meta">In <?= html::anchor('forum/'. $topic->forum->id, $topic->forum->title) ?></div>
          </li>
        <?php endforeach ?>
        <li class="clearfix">
          <div class="right"><small><?= html::anchor('forum/unread', 'All unread topics &raquo;') ?></small></div>
        </li>
      <?php else: ?>
        <li>No recent forum topics. <?= html::anchor('forum/topic/add', 'Post a new topic') ?>.</li>
      <?php endif ?>
      </ul>
    </div>
  </div>

  <div class="split-right">
    <?php if (count($this->site->users) == 1): ?>
      <div class="announcements block">
        <h3 class="title">Getting Started</h3>
        <p>Now that you've created your chapter account, the next step is to start inviting your members. To do so, visit the "Members" section and click on <?= html::anchor('members/invite', '"Invite Members"') ?>.</p>
      </div>
    <?php endif ?>
    
    <?php if ($this->outstanding_balance > 0): ?>
      <div id="dashboard-finances" class="announcements block">
        <h3 class="title">Finances</h3>
        <ul>
          <li>
            Your balance: <?= money::display($this->outstanding_balance) ?>
            <?php if ($this->site->collections_enabled()): ?>
              &mdash; <?= html::anchor('finances/payment', 'Pay online now') ?>
            <?php endif ?>
          </li>
        </ul>
      </div>    
    <?php endif ?>
    
    <?php if ($this->campaigns->count() > 0): ?>
      <div id="dashboard-campaigns" class="announcements block clearfix">
        <?php if (A2::instance()->allowed('campaign', 'manage')): ?>
          <div class="right"><?= html::anchor('finances/fundraising', 'Manage campaigns &raquo;') ?></div>
        <?php endif ?>
        <h3 class="title">Fundraising Campaigns</h3>
        <table>
          <?php foreach ($this->campaigns as $campaign): ?>
            <tr>
              <td><?= html::anchor($campaign->url(), text::limit_chars($campaign->title, 40), array('target' => '_blank')) ?></td>
              <td class="right">
                <?= money::display($campaign->campaign_total) ?><br />
                <?php if ($campaign->goal): ?>
                   <label>GOAL:</label> <?= money::display($campaign->goal) ?>
                <?php endif ?>
              </td>
            </tr>
          <?php endforeach ?>
        </table>
      </div>
    <?php endif ?>
    
    <div id="dashboard-service" class="announcements block clearfix">
      <div class="right"><?= html::anchor('service/record', 'Record service &raquo;') ?></div>
      <h3 class="title">Community Service</h3>
      <table>
        <tr>
          <td><?= html::anchor('service/members', 'Chapter Total') ?></td>
          <td class="right"><?= format::plural(number_format($this->service_chapter_total->hours, 1), '@count hour', '@count hours') ?></td>
          <td class="right"><?= money::display($this->service_chapter_total->dollars) ?></td>
        </tr>
        <tr>
          <td><?= html::anchor('service', 'My Contribution') ?></td>
          <td class="right"><?= format::plural(number_format($this->service_member_total->hours, 1), '@count hour', '@count hours') ?></td>
          <td class="right"><?= money::display($this->service_member_total->dollars) ?></td>
        </tr>
      </table>
    </div>

    <?php if ($this->birthdays->count()): ?>
      <div id="dashboard-alerts" class="announcements block">
        <h3 class="title">Upcoming Birthdays</h3>
        <ul>
          <?php foreach ($this->birthdays as $member): ?>
            <li><b><?= $member->birthday('M j') ?></b> &mdash; <?= $member->name(TRUE) ?> <em><?= $member->birthday('age') ?></em></li>
          <?php endforeach ?>
        </ul>
      </div>
    <?php endif; ?>

    <?php if ($this->photos->count()): ?>
      <div id="dashboard-photo" class="announcements block">
        <div class="right"><?= html::anchor('files/photos', 'Browse albums &raquo;') ?></div>
        <h3 class="title">Recent Photos</h3>
        <div class="clearfix photos">
          <?php foreach ($this->photos as $photo): ?>
            <div class="photo hoverable col-<?= text::alternate(1,2,3,4,5) ?>">
              <?= html::anchor('files/photos/album/photo/'. $photo->id, theme::image('small', $photo->filename)) ?>
            </div>
          <?php endforeach ?>
        </div>
      </div>  
    <?php endif ?>

    <?php if ($this->system_messages->count()): ?>
      <div id="dashboard-alerts" class="announcements block">
        <h3 class="title">ChapterBoard Announcements</h3>
        <ul>
        <?php foreach ($this->system_messages as $message): ?>
          <li class="hoverable"><b><?= date::display($message->created, 'M d') ?></b> &mdash; <?= $message->body ?> </li>
        <?php endforeach ?>
      </div>
    <?php endif ?>

    <?php if ( ! $this->site->slug && $this->user->has_role('admin')): ?>
      <div class="announcements block">
        <h3 class="title">Action Required</h3>
        <ul>
          <li>Please <?= html::anchor('settings', 'set your chapter nickname') ?>.</li>
        </ul>
      </div>
    <?php endif ?>
  </div>
</div>