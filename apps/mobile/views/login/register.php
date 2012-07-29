<div id="create-account-form">
  <div class="content">
    <h2>Join Your Chapter on ChapterBoard</h2>
    <div class="form <?php print $this->errors ? 'errors' : ''; ?>">
      <br />
      <?php if (!empty($this->errors)): ?>
        <div class="login-errors">* Please fix any errors below.</div>
      <?php endif; ?>

      <?= form::open(); ?>
      
      <div class="clearfix">
        <?= form::label('first_name', 'First Name:') ?>
        <?= form::input('first_name', $this->form['first_name']); ?>
        <div class="form-error"> <?= $this->errors['first_name']; ?></div>
      </div>
      <div class="clearfix">
        <?= form::label('last_name', 'Last Name:') ?>
        <?= form::input('last_name', $this->form['last_name']); ?>
        <div class="form-error"> <?= $this->errors['last_name']; ?></div>
      </div>
      <div class="clearfix">
        <?= form::label('password', 'Password:')?>
        <?= form::password('password') ?>
        <div class="form-error"><?= $this->errors['password'] ?></div>
      </div>
      <div class="clearfix">
        <?= form::label('password_confirm', 'Confirm Password:')?>
        <?= form::password('password_confirm') ?>
        <div class="form-error"><?= $this->errors['password_confirm'] ?></div>
      </div>
      <br />
      
      <div class="clearfix checkbox">
        <label><?= form::checkbox('agreement', 1) ?> I have read and agree to the <?= html::anchor(Kohana::config('app.public_url') .'/terms-of-service', 'terms of service', array('target' => '_blank')) ?>.</label>
        <div class="form-error"> <?= $this->errors['agreement']; ?></div>        
      </div>
    
      <?= form::submit('submit', 'Create Account'); ?>
      <?= form::close(); ?>
    </div>
    
  </div>
</div>