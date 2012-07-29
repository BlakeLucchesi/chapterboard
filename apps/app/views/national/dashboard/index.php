<div class="clearfix">
  <div class="split-left">
    <?= message::get(); ?>

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

    <div id="dashboard-forum" class="block">
      <div class="right">
        <?= html::anchor('forum/unread', 'View unread topics &raquo;') ?>
      </div>
      <h3 class="title">Recent Forum Topics</h3>
      <ul>
      <?php if ($this->topics->count()): ?>
        <?php foreach ($this->topics as $topic): ?>
          <li class="hoverable clearfix <?= $topic->is_new() ? 'new' : '' ?>">
            <span class="date">last comment <?= date::ago($topic->updated) ?></span>
            <span class="icon"></span>
            <div class="title"><?= html::anchor('forum/topic/'. $topic->id .'#new', ucfirst($topic->title)) ?></div>
            <div class="meta"><?= html::anchor('forum/'. $topic->forum->id, $topic->forum->title) ?></div>
          </li>
        <?php endforeach ?>
      <?php else: ?>
        <li>No recent forum topics available.</li>
      <?php endif ?>
      </ul>
    </div>
  </div>

  <div class="split-right">
    
    <div id="dashboard-service" class="announcements block clearfix">
      <h3 class="title">Community Service</h3>
      <table>
        <tr>
          <td><?= html::anchor('service', 'Chapter Totals') ?></td>
          <td class="right"><?= format::plural(number_format($this->service_total->hours, 1), '@count hour', '@count hours') ?></td>
          <td class="right"><?= money::display($this->service_total->dollars) ?></td>
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

    <?php if ($this->system_messages->count()): ?>
      <div id="dashboard-alerts" class="announcements block">
        <h3 class="title">ChapterBoard Announcements</h3>
        <ul>
        <?php foreach ($this->system_messages as $message): ?>
          <li class="hoverable"><b><?= date::display($message->created, 'M d') ?></b> &mdash; <?= $message->body ?> </li>
        <?php endforeach ?>
      </div>
    <?php endif ?>
  </div>
</div>