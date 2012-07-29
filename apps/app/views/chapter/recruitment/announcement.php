<div id="recruitment-announcement-form">
  <div class="heading clearfix">
    <h2><?= $this->title ?></h2>
  </div>

  <?= message::get() ?>
  
  <div class="help">
    <p>Use the form below to send an email announcement to recruits. The email that recruits receive will appear to come from your personal email address allowing them to reply back to your personal email address. However, we recommend you include your contact information so that recruits can get in touch with you if they have questions. </p>
  </div>
  
  <?= form::open() ?>
  <fieldset class="clearfix">
    <div class="split-left">
      <div class="clearfix">
        <?= form::label('subject', 'Subject*:') ?>
        <?= form::input('subject', $this->form['subject']) ?>
        <div class="error"><?= $this->errors['subject'] ?></div>
      </div>
      <div class="lists clearfix element">
        <?php foreach ($this->lists as $id => $name): ?>
          <div class="list clearfix">
            <label><?= is_null($first) ? 'Send to*:' : ''; $first = FALSE; ?></label>
            <div class="checkbox">
              <label><?= form::checkbox('lists['. $id .']', $id, $this->form['lists'][$id]) ?> <?= $name ?></label>
            </div>
          </div>
        <?php endforeach ?>
        <div class="error"><label></label><?= $this->errors['lists'] ?></div>
      </div>
      
    </div>
    <div class="split-right">
      <div class="clearfix">
        <?= form::label('message', 'Message*:') ?>
        <span class="inline-error"><?= $this->errors['message'] ?></span>
        <?= form::textarea('message', $this->form['message']) ?>
        <div class="right"><?= form::submit('post', 'Send Announcement'); ?></div>
        <div class="form-help">Plain text formatting only.</div>
      </div>

    </div>
  </fieldset>

  <?= form::close() ?>

</div>