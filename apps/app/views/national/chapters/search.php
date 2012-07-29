<div id="members-roster">
  <div class="heading clearfix">
    <h2><?= $this->title ?></h2>
  </div>

  <div class="clearfix">
    <div class="split-left">
      <div id="members" class="clearfix type-leadership">
        <?php if ($_GET['name'] && $this->members->count()): ?>
          <?php foreach ($this->members as $member): ?>
            <div class="member-position">
              <?= $member->profile->position ?>
            </div>
            <div class="member clearfix hoverable">
              <div class="size" title="Member type"><?= $member->type() ?></div>
              <div class="photo">
                <?= html::anchor('profile/'. $member->id, theme::image('small', $member->picture())) ?>
              </div>

              <h3 class="member-name">
                <?= html::anchor('profile/'. $member->id, $member->name()) ?> 
              </h3>

              <div class="information">
                <span class="phone-number"><em><?= format::phone($member->profile->phone) ?></em></span><br />
                <span class="email"><?= $member->email(TRUE) ?></span>
              </div>

              <div class="address">
                <?= $member->site->chapter_name ?><br />
                <?= $member->site->school->name ?>
              </div>

              <div class="meta">
              </div>
            </div>
          <?php endforeach;?>
        <?php elseif ($_GET['name']): ?>
          <div id="empty">
            <p>No members matched your search.</p>
          </div>
        <?php else: ?>
          <?= $this->search_form; ?>
        <?php endif;?>
      </div>
    </div>
    <div class="split-right">
      <div class="clearfix">
        <?php if ($_GET['name']): ?>
          <?= $this->search_form ?>
        <?php endif; ?>
      </div>
    </div>

  </div>
</div>