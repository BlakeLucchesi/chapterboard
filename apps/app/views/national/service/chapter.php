<div id="service">  
  <div class="heading clearfix">
    <h2><?= $this->title ?></h2>
  </div>
  
  <table>
    <thead>
      <tr>
        <th>Group</th>
        <th class="amount right">Hours</th>
        <th class="amount right">Donations</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($this->summary as $group): ?>
        <tr>
          <td><?= $group['title'] ?></td>
          <td class="right"><?= number_format($group['hours'], 1) ?></td>
          <td class="right"><?= money::display($group['dollars']) ?></td>
        </tr>
      <?php endforeach ?>
    </tbody>
    <tfoot>
      <tr>
        <td class="right">Totals:</td>
        <td class="right"><?= format::plural(number_format($this->summary_sum['hours'], 1), '@count hour', '@count hours') ?></td>
        <td class="right"><?= money::display($this->summary_sum['dollars']) ?></td>
      </tr>
    </tfoot>
  </table>
  
  <div class="heading clearfix">
    <h2><?= $this->members_title; ?></h2>
    <?php if ($this->period != $this->current_period): ?>
      <div class="right">
        * When viewing reports for previous years only members who recorded hours in that period are shown. 
      </div>      
    <?php endif ?>
  </div>

  <?= message::get() ?>
  
  <div id="tabbed-section">
    <ul>
      <li><?= html::anchor('service/chapter/'. $this->chapter->id .'', 'Actives ('. number_format($this->list_counts['active']) .')') ?></li>
      <li><?= html::anchor('service/chapter/'. $this->chapter->id .'/pledge', 'New Members ('. number_format($this->list_counts['pledge']) .')') ?></li>
      <li><?= html::anchor('service/chapter/'. $this->chapter->id .'/alumni', 'Alumni ('. number_format($this->list_counts['alumni']) .')') ?></li>
      <li><?= html::anchor('service/chapter/'. $this->chapter->id .'/all', 'All Members ('. number_format($this->list_counts['all']) .')') ?></li>
    </ul>
  </div>

  <div>
    <table class="sort">
      <thead>
        <tr>
          <th class="{sorter: 'link_sort'}">Member</th>
          <th class="hours right {sorter: 'digit'}">Hours</th>
          <th class="dollars right">Dollars</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($this->period == $this->current_period): ?>
          <?php foreach ($this->members as $member): ?>
            <?php if (($this->type == $member->type || $this->type == 'all') && ($member->status || $this->records[$member->id]->hours || $this->records[$member->id]->dollars)): ?>
              <tr class="hoverable">
                <td class="title">
                  <?= html::anchor('profile/'. $member->id, $member->name()) ?>
                  <?= $member->status ? '' : '<em>Archived</em>' ?>
                </td>
                <td class="right"><?= number_format($this->records[$member->id]->hours, 1) ?></td>
                <td class="right"><?= money::display($this->records[$member->id]->dollars) ?></td>
              </tr>
            <?php endif ?>
          <?php endforeach ?>
        <?php else: ?>
          <?php foreach ($this->records as $member): ?>
            <tr class="hoverable">
              <td class="title">
                <?= html::anchor('profile/'. $member->id, $member->first_name.' '.$member->last_name) ?>
                <?= $member->status ? '' : '<em>Archived</em>' ?>
              </td>
              <td class="right"><?= number_format($member->hours, 1) ?></td>
              <td class="right"><?= money::display($member->dollars) ?></td>
            </tr>
          <?php endforeach ?>
        <?php endif; ?>
      </tbody>
      <tfoot>
        <tr>
          <td class="right">Totals:</td>
          <td class="right"><?= format::plural(number_format($this->sum['hours'], 1), '@count hour', '@count hours') ?></td>
          <td class="right"><?= money::display($this->sum['dollars']) ?></td>
        </tr>
      </tfoot>
    </table>
  </div>
</div>