<div id="dashboard">
  
  <!-- Announcements -->
  <!-- <?php if (count($this->announcements)): ?>
    <div id="announcements" class="block">
      <h2 class="title">Chapter Announcements</h2>
      <div class="items">
        <?php foreach ($this->announcements as $announcement): ?>
          <div class="item boxed">
            <div class="title"><?= html::thickbox_anchor('announcements/show/'. $announcement->id, $announcement->title) ?></div>
            <div class="meta">posted <?= date::ago($announcement->created) ?></div>
          </div>
        <?php endforeach ?>
      </di>
    </div>
  <?php endif ?> -->

  <!-- Events -->
  <?php if ($this->events): ?>
    <div id="upcoming-events" class="block">
      <div class="right"><?= html::anchor('events', 'Calendar') ?></div>
      <h2 class="title">Upcoming Events</h2>
      <div class="items">
        <?php foreach ($this->events as $event): ?>
          <div class="item boxed">
            <div class="meta"><?= html::anchor('events/'. $event->id, $event->title) ?></div>
            <div class="title"><?= date::display($event->start, 'D') ?> <span class="time"><?= $event->all_day ? 'all day' : 'at '. date::time($event->start) ?></span></div>          
          </div>
        <?php endforeach ?>
      </div>
    </div>
  <?php endif ?>

  <!-- Forum -->
  <div id="forum" class="block">
    <div class="right"><?= html::anchor('forum/unread', 'Unread Topics') ?></div>
    <h2 class="title">Recent Topics</h2>
    <div class="items">
    <?php if ($this->topics->count()): ?>
      <?php foreach ($this->topics as $topic): ?>
        <div class="item boxed <?= $topic->is_new() ? 'new' : 'read' ?>">
          <div class="title"><?= html::anchor('forum/topic/'. $topic->id, ucfirst($topic->title)) ?></div>
          <div class="meta date"><?= date::ago($topic->updated) ?> by <?= $topic->last_comment->user->name() ?></div>
        </div>
      <?php endforeach ?>
    <?php else: ?>
      <div class="item">
        <div class="title">No recent forum topics available.</div>
      </div>
    <?php endif ?>
    </div>
  </div>

</div>