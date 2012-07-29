<div id="login-block">
  <div class="content">
    <div class="form">
      <?php if (isset($this->error)): ?>
        <div class="login-errors"><?= $this->error ?></div>
      <?php endif; ?>
  
      <?php print form::open('login'); ?>
      <div class="clearfix">
        <?= form::label('email', 'Email:')?>
        <?= form::input('email', $this->form['email']) ?>
      </div>
      <div class="clearfix">
        <?= form::label('pass', 'Password:')?>
        <?= form::password('pass') ?>
      </div>
      
      <!-- <div class="clearfix checkbox">
        <label><?= form::checkbox('remember', true) ?> Keep me logged in</label>
      </div> -->
      
      <?= form::submit('submit', 'Sign In'); ?>
      <?= form::close(); ?>
    </div>
    
    <div class="login-help">
      <ul>
        <li>If you know your password and have trouble logging in, clear the cookies in your browser and try again.</li>
        <li><strong>Help:</strong> <?= html::anchor('reset', 'I forgot my password') ?></li>
      </ul>
    </div>
  </div>
</div>
