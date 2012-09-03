<div id="chapterboard-support-form">
  <div class="heading clearfix">
    <h2><?= $this->title ?></h2>
  </div>
  
  <?= message::get(); ?>
  <div class="clearfix">
    <?= form::open() ?>
    <div class="split-left">
      <fieldset>
        <div class="clearfix">
          <?= form::label('body', 'Your Message:')?>
          <?= form::textarea('body', $this->form['body']) ?>
          <span class="error"><?= $this->errors['body'] ?></span>
        </div>
        <div class="right">
          <?= form::submit('send', 'Send') ?> or <?= html::anchor($this->input->get('redirect') ? $this->input->get('redirect') : 'dashboard', 'cancel') ?>
        </div>
      </fieldset>
    </div>
    <div class="split-right">
      <div class="help">
        <?php if ($this->method == 'feedback'): ?>
          <p><b>Is there something you feel we could do better?</b> Is there a feature you’d like to see on ChapterBoard? Is there a change you wish would be made? We want to know!</p>
          <p>To ensure your suggestions are best understood and fulfilled, please be sure to provide as much detail as possible regarding what you don’t like, what you would like done better, and what you would like added. Although not required, additional information, such as the following, will help us understand your suggestions and create the best features possible:</p>
          <ul>
            <li>Specific functionality about a feature you want to add (including examples if possible)
            <li>Specific examples of what you don’t like and why
            <li>Specific features or methods that YOU think could improve the problem
          </ul>
        <?php else: ?>
          <p><b>We’re here to help</b>. If you’ve come across any error messages, access issues, or bugs please let us know. In order to best assist you, we ask that you simply provide as much detail about the problem as possible, including:</p>
          <ul>
            <li>The action you were attempting</li>
            <li>The steps you took before you encountered the issue</li>
            <li>Any error message(s) you received (a copy of the message if possible)</li>
            <li>The date/time you encountered the issue</li>
            <li>Your browser name and version if you know it</li>
            <li>Any additional information that may be useful</li>
          </ul>
          <br />
          <p>Additionally, while we prefer responding via email, it’s always best to leave us your phone number and a good time to contact you, just in case we need to get in touch over the phone.</p>
        <?php endif ?>
        <br />
        <p>Thanks!</p>
      </div>
    </div>
    <?= form::close() ?>
  </div>
</div>