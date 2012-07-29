<div id="signups" class="<?= $this->event->rsvp ? 'rsvp' : 'signup' ?>">
  <div class="heading clearfix">
    <div class="right"><?= html::thickbox_anchor('calendar/event/'. $this->event->id, '&laquo; Back to Event Details') ?></div>
    <h3><?= $this->title ?></h3>
  </div>

  <div class="members">
    <?php foreach ($this->signups as $signup): ?>
      <div class="member clearfix">
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
        <?php if ($signup->event->rsvp): ?>
          <div class="notes">
            <?= $signup->note; ?>
          </div>
        <?php endif ?>
      </div>
    <?php endforeach ?>
  </div>
</div>