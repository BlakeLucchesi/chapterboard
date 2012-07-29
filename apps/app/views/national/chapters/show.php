<div id="members-roster">
  <div class="heading clearfix">
    <h2><?= $this->title ?></h2>
  </div>

  <div class="clearfix">
    <div class="split-left">
      <div id="tabbed-section">
        <ul>
          <li><?= html::anchor('chapters/'. $this->chapter->id .'/active', 'Actives ('. $this->type_count['active'] .')', array('class' => $this->type == 'active' ? 'active' : '')) ?></li>
          <li><?= html::anchor('chapters/'. $this->chapter->id .'/pledge', 'New Members ('. $this->type_count['pledge'] .')', array('class' => $this->type == 'pledge' ? 'active' : '')) ?></li>
          <li><?= html::anchor('chapters/'. $this->chapter->id .'/alumni', 'Alumni ('. $this->type_count['alumni'] .')', array('class' => $this->type == 'alumni' ? 'active' : '')) ?></li>
          <li><?= html::anchor('chapters/'. $this->chapter->id .'/leadership', 'Leadership ('. $this->type_count['leadership'] .')', array('class' => $this->type == 'leadership' ? 'active' : '')) ?></li>
          <li><?= html::anchor('chapters/'. $this->chapter->id .'/all', 'All Members ('. $this->type_count['all'] .')', array('class' => $this->type == 'all' ? 'active' : '')) ?></li>
        </ul>
      </div>
      <div id="members" class="type-leadership">
        <?php if ($this->members->count()): ?>
          <?php foreach ($this->members as $member): ?>
            <div class="member-position">
              <?= $member->profile->position ?>
            </div>
            <div class="member clearfix hoverable">
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
                <?= $member->profile->address() ?>
              </div>

              <div class="meta">
                <?php if ($member->profile->school_year): ?>
                  <div class="school-year"><em><?= $member->profile->school_year ?></em></div>
                <?php endif ?>
                <div class="pledge-class">
                  <?php if ($member->profile->initiation_year): ?>
                    Initiated: <?= $member->profile->initiation_year ?>
                  <?php endif ?>
                  <?php if ($member->profile->scroll_number): ?>
                    <?php if ($member->profile->initiation_year): ?>
                      ·
                    <?php endif ?>
                    Scroll #<?= number_format($member->profile->scroll_number) ?>
                  <?php endif ?>
                </div>
              </div>
            </div>
          <?php endforeach;?>
        <?php else: ?>
          <div id="empty">
            <p>There are no chapter leaders listed for this chapter.</p>
          </div>
        <?php endif;?>
      </div>
    </div>

    <div class="split-right">
  
      <div id="member-stats">
        <div class="content">
                  
          <?php if ($this->stats['types']): ?>
            <div class="block">
              <h3>Member Types</h3>
              <div class="table-data sizes">
                <?php foreach ($this->stats['types'] as $key => $value): ?>
                  <div class="row">
                    <label><?= ucwords(inflector::plural($this->types[$key])) ?>:</label><span class="value"><?= $value ?></span>
                  </div>          
                <?php endforeach ?>
              </div>
            </div>
          <?php endif ?>

          <?php if ($this->stats['shirt_size']): ?>
            <div class="block">
              <h3>Shirt Sizes</h3>
              <div class="table-data sizes">
                <?php foreach ($this->stats['shirt_size'] as $key => $value): ?>
                  <div class="row">
                    <?php if ($key): ?>
                      <label><?= $this->sizes[$key] ?>:</label><span class="value"><?= $value ?></span>
                    <?php endif ?>
                  </div>          
                <?php endforeach ?>
              </div>
            </div>
          <?php endif ?>
          
          <?php if ($this->stats['year']): ?>
            <div class="block">
              <h3>School Years</h3>
              <div class="table-data">
                <?php foreach ($this->stats['year'] as $key => $value): ?>
                  <?php if ($key): ?>
                    <div class="row"><label><?= ucwords($key) ?>:</label><span class="value"><?= $value ?></span></div>
                  <?php endif ?>
                <?php endforeach ?>
              </div>
            </div>
          <?php endif ?>

          <?php if ($this->stats['initiation_year']): ?>
            <div class="block">
              <h3>Initiation Years</h3>
              <div class="table-data">
                <?php foreach ($this->stats['initiation_year'] as $class): ?>
                  <?php if ($class['name']): ?>
                    <div class="row"><label><?= ucwords($class['name']) ?>:</label><span class="value"><?= $class['count'] ?></span></div>
                  <?php endif ?>
                <?php endforeach ?>
              </div>
            </div>
          <?php endif ?>

        </div>
      </div>
    </div>

  </div>
</div>