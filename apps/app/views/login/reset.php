<div id="password-reset-form">
  <div class="content">
    <div class="form <?php print $errors ? 'errors' : ''; ?>">
      
      <div class="block">        
        <h2>Forgot your password?</h2>
        <p>Enter your email address to reset your password.</p>
      </div>
      
      <?php echo message::get(); ?>

      <?php echo form::open('reset', array('autocomplete' => 'off')); ?>
      <div class="clearfix">
        <?php echo form::label('email', 'Email Address:') ?>
        <?php echo form::input('email', $this->form['email']); ?>
      </div>

      <?php echo form::submit('submit', 'Recover Password'); ?>
      <?php echo form::close(); ?>
    </div>
    <div class="login-help">
      <ul>
        <li><?= html::anchor('login', 'Take me back to the login page') ?></li>
      </ul>
    </div>
    
  </div>
</div>