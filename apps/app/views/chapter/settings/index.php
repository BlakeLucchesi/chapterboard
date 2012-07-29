<div id="settings-permissions" class="clearfix">
  
  <div class="heading clearfix">
    <h2 class="title">Chapter Account Information</h2>
  </div>

  <?= message::get() ?>

  <div class="clearfix">
    <div class="split-left">
      <div class="block clearfix">
        <?= form::open(); ?>
        <fieldset>
          <div class="clearfix">
            <?= form::label('', 'Chapter Name:')?>
            <?= $this->site->chapter->name; ?>
          </div>

          <div class="clearfix">
            <?= form::label('', 'University:')?>
            <?= $this->site->school->name; ?>
          </div>

          <div class="clearfix">
            <?= form::label('slug', 'Chapter Nickname:*') ?>
            <?= form::input('slug', $this->form['slug'], 'class="small"') ?>
            <span class="form-tip" title="This will be used for unique public urls (as seen in the fundraising campaigns) and other cool stuff we're currently working on.  Keep it short and sweet, and please only letters, numbers and dashes allowed."><?= html::image('minis/information.png'); ?></span>
            <div>e.x. "WashingtonDelts", "FloridaDG", "ArizonaPikes"</div>
            <div class="error"><?= $this->errors['slug'] ?></div>
          </div>

          <div class="clearfix">
            <?= form::label('user_id', 'Primary Contact:')?>
            <?= form::dropdown('user_id', $this->members, $form['user_id'] ? $form['user_id'] : $this->site->user->id) ?>
            <span class="form-tip" title="The individual responsible for managing the chapter's account.  This is who we will contact regarding online collections or any service related issues."><?= html::image('minis/information.png'); ?></span>
            <span class="error"><?= $errors['user_id'] ?></span>
          </div>
          <div class="right">
            <?= form::submit('save', 'Update Account', 'class="inline-submit"') ?>
          </div>
        </fieldset>
        <?= form::close() ?>
      </div>

    </div><!-- split-left -->
    <div class="split-right">
        <div class="block clearfix">
            <h3>Data Backup</h3>
            <p>Use this feature if you would like to download a file with all of your chapters data. The file will contain an xml file with all of the chapter data (forum topics, recruitment lists, etc) and a folder with all of your chapter's uploaded files.</p>
            <p>After clicking the "Generate Backup" button we will begin the backup process.  You will be sent an email with a link to download your backed up data when it is ready.</p>
            <?= form::open('settings/backup') ?>
                <?= form::submit('submit', 'Generate Backup'); ?>
            <?= form::close(); ?>
        </div>
    </div><!-- end split-right -->
  </div>
</div>