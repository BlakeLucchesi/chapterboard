<div id="member">
  <h2 class="title"><?= $this->member->name() ?></h2>
  <div><p><?= $this->member->phone(TRUE) ?></p></div>
  <div><p><?= html::mailto($this->member->email) ?></p></div>

  <?php if ($address = $this->member->profile->address()): ?>
    <div class="block">
      <h4>Mailing Address</h4>
      <div><?= $address ?></div>
    </div>
  <?php endif ?>

  <div class="block">
    <h4>Emergency Contacts</h4>
    <p>
      <?= $this->member->profile->emergency1_name ?><br />
      <a href="tel:<?= $this->member->profile->emergency1_phone ?>"><?= format::phone($this->member->profile->emergency1_phone) ?></a>
    </p>
    <p>
      <?= $this->member->profile->emergency2_name ?><br />
      <a href="tel:<?= $this->member->profile->emergency2_phone ?>"><?= format::phone($this->member->profile->emergency2_phone) ?></a>
    </p>
  </div>
  
</div>