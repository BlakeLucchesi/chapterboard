<div id="login-block">
  <div class="content">
    <div class="form">
      <?php if (isset($this->error)): ?>
        <div class="login-errors"><?= $this->error ?></div>
      <?php endif; ?>
  
      <?= form::open(); ?>
        <div class="clearfix">
          <?= form::label('email', 'Email:')?>
          <?= form::input(array('name' => 'email', 'type' => 'email'), $this->form['email']) ?>

          <?= form::label('pass', 'Password:')?>
          <?= form::password('pass') ?>
        </div>
        <div class="clearfix">
          <label class="checkbox"><?= form::checkbox('remember', '1') ?> Remember me</label>
          <?= form::submit('submit', 'Sign In'); ?>
        </div>
      <?= form::close(); ?>
    </div>
    
    <div class="login-help">
      <ul>
        <li><strong>Help:</strong> <?= html::anchor('reset', 'I forgot my password') ?></li>
      </ul>
    </div>
  </div>
</div>
