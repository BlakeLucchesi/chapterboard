<?php foreach ($this->members as $member): ?>
  <div class="member clearfix">
    <div class="photo">
      <?php echo theme::image('mini', $member->picture()) ?>
    </div>

    <h3 class="member-name"><?php echo $member->name(TRUE) ?></h3>

    <div class="information">
      <div class="phone-number"><?php echo $member->profile->phone ?></div>
      <div class="email"><?php echo $member->email(TRUE) ?></div>
    </div>

    <div class="address">
      <?php echo $member->profile->address() ?>
    </div>
  </div>
<?php endforeach ?>