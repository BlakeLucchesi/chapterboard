<div id="group-admin">
  <div class="heading clearfix">
    <h2><?= $this->title ?></h2>
  </div>

  <div class="clearfix">
    <div class="split-left">
      <div id="members">
        <?= View::factory('members/groups/members')->render(); // Same as whats loaded via ajax, don't remove... ?>
      </div>
    </div>

    <div class="split-right">
      <div id="member-stats">
        <div id="member-search-form" class="block">
          <?= form::open(NULL, array('method' => 'get')) ?>
          <fieldset>
            <h3>Add Members</h3>
            <?= form::input('name', $this->form['name'], 'autocomplete="off"') ?>
            <!-- <?= form::submit('', 'Add Member') ?> -->
            <?= form::close() ?>            
          </fieldset>
          
          <div id="flash"></div>
        </div>

      </div>
    </div>
  </div>
</div>