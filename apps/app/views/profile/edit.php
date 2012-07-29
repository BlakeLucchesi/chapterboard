<?= $this->user->help('first_login') ?>

<div id="profile-edit">
<div class="heading clearfix icon">
  <h2><?= $this->title; ?></h2>
  <ul>
    <li><?= html::anchor('profile/'. $this->profile->user_id, 'View Profile') ?></li>
  </ul>
</div>

<?= message::get(); ?>

<?= form::open_multipart(URI::string()) ?>
  <fieldset class="clearfix">
    
    <div class="split-left">
      <div class="block">
        <div class="right"><em>* denotes required field.</em></div>
        <h3>Login Information</h3>
        <div class="clearfix">
          <?= form::label('name', 'Name:')?>
          <?= $this->profile->user->name() ?>
        </div>
        
        <?php if (A2::instance()->allowed('user', 'edit_name')): ?>
          <div class="clearfix">
            <?= form::label('first_name', 'First Name:*')?>
            <?= form::input('first_name', $this->form['first_name']) ?>
            <span class="error"><?= $this->errors['first_name'] ?></span>
          </div>
          <div class="clearfix">
            <?= form::label('last_name', 'Last Name:*')?>
            <?= form::input('last_name', $this->form['last_name']) ?>
            <span class="error"><?= $this->errors['last_name'] ?></span>
          </div>
        <?php endif ?>
        
        <div class="clearfix">
          <?= form::label('email', 'Email Address*:')?>
          <?= form::input('email', $this->form['email'], 'class="medium"') ?>
          <span class="error"><?= $this->errors['email'] ?></span>
        </div>
        
        <div class="clearfix">
          <?= form::label('password', 'New Password:')?>
          <?= form::password('password', $this->form['password'], 'class="small" autocomplete="off"') ?>
          <span class="error"><?= $this->errors['password'] ?></span>
        </div>
        <div class="clearfix">
          <?= form::label('password_confirm', 'Retype Password:')?>
          <?= form::password('password_confirm', $this->form['password_confirm'], 'class="small" autocomplete="off"') ?> (type your new password again.)
          <span class="error"><?= $this->errors['password_confirm'] ?></span>
        </div>
        
      </div>
      <div class="block">
        <h3>Contact Information</h3>
        <div class="clearfix">
          <?= form::label('phone', 'Mobile Phone*:')?>
          <?= form::input('phone', format::phone($this->form['phone']), 'class="small"') ?>
          <span class="error"><?= $this->errors['phone'] ?></span>
        </div>
        <div class="clearfix">
        <div class="description">Choose your phone service provider to receive group text messages from your chapter.</div>
          <?= form::label('phone_carrier', 'Phone Provider:')?>
          <?= form::dropdown('phone_carrier', sms::carriers_select(), $this->form['phone_carrier']) ?>
          <span class="error"><?= $this->errors['phone_carrier'] ?></span>
        </div>
        
        <br />
        <h3>Home/Permanent Address</h3>
        <div class="clearfix">
          <?= form::label('home_address1', 'Address:')?>
          <?= form::input('home_address1', $this->form['home_address1'], 'class="medium"') ?>
          <span class="error"><?= $this->errors['home_address1'] ?></span>
        </div>
        <div class="clearfix">
          <?= form::label('home_address2', '')?>
          <?= form::input('home_address2', $this->form['home_address2'], 'class="medium"') ?>
          <span class="error"><?= $this->errors['home_address2'] ?></span>
        </div>
        <div class="clearfix">
          <?= form::label('home_city', 'City:')?>
          <?= form::input('home_city', $this->form['home_city'], 'class="medium"') ?>
          <span class="error"><?= $this->errors['home_city'] ?></span>
        </div>
        <div class="clearfix">
          <?= form::label('home_state', 'State:')?>
          <?= form::state_select('home_state', TRUE, $this->form['home_state'], '', TRUE) ?>
          <span class="error"><?= $this->errors['home_state'] ?></span>
        </div>
        <div class="clearfix">    
          <?= form::label('home_zip', 'Zip:')?>
          <?= form::input('home_zip', $this->form['home_zip'], 'size="10" class="mini"') ?>
          <span class="error"><?= $this->errors['home_zip'] ?></span>
        </div>
        
        <br />
        <h3>School Address</h3>
        <div class="clearfix">
          <?= form::label('address1', 'Address:')?>
          <?= form::input('address1', $this->form['address1'], 'class="medium"') ?>
          <span class="error"><?= $this->errors['address1'] ?></span>
        </div>
        <div class="clearfix">
          <?= form::label('address2', '')?>
          <?= form::input('address2', $this->form['address2'], 'class="medium"') ?>
          <span class="error"><?= $this->errors['address2'] ?></span>
        </div>
        <div class="clearfix">
          <?= form::label('city', 'City:')?>
          <?= form::input('city', $this->form['city'], 'class="medium"') ?>
          <span class="error"><?= $this->errors['city'] ?></span>
        </div>
        <div class="clearfix">
          <?= form::label('state', 'State:')?>
          <?= form::state_select('state', TRUE, $this->form['state'], '', TRUE) ?>
          <span class="error"><?= $this->errors['state'] ?></span>
        </div>
        <div class="clearfix">    
          <?= form::label('zip', 'Zip:')?>
          <?= form::input('zip', $this->form['zip'], 'size="10" class="mini"') ?>
          <span class="error"><?= $this->errors['zip'] ?></span>
        </div>
      </div>
      
      <div class="block">
        <h3>Profile</h3>      
        <div class="clearfix">
          <?= form::label('birthday', 'Date of Birth:')?>
          <?= form::input('birthday', $this->form['birthday'] ? date::display($this->form['birthday'], 'm/d/Y', FALSE) : '', 'class="small"') ?> <span>MM/DD/YYYY</span>
          <span class="error"><?= $this->errors['birthday'] ?></span>
        </div>
        <div class="clearfix">
          <?= form::label('school_year', 'Year in School:')?>
          <?= form::dropdown('school_year', Kohana::config('chapterboard.school_years'), $this->form['school_year']) ?>
          <span class="error"><?= $this->errors['school_year'] ?></span>
        </div>
        <div class="clearfix">
          <?= form::label('shirt_size', 'Shirt Size*:')?>
          <?= form::dropdown('shirt_size', Kohana::config('chapterboard.shirt_sizes'), $this->form['shirt_size']) ?>
          <span class="error"><?= $this->errors['shirt_size'] ?></span>
        </div>
        <div class="clearfix">
          <?= form::label('student_id', 'Student ID:')?>
          <?= form::input('student_id', $this->form['student_id'], 'class="small"') ?> <span>(Only chapter admins can see this.)
          <span class="error"><?= $this->errors['student_id'] ?></span>
        </div>
        <div class="clearfix">
          <?= form::label('department', 'School/Department:')?>
          <?= form::input('department', $this->form['department'], 'class="medium"') ?> <em>e.g. Physical Sciences</em>
          <span class="error"><?= $this->errors['department'] ?></span>
        </div>
        <div class="clearfix">
          <?= form::label('major', 'Major:')?>
          <?= form::input('major', $this->form['major'], 'class="medium"') ?> <em>e.g. Chemistry</em>
          <span class="error"><?= $this->errors['major'] ?></span>
        </div>
        
        <!-- <div class="clearfix">
          <?= form::label('pledge_date', 'Pledge Date:')?>
          <?= form::input('pledge_date', $this->form['pledge_date'] ? date::display($this->form['pledge_date'], 'm/d/Y', FALSE) : '', 'class="small"') ?> <span>MM/DD/YYYY</span>
          <span class="error"><?= $this->errors['pledge_date'] ?></span>
        </div>
        <div class="clearfix">
          <?= form::label('initiation_date', 'Initiation Date:')?>
          <?= form::input('initiation_date', $this->form['initiation_date'] ? date::display($this->form['initiation_date'], 'm/d/Y', FALSE) : '', 'class="small"') ?> <span>MM/DD/YYYY</span>
          <span class="error"><?= $this->errors['initiation_date'] ?></span>
        </div> -->
        <div class="clearfix">
          <?= form::label('initiation_year', 'Initiation Year:')?>
          <?= form::year_select('initiation_year', -125, $this->form['initiation_year'], 'class="mini"', TRUE) ?> <span> (Leave blank if you have not been initiated.)</span>
          <span class="error"><?= $this->errors['initiation_year'] ?></span>
        </div>
      </div>
      
      <div class="block">
        <h3>Emergency Contacts</h3>
        <div class="clearfix">
          <?= form::label('emergency1_name', 'Contact Name:')?>
          <?= form::input('emergency1_name', $this->form['emergency1_name']) ?>
          <span class="error"><?= $this->errors['emergency1_name'] ?></span>
        </div>
        <div class="clearfix">
          <?= form::label('emergency1_phone', 'Contact Phone:')?>
          <?= form::input('emergency1_phone', $this->form['emergency1_phone']) ?>
          <span class="error"><?= $this->errors['emergency1_phone'] ?></span>
        </div>
        <br />
        <div class="clearfix">
          <?= form::label('emergency2_name', 'Contact Name:')?>
          <?= form::input('emergency2_name', $this->form['emergency2_name']) ?>
          <span class="error"><?= $this->errors['emergency2_name'] ?></span>
        </div>
        <div class="clearfix">
          <?= form::label('emergency2_phone', 'Contact Phone:')?>
          <?= form::input('emergency2_phone', $this->form['emergency2_phone']) ?>
          <span class="error"><?= $this->errors['emergency2_phone'] ?></span>
        </div>
        
        
      </div>
      
    </div>
    
    <div class="split-right">
      <div class="clearfix">
        <h3>Profile Picture</h3>
        <?= form::upload('picture', $this->form['picture']) ?>
        <span class="error"><?= $this->errors['picture'] ?></span>
      </div>
      <div class="profile-picture">
        <?= theme::image('profile', $this->profile->user->picture()) ?>
      </div>
    </div>
  </fieldset>
  <div class="right">
    <?= form::submit('submit', 'Save Profile')?> or <?= html::anchor('profile/'. $this->form['id'], 'cancel') ?> 
  </div>
<?= form::close() ?>