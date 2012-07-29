<?= $this->user->help('recruitment'); ?>

<div id="recruitment-dashboard">
  <div class="heading clearfix">
    <h2><?= $this->title ?></h2>
    <div class="right">
      <span class="excel-link">Export: 
        <?= html::anchor('recruitment/export/recruiting', 'Actively Recruiting', array('title' => "Export actively recruiting recruits")) ?></span> |
        <?= html::anchor('recruitment/export/bidded', 'Bidded', array('title' => "Export bidded recruits")) ?></span>
    </div>
  </div>

  <?= message::get(); ?>
  
  <div class="clearfix">
    <div id="recruitment-list">
      
      <div id="recruitment-categories">
        <ul>
          <li><?= html::anchor('recruitment', 'Actively Recruiting ('. number_format($this->list_counts[0]) .')') ?></li>
          <li><?= html::anchor('recruitment/bidded', 'Bidded Members ('. number_format($this->list_counts[1]) .')') ?></li>
          <li><?= html::anchor('recruitment/not-recruiting', 'No Longer Recruiting ('. number_format($this->list_counts[2]) .')') ?></li>
        </ul>
      </div>
      <div id="recruits">
        <?php if ($this->year || $this->hometown || $this->high_school): ?>
          <div class="recruit highlight">
            <span class="right"><?= html::anchor(Router::$current_uri, 'X', array('title' => 'Remove Filter')) ?></span>
            <?php if ($this->year): ?>
              Showing Only: <?= $this->year ?> recruits.
            <?php elseif ($this->hometown): ?>
              Showing Only: Recruits from <?= $this->recruits[0]->hometown ?> (Hometown)
            <?php elseif ($this->high_school): ?>
              Showing Only: Recruits from <?= $this->recruits[0]->high_school ?> (High School)
            <?php endif; ?>
          </div>
        <?php endif ?>
        <?php if ($this->recruits->count()): ?>
          <?php foreach ($this->recruits as $recruit): ?>
            <div class="recruit hoverable sortable clearfix" name="<?= text::searchable($recruit->name) ?>" category="<?= $recruit->bid_status ?>" fit="<?= $recruit->like_count ?>" updated="<?= strtotime($recruit->updated) ?>">
              <?php if ($this->list_id == 1): ?>
                <div class="bid-status bid-status-<?= $recruit->bid_status ?>"><?= $recruit->bid_status() ?></div>
              <?php endif ?>
              <div class="photo"><?= html::anchor('recruitment/show/'. $recruit->id, theme::image('small', $recruit->picture())); ?></div>
              <h3 class="name"><?= html::anchor('recruitment/show/'. $recruit->id, $recruit->name(TRUE)) ?></h3>
              <div class="meta">
                <div>
                  <strong><?= $recruit->year ? $recruit->year : 'Year Unknown' ?></strong>
                  <?php if ($recruit->phone): ?>
                    · <?= format::phone($recruit->phone); ?>
                  <?php endif ?>
                  <?php if ($recruit->facebook): ?>
                    · <?= html::anchor($recruit->facebook, 'Facebook Profile') ?>
                  <?php endif ?>
                </div>
                <div>
                  <?= format::plural($recruit->like_count, Kohana::lang('recruitment.good_fit.singular'), Kohana::lang('recruitment.good_fit.plural')); ?>
                  · <?= format::plural($recruit->comment_count, '@count comment', '@count comments') ?>
                </div>
              </div>
              
              <div class="meta-hometown">
                <?php if ($recruit->hometown): ?>
                  <div class="hometown">Home: <?= $recruit->hometown ?></div>
                <?php endif ?>
                <?php if ($recruit->high_school): ?>
                  <div class="highschool">H.S.: <?= $recruit->high_school ?></div>
                <?php endif ?>
              </div>
            </div>
          <?php endforeach ?>
        <?php else: ?>
          <div class="recruit">
            <h3 class="name">No results.</h3>
          </div>
        <?php endif ?>
        <div id="empty" class="recruit">
          <h3 class="name">There are no recruits in this list.</h3>
        </div>
      </div>
    </div>
    
    <div id="recruitment-actions">
      <?php if ($this->list_id == 1): ?>
        <div class="block">
          <label>Bid Summary:</label>
          <ul>
            <li>Pending: <?= $this->bid_counts[0] ?></li>
            <li>Accepted: <?= $this->bid_counts[1] ?></li>
            <li>Declined: <?= $this->bid_counts[2] ?></li>
          </ul>
        </div>
      <?php endif ?>
      
      <div class="sorting block">
        <label>Sorted by:</label>
        <ul>
          <li><?= html::anchor('#', 'Last Updated', array('class' => 'active', 'sort' => 'updated', 'order' => 'desc')) ?></li>
          <li><?= html::anchor('#', 'Alphabetical', array('sort' => 'name', 'order' => 'asc')) ?></li>
          <li><?= html::anchor('#', 'Best Fit', array('sort' => 'fit', 'order' => 'desc')) ?></li>
      </div>
      
      <div class="admin block">
        <label>School Year:</label>
        <ul>
          <li><?= html::anchor(Router::$current_uri, 'All', array('class' => $this->year ? 'inactive' : 'active')) ?></li>
          <?php foreach (Kohana::config('chapterboard.recruit_school_years') as $key => $value): ?>
            <?php if ($key): ?>
              <li><?= html::anchor(Router::$current_uri.'?year='. $key, sprintf('%s (%s)', $value, text::minimum($this->stats['year'][$value])), array('class' => $this->year == $key ? 'active' : '')) ?></li>
            <?php endif ?>
          <?php endforeach ?>
        </ul>
      </div>
      
      <?php if ($this->stats['hometown']): ?>
        <div class="admin block">
          <label>Hometown:</label>
          <ul>
            <li><?= html::anchor(Router::$current_uri, 'All', array('class' => $this->hometown ? 'inactive' : 'active')) ?></li>
            <?php foreach ($this->stats['hometown'] as $key => $hometown): ?>
              <li><?= html::anchor(Router::$current_uri .'?hometown='. $key, sprintf('%s (%s)', $hometown['name'], $hometown['count']), array('class' => $this->hometown == $key ? 'active' : '')) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif ?>
      
      <?php if ($this->stats['high_school']): ?>
        <div class="admin block">
          <label>High School:</label>
          <ul>
            <li><?= html::anchor(Router::$current_uri, 'All', array('class' => $this->high_school ? 'inactive' : 'active')) ?></li>
            <?php foreach ($this->stats['high_school'] as $key => $high_school): ?>
              <li><?= html::anchor(Router::$current_uri .'?high_school='. $key, sprintf('%s (%s)', $high_school['name'], $high_school['count']), array('class' => $this->high_school == $key ? 'active' : '')) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif ?>
      
      <?php if (A2::instance()->allowed('recruit', 'manage')): ?>
        <div class="admin block">
          <label>Administer Lists:</label>
          <ul>
            <li><?= html::anchor('recruitment/admin/clear/0', 'Archive Actively Recruiting', array('class' => 'alert', 'title' => 'Are you sure you want to archive all the recruits from the Actively Recruiting list?')) ?></li>
            <li><?= html::anchor('recruitment/admin/clear/1', 'Archive Bidded Members', array('class' => 'alert', 'title' => 'Are you sure you want to archive all the recruits from the Bidded Members list?')) ?></li>
            <li><?= html::anchor('recruitment/admin/clear/2', 'Archive No Longer Recruiting', array('class' => 'alert', 'title' => 'Are you sure you want to archive all the recruits from the No Longer Recruiting list?')) ?></li>            
          </ul>
        </div>
      <?php endif ?>
      
    </div>
  </div>
</div>