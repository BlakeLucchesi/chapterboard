<?= message::get() ?>

<div class="clearfix">
  <h2>Your account is just a click away.</h2>
  <p>We've just sent you a text message with a confirmation code from the number (415) 800-3041. Use the code from the text message to complete your account registration.</p>
    
  <?= form::open(); ?>
    
  <div class="clearfix">
    <?= form::label('confirm_token', 'Secret Code:*') ?>
    <?= form::input('confirm_token', $this->form['confirm_token'], 'class="small uppercase" autocomplete="off"') ?>
    <div class="form-error"><?= $this->errors['confirm_token'] ?></div>
  </div>
  <br />
  <h2 class="title">Your Account Details</h2>
  <div class="clearfix">
    <?= form::label('password', 'Password:*')?>
    <?= form::password('password', $this->form['password'], 'autocomplete="off"') ?>
    <div class="form-error"><?= $this->errors['password'] ?></div>
  </div>
  <div class="clearfix">
    <?= form::label('password_confirm', 'Confirm:*')?>
    <?= form::password('password_confirm', $this->form['password_confirm'], 'autocomplete="off"') ?>
    <div class="form-error"><?= $this->errors['password_confirm'] ?></div>
  </div>
  <br />

  <div class="clearfix">
    <?= form::label('timezone', 'Timezone:*')?>
    <?= form::dropdown('timezone', $this->timezones, $this->form['timezone']) ?>
    <div class="form-error"><?= $this->errors['timezone'] ?></div>
  </div>
  
  <div class="clearfix">
    <div class="clearfix">
      <?= form::label('slug', 'Chapter Nickname:*') ?>
      <?= form::input('slug', $this->form['slug']) ?>
    </div>
    <div class="setup-tips">e.x. "WashingtonDelts", "FloridaDG", "ArizonaPikes" <span class="form-tip" title="Keep it short and simple, and please use only letters, numbers and dashes."><?= html::image('minis/information.png'); ?></span></div>
    <div class="form-error"><?= $this->errors['slug'] ?></div>
  </div>

  <?= form::submit('submit', 'Complete Setup'); ?>
  <?= form::close(); ?>
  
</div>

<div class="login-help clearfix">
  <ul>
    <li>
    <strong>Didn't receive our confirmation text message?</strong><br />
    If it's been more than a few minutes and you still have not received your confirmation email, please send us an email at <?= html::mailto('team@chapterboard.com') ?> and we'll gladly assist you.
    </li>
  </ul>
</div>

<?php if (IN_PRODUCTION): ?>
  <!-- Paste this code just above the closing </body> of your conversion page. The tag will record a conversion every time this page is loaded. Optional 'sku' and 'value' fields are described in the Help Center. -->
  <script src="//ah8.facebook.com/js/conversions/tracking.js"></script><script type="text/javascript">
  try {
    FB.Insights.impression({
       'id' : 6002447767720,
       'h' : 'f03d939655'
    });
  } catch (e) {}
  </script>
<?php endif ?>