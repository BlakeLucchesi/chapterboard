<div id="campaign-page" class="clearfix">
  <div class="split-left">
    <div class="heading clearfix">
      <h2 class="title"><?= $this->title ?></h2>
    </div>
    <div id="campaign-description" class="clearfix">
      <?php if ($this->campaign->picture): ?>
        <span class="campaign-picture"><?= theme::image('profile', $this->campaign->picture) ?></span>
      <?php endif ?>
      <?= format::html($this->campaign->body) ?>
    </div>
  </div>
  <div class="split-right">  
    
    <div class="block">
      <div id="share-links" class="clearfix">
        <div id="fb-root"></div><script src="http://connect.facebook.net/en_US/all.js#appId=241940205848548&amp;xfbml=1"></script><fb:like send="true" layout="button_count" width="80" show_faces="false" font="trebuchet ms"></fb:like>
        <a href="http://twitter.com/share" class="twitter-share-button" data-count="horizontal" data-via="ChapterBoard">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
        <a id="share-email" href="mailto:?subject=<?= $this->title ?>&body=<?= text::limit_words(strip_tags($this->campaign->body), 20) ?>%0A%0ADonate now online: <?= $this->campaign->url() ?>" alt="Share via Email" title="Share via Email">
          Share via email <?= html::image('/minis/email_go.png') ?>
        </a>
      </div>
    </div>
    
    <?= form::open(NULL, array('id' => 'payment-form')) ?>

    <?php if (isset($this->errors)): ?>
      <div class="system-messages">
        <div class="message-error">
          <h3 class="title">There were errors with your request, please make the suggested changes and try again:</h3>
          <ul>
            <?php foreach ($this->errors as $error): ?>
              <li><?= $error ?></li>
            <?php endforeach ?>
          </ul>
        </div>
      </div>
    <?php endif ?>
    
    <?= message::get() ?>
    
    <fieldset class="clearfix">
      <div id="payment-amount" class="block">
        <?php if ($this->campaign->payment_options): ?>
          <h3 class="title">1. Select Payment Amount</h3>
        <?php else: ?>
          <h3 class="title">1. Enter Payment Amount</h3>
        <?php endif ?>
        <?php if ($this->campaign->payment_options): ?>
          <?php foreach ($this->campaign->payment_options as $key => $option): ?>
            <div class="clearfix checkbox item"><label><?= form::radio('amount_option', $key, $this->form['amount_option'], 'amount="'. $option['value'] .'"') ?> <?= $option['label'] ?> - <?= money::display($option['value']) ?></label></div>
          <?php endforeach ?>
        <?php endif ?>
        <?php if ($this->campaign->payment_free_entry): ?>
          <?php if ($this->campaign->payment_options): ?>
            <div class="clearfix item">
              <label><?= form::radio('amount_option', -1, $this->form['amount_option']) ?> Custom Amount: </label>
              $ <?= form::input('amount', $this->form['amount'], 'class="amount small"') ?> <em>($10.00 minimum)</em>
            </div>
          <?php else: ?>
            <div class="clearfix item">
              <?= form::label('amount', 'Amount:') ?>
              $ <?= form::input('amount', $this->form['amount'], 'class="amount small"') ?> <em>($10.00 minimum)</em>
            </div>
          <?php endif ?>
        <?php endif ?>
      </div>
      <div class="block">
        <h3>2. Enter Billing Information</h3>
        <div class="clearfix">
          <div id="firstname-input" class="name-input">
            <?= form::label('first_name', 'First Name:*')?>
            <?= form::input('first_name', $this->form['first_name']) ?>
            <span class="error"></span>
          </div>
          <div id="lastname-input" class="name-input">
            <?= form::label('last_name', 'Last Name:*')?>
            <?= form::input('last_name', $this->form['last_name']) ?>
            <span class="error"></span>
          </div>
        </div>

        <div id="address-input" class="clearfix">
          <?= form::label('address', 'Address:*')?>
          <?= form::input('address', $this->form['address']) ?>
          <span class="error"></span>
        </div>

        <div class="clearfix">
          <div id="city-input">
            <?= form::label('city', 'City:*')?>
            <?= form::input('city', $this->form['city']) ?>
          </div>
          <div id="state-input">
            <?= form::label('state', 'State:*')?>
            <?= form::state_select('state', TRUE, $this->form['state']) ?>
          </div>
          <div id="zipcode-input">
            <?= form::label('zip', 'Zip code:*')?>
            <?= form::input('zip', $this->form['zip']) ?>
          </div>
        </div>
        <br />
        <div class="clearfix">
          <div class="name-input">
            <?= form::label('email', 'Email:*') ?>
            <?= form::input('email', $this->form['email']) ?>
          </div>
          <div class="name-input">
            <?= form::label('phone', 'Phone:*') ?>
            <?= form::input('phone', $this->form['phone']) ?>
          </div>
        </div>

        <div id="notes-input" class="clearfix">
          <?= form::label('note', 'Additional Notes:')?>
          <?= form::input('note', $this->form['note']) ?>
        </div>
      </div>

      <div class="block">
        <h3>3. Enter Payment Information</h3>

        <div id="payment-method" class="clearfix">
          <label><?= form::radio('payment_method', 'credit', $this->form['payment_method'] == 'credit' ? TRUE : FALSE) ?> Credit Card</label>
          <label><?= form::radio('payment_method', 'echeck', $this->form['payment_method'] == 'echeck' ? TRUE : FALSE) ?> Checking Account</label>
        </div>

        <div id="echeck" class="clearfix">
          <div class="clearfix">
            <div id="routing-number" class="clearfix">
              <?= form::label('RoutingNumber', 'Routing Number:*') ?>
              <?= form::input('RoutingNumber', $this->form['RoutingNumber'], 'autocomplete="off"') ?>
              <span class="form-tip"><?= html::thickbox_anchor('images/check.gif', html::image('minis/information.png')) ?></span>
            </div>
            <div id="account-number" class="clearfix">
              <?= form::label('AccountNumber', 'Account Number:*') ?>
              <?= form::input('AccountNumber', $this->form['AccountNumber'], 'autocomplete="off"') ?>
            </div>
          </div>
        </div><!-- #echeck -->

        <div id="credit-card" class="clearfix">
          <div class="clearfix">
            <div id="card-number-input">
              <?= form::label('card_num', 'Card Number:*') ?>
              <?= form::input('card_num', $this->form['card_num'], 'autocomplete="off"') ?>
              <span class="error"></span>
            </div>
            <div id="expiration-input">
              <?= form::label('Expiration:') ?>
              <?= form::month_select('month', FALSE, $this->form['month']) ?>
              <?= form::year_select('year', 10, $this->form['year']) ?>
              <span class="error"></span>
            </div>          
          </div>
          <div class="clearfix">
            <div id="card-cvv-input">
              <?= form::label('CVV#:') ?>
              <?= form::input('card_code', $this->form['card_code'], 'autocomplete="off"') ?>
              <span class="form-tip" title="Click here for help to find your card's CVV#"><?= html::thickbox_anchor('images/credit_card_cvv.gif', html::image('minis/creditcards.png')); ?></span>
            </div>
            <div id="card-types-image"><?= html::image('images/credit_card_logos.gif'); ?></div>
          </div>
        </div><!-- #credit-card -->

        <div id="payment-total" class="clearfix">
          <p>By clicking "Process Payment" you authorize the following amount to be charged to your card:</p>
          <p><strong>Total Payment Amount:</strong> $<span id="payment-total-amount">0.00</span></p>
          <div id="process-input">
            <div class="submit-button">
              <?= form::submit('post', 'Process Payment') ?>
            </div>
            <div class="status">
            <?= html::image('images/throbber.gif') ?>
            </div>
          </div>
        </div>

      </div>

    </fieldset>
    <?= form::close(); ?>
  </div>
</div>