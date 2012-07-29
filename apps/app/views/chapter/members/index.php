<?= $this->user->help('members') ?>

<div id="members-roster" class="member-roster">
  <div class="heading clearfix icon relative">
    <h2><?= $this->title ?></h2>
    <div class="right">
      <span class="excel-link">Export:
        <?php foreach ($this->groups as $group): ?>
          <?= html::anchor('members/export/'. $group->static_key, $group->name, array('title' => "Export {$group->name} members")) ?> |
        <?php endforeach ?>
        <?= html::anchor('members/export/all', 'All Members', array('title' => "Export All Members")) ?>
      </span>
    </div>
    <!-- <div class="print-link right"><?= html::anchor('members/printable?name='. $this->name .'&type='. $this->type, 'Printer Friendly', array('class' => 'print')) ?></div> -->
  </div>

  <div class="clearfix">
    <div class="split-left">
      <div id="tabbed-section">
        <ul>
          <?php foreach ($this->groups as $group): ?>
            <li><?= html::anchor('members/'. $group->static_key, $group->name .' ('. $this->type_count[$group->static_key] .')', array('class' => $this->type == $group->static_key ? 'active' : '')) ?></li>
          <?php endforeach ?>
          <li><?= html::anchor('members/leadership', 'Leadership ('. $this->type_count['leadership'] .')', array('class' => $this->type == 'leadership' ? 'active' : '')) ?></li>
          <li><?= html::anchor('members/all', 'All Members ('. $this->type_count['all'] .')', array('class' => $this->type == 'all' ? 'active' : '')) ?></li>
        </ul>
      </div>
      <div id="members" class="type-<?= $this->type ?>">
        <?php if ($_GET['major']): ?>
          <div class="member member-filter highlight">Showing <?= $_GET['major'] ?> majors. <?= html::anchor(Router::$current_uri, 'Close') ?></div>
        <?php endif ?>
        <?php if ($_GET['department']): ?>
          <div class="member member-filter highlight">Showing members who studied <?= $_GET['department'] ?>. <?= html::anchor(Router::$current_uri, 'Close') ?></div>
        <?php endif ?>

        <?php if ($this->members->count()): ?>
          <?php foreach ($this->members as $member): ?>
            <?php if ($this->type == 'leadership'): ?>
              <div class="member-position">
                <?= $member->profile->position ?>
              </div>
            <?php endif ?>
            <div class="member clearfix hoverable">
              <?php if ($member->profile->shirt_size): ?>
                <div class="size" title="Shirt size"><?= $member->shirt_size(FALSE) ?></div>
              <?php endif ?>
              <div class="photo">
                <?= html::anchor('profile/'. $member->id, theme::image('small', $member->picture())) ?>
              </div>

              <h3 class="member-name">
                <?= $member->name(true) ?> 
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

              <?php if ($member->profile->student_id && $this->admin): ?>
                <div class="student-id">ID#: <?= $member->profile->student_id; ?></div>
              <?php endif ?>

            </div>
          <?php endforeach;?>
        <?php else: ?>
          <?php if ($this->type == 'leadership'): ?>
            <div id="empty" class="member clearfix">
              <h3>Chapter Leaders have not been assigned</h3>
              <?php if (A2::instance()->allowed('user', 'manage')): ?>
                <p><br />Use the "<?= html::anchor('members/admin', 'Manage Members') ?>" form to update your chapter roster information.</p>
              <?php endif ?>
            </div>
          <?php else: ?>
            <div id="empty" class="member clearfix"><h3><?= Kohana::lang('members.no_results.'. $this->type) ?></h3></div>
          <?php endif ?>
        <?php endif?>
      </div>
    </div>

    <div class="split-right">
  
      <div id="member-stats">
        <div class="content">
          
          <?php if ($this->type != 'leadership'): ?>
            <div id="member-search-form" class="block">
              <?= form::open(NULL, array('method' => 'get')) ?>
              <?= form::input('name', $this->form['name']) ?>
              <?= form::submit('', 'Search') ?>
              <?php if ($this->form['name']): ?>
                <br />
                <?php if ($this->type == 'active'): ?>
                  <?= html::anchor('members', 'clear results') ?>
                <?php else: ?>  
                  <?= html::anchor('members/'. $this->type, 'clear results') ?>
                <?php endif ?>
              <?php endif ?>
              <?= form::close() ?>
            </div>
          <?php endif ?>
                    
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
                <div class="row"><label>First Year:</label><span class="value"><?= number_format($this->stats['year']['First Year']) ?></span></div>
                <div class="row"><label>Second Year:</label><span class="value"><?= number_format($this->stats['year']['Second Year']) ?></span></div>
                <div class="row"><label>Third Year:</label><span class="value"><?= number_format($this->stats['year']['Third Year']) ?></span></div>
                <div class="row"><label>Fourth Year:</label><span class="value"><?= number_format($this->stats['year']['Fourth Year']) ?></span></div>
                <div class="row"><label>Super Senior:</label><span class="value"><?= number_format($this->stats['year']['Super Senior']) ?></span></div>
                <div class="row"><label>Alumni:</label><span class="value"><?= number_format($this->stats['year']['Alumni']) ?></span></div>
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
          
          <?php if ($this->stats['departments']): ?>
            <div class="block">
              <h3>Schools/Departments</h3>
              <table>
                <?php foreach ($this->stats['departments'] as $key => $value): ?>
                  <?php if ($key): ?>
                    <tr><td><?= html::anchor(Router::$current_uri .'?department='.urlencode($key), $key) ?>:</td><td class="right"><?= $value ?></td></tr>
                  <?php endif ?>
                <?php endforeach ?>
              </table>
            </div>
          <?php endif ?>
          
          <?php if ($this->stats['majors']): ?>
            <div class="block">
              <h3>Majors</h3>
              <table>
                <?php foreach ($this->stats['majors'] as $key => $value): ?>
                  <?php if ($key): ?>
                    <tr>
                      <td><?= html::anchor(Router::$current_uri .'?major='.urlencode($key), $key) ?></td><td class="right"><?= $value ?></td>
                    </tr>
                  <?php endif ?>
                <?php endforeach ?>
              </table>
            </div>
          <?php endif ?>

        </div>
      </div>
    </div>
  </div>
</div>