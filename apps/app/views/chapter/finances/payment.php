<div id="finances">

  <?= message::get() ?>
  
  <?php if ( ! empty($this->errors)): ?>
    <div class="system-messages">
      <div class="message-error">
        <h3><?= Kohana::lang('form_finance_payment.message') ?></h3>
        <ul>
          <?php foreach ($this->errors as $field => $error): ?>
            <li><?= $error ?></li>
          <?php endforeach ?>
          <?php if (method_exists($this->payment, 'get_response_reason') && $this->payment->get_response_reason()): ?>
            <li><?= $this->payment->get_response_reason(); ?></li>
          <?php endif ?>
        </ul>
      </div>
    </div>
  <?php endif ?>
  
  <?= form::open(NULL, array('id' => 'payment-form')) ?>
  <div class="clearfix">
    <h3>1. Enter Payment Amount</h3>
    <table id="payment-table">
      <thead>
        <tr>
          <th class="title">Charge</th>
          <th>Due Date</th>
          <th class="right amount">Amount Due</th>
          <th class="right amount">Enter Amount</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($this->charges as $charge): ?>
          <tr class="hoverable">
            <td><?= $charge->title ?></td>
            <td><?= date::display($charge->due, 'M d, Y', FALSE) ?></td>
            <td class="right"><?= money::display($charge->balance()) ?></td>
            <td class="right"><?= form::input('amount['.$charge->id.']', $this->form['amount'][$charge->id] ? $this->form['amount'][$charge->id] : '0.00', 'class="amount-input"') ?></td>
          </tr>
        <?php endforeach ?>
      </tbody>
      <tfoot>
        <tr>
          <td class="right" colspan="3">Total:</td>
          <td class="right" id="pay-total"><?= $this->form['sum'] ?></td>
        </tr>
      </tfoot>
    </table>
  </div>
  
  <fieldset class="clearfix">
    <div class="split-left">
      <h3>2. Enter Billing Information</h3>
      <div class="clearfix">
        <div id="firstname-input" class="name-input">
          <?= form::label('first_name', 'First Name:')?>
          <?= form::input('first_name', $this->form['first_name']) ?>
          <span class="error"></span>
        </div>
        <div id="lastname-input" class="name-input">
          <?= form::label('last_name', 'Last Name:')?>
          <?= form::input('last_name', $this->form['last_name']) ?>
          <span class="error"></span>
        </div>
      </div>
      
      <div id="address-input" class="clearfix">
        <?= form::label('address', 'Address:')?>
        <?= form::input('address', $this->form['address']) ?>
        <span class="error"></span>
      </div>
      
      <div class="clearfix">
        <div id="city-input">
          <?= form::label('city', 'City:')?>
          <?= form::input('city', $this->form['city']) ?>
          <span class="error"></span>
        </div>
        <div id="state-input">
          <?= form::label('state', 'State:')?>
          <?= form::state_select('state', TRUE, $this->form['state']) ?>
          <span class="error"></span>
        </div>
        <div id="zipcode-input">
          <?= form::label('zip', 'Zip code:')?>
          <?= form::input('zip', $this->form['zip']) ?>
          <span class="error"></span>
        </div>
      </div>
      
    </div>
    
    <div class="split-right">
      <h3>3. Enter Payment Information</h3>
      
      <div id="payment-method" class="clearfix">
        <label><?= form::radio('payment_method', 'echeck', $this->form['payment_method'] == 'echeck' ? TRUE : FALSE) ?> Checking Account</label>
        <label><?= form::radio('payment_method', 'credit', $this->form['payment_method'] == 'credit' ? TRUE : FALSE) ?> Credit Card</label>
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
        <div class="clearfix">
          <div class="clearfix">
            <?= form::label('Phone', 'Phone Number:*')?>
            <?= form::input('Phone', $this->form['Phone']) ?>
            <span class="error"><?= $this->errors['Phone'] ?></span>
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
      
      <div id="process-input">
        <div class="submit-button">
          <?= form::submit('post', 'Process Payment') ?>
        </div>
        <div class="status">
        <?= html::image('images/throbber.gif') ?>
        </div>
      </div>
      
    </div>
  
  </fieldset>
  <?= form::close(); ?>
</div>
