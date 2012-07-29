<div id="breadcrumbs">
  <?= html::anchor('finances/fundraising', 'Fundraising') ?> &raquo; <?= $this->campaign ? 'Edit Campaign' : 'Create Campaign' ?>
</div>
<div class="heading clearfix">
  <h2 class="title"><?= $this->title ?></h2>
</div>

<?= message::get(); ?>

<div id="campaign-form" class="clearfix">
  <fieldset>
    <?= form::open_multipart() ?>
      <div class="clearfix">
        <div class="split-left">
          <div class="clearfix">
            <?= form::label('title', 'Title:*')?>
            <?= form::input('title', $this->form['title']) ?>
            <div class="error"><?= $this->errors['title'] ?></div>
          </div>

          <div class="clearfix">
            <?= form::label('deposit_account_id', 'Deposit Account:*')?>
            <?= form::dropdown('deposit_account_id', $this->deposit_account_options, $this->form['deposit_account_id']) ?>
            <div class="error"><?= $this->errors['deposit_account_id'] ?></div>
          </div>

          <div class="clearfix">
            <?= form::label('slug', 'URL:*')?>
            https://www.payrally.com/<?= $this->site->slug() ?>/<?= form::input('slug', $this->form['slug'], 'class="small"') ?>
            <div class="error"><?= $this->errors['slug'] ?></div>
          </div>

          <div class="clearfix">
            <?= form::label('goal', 'Funding Goal:') ?>
            $ <?= form::input('goal', $this->form['goal'], 'class="small amount"') ?>
            <div class="error"><?= $this->errors['goal'] ?></div>
          </div>

          <div class="clearfix">
            <?= form::label('expires', 'End Date:')?>
            <?= form::input('expires', $this->form['expires'], 'class="small date-pick"') ?>
            <span class="error"><?= $this->errors['expires'] ?></span>
          </div>

          <div class="clearfix">
            <?= form::label('picture', 'Picture:')?>
            <div id="picture-upload-field">
              <?php if ($this->campaign->picture): ?>
                <?= theme::image('profile', $this->campaign->picture) ?><br />
                <label><?= form::checkbox('picture_remove', TRUE) ?> Remove picture</label><br />
              <?php endif ?>
              <?= form::upload('picture', $this->form['picture']) ?>
            </div>
            <div class="error"><?= $this->errors['picture'] ?></div>
          </div>
        </div>

        <div class="split-right">
          <h4 class="title">Payment Amount Options</h4>
          <p>Use the settings below to configure the payment options that users see when they visit your campaign page:</p>
          <div class="clearfix checkbox">
            &nbsp;&nbsp;<label><?= form::checkbox('payment_free_entry', TRUE, $this->form['payment_free_entry']) ?> Allow user to enter their own amount</label>
          </div>
          <div class="clearfix">
            <table>
              <thead>
                <tr>
                  <th>Preset Options</th>
                  <th>Amount</th>
                </tr>
              </thead>
              <?php foreach ($this->form['payment_options'] as $key => $value): ?>
                <tr>
                  <td><?= form::input("payment_options[$key][label]", $this->form['payment_options'][$key]['label'], $key == 0 ? 'placeholder="Event t-shirt"' : '') ?></td>
                  <td>$ <?= form::input("payment_options[$key][value]", $this->form['payment_options'][$key]['value'], 'class="amount mini"') ?></td>
                </tr>
              <?php endforeach ?>
            </table>
          </div>
        </div>
      </div>

      <div class="clearfix">
        <div class="error"><?= $this->errors['body'] ?></div>
        <?= form::label('body', 'Description:*')?>
        <em>You may include HTML such as video embed codes if you like.</em>
        <?= form::textarea('body', $this->form['body'], 'rows="30"') ?>
      </div>
      
      <div class="right">
        <?php if ($this->campaign->loaded): ?>
          <?= form::submit('add', 'Save Changes') ?>
        <?php else: ?>
          <?= form::submit('add', 'Create Campaign') ?>
        <?php endif ?>
        or <?= html::anchor('finances/fundraising', 'cancel') ?>
      </div>
    <?= form::close() ?>
  </fieldset>
</div>