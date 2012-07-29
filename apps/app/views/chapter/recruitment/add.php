<div id="recruitment-add">
  <div class="heading clearfix">
    <h2><?= $this->title ?></h2>
  </div>

  <?= message::get(); ?>

  <?= form::open_multipart(NULL, array('id' => 'recruit-form')); ?>
  <fieldset class="clearfix">
    <div class="split-left">
      <div class="clearfix">
        <?= form::label('name', 'Name:')?>
        <?= form::input('name', $this->form['name']) ?>
        <span class="error"><?= $this->errors['name'] ?></span>
      </div>
      
      <div class="clearfix">
        <?= form::label('phone', 'Phone:')?>
        <?= form::input('phone', $this->form['phone']) ?>
        <span class="error"><?= $this->errors['phone'] ?></span>
      </div>
      
      <div class="clearfix">
        <?= form::label('email', 'Email:')?>
        <?= form::input('email', $this->form['email']) ?>
        <span class="error"><?= $this->errors['email'] ?></span>
      </div>
      
      <div class="clearfix">
        <?= form::label('facebook', 'Facebook Profile:')?>
        <?= form::input('facebook', $this->form['facebook']) ?>
        <span class="error"><?= $this->errors['facebook'] ?></span>
      </div>
      
      <div class="clearfix">
        <?= form::label('year', 'School Year:')?>
        <?= form::dropdown('year', Kohana::config('chapterboard.recruit_school_years'), $this->form['year']) ?> 
        <div class="error"><?= $this->errors['year'] ?></div>
      </div>
      
      <div class="clearfix">
        <?= form::label('major', 'Major:')?>
        <?= form::input('major', $this->form['major']) ?>
        <span class="error"><?= $this->errors['major'] ?></span>
      </div>
      
      
      <div class="clearfix">
        <?= form::label('hometown', 'Hometown:')?>
        <?= form::input('hometown', $this->form['hometown']) ?>
        <span class="error"><?= $this->errors['hometown'] ?></span>
      </div>
      
      <div class="clearfix">
        <?= form::label('high_school', 'High School:')?>
        <?= form::input('high_school', $this->form['high_school']) ?>
        <span class="error"><?= $this->errors['high_school'] ?></span>
      </div>
      
      <div class="clearfix">
        <?= form::label('photo', 'Photo:')?>
        <?= form::upload(array('name' => 'photo')) ?>
        <span class="error"><?= $this->errors['photo'] ?></span>
      </div>
    
    </div>

    <div class="split-right">
      <div class="clearfix">
        <?= form::label('about', 'About:')?><span class="inline-error"><?= $this->errors['about'] ?></span>
        <?= form::textarea('about', $this->form['about']) ?>
        <div class="right"><?= form::submit('post', $this->recruit->id ? 'Save Changes' : 'Add Recruit') ?></div>
      </div>
      
    </div>
  </fieldset>
  <?= form::close(); ?>
</div>