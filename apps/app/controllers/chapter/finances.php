<?php defined('SYSPATH') or die('No direct script access.');

class Finances_Controller extends Private_Controller {
  
  public $secondary = 'menu/finances';
  
  protected $payment; // Used to store the payment processor object.
  
  function _pre_controller() {
    javascript::add('scripts/finances.js');
  }
  
  /**
   * Show a summary of all members.
   */
  function index() {
    if ( ! A2::instance()->allowed('finance', 'manage'))
      Event::run('system.403');

    $this->title = 'Member Balances';
    $this->members = ORM::factory('finance_charge_member')->balances();
    
    if (request::is_ajax()) {
      $response['members'] = array();
      foreach ($this->members as $id => $member) {
        $response['members'][] = array(
          'id' => $id,
          'name' => $member->name,
          'balance' => money::display($member->balance),
          'past_due_balance' => money::display($member->overdue_amount),
          'phone' => preg_replace('/[^0-9]/i', '', $member->phone),
          'phone_formatted' => $member->phone
        );
        $past_due += $member->overdue_amount;
        $outstanding += $member->balance;
      }
      $response['total_outstanding'] = money::display($outstanding);
      $response['member_count'] = format::plural(count($this->members), '@count member - %amount', '@count members - %amount', array('%amount' => money::display($outstanding)));
      response::json(TRUE, null, $response);
    }
  }
  
  /**
   * Online dues collection and signup information.
   */
  
  /**
   * Send an email reminder about outstanding balances.
   */
  function reminder() {
    $this->title = 'Send Email Reminder';
    $this->members = ORM::factory('finance_charge_member')->balances();
    
    if ($post = $this->input->post()) {
      $subject = Kohana::lang('finance_reminder.members.subject');
      if ($this->site->collections_enabled()) {
        $message = Kohana::lang('finance_reminder.members.message_finances');
      }
      else {
        $message = Kohana::lang('finance_reminder.members.message_basic');
      }
      
      foreach ($this->members as $member) {
        $replacements = array(
          '!name' => $member->name,
          '!due_amount' => money::display($member->balance),
          '!pay_link' => url::base(),
        );
        $email = array(
          'subject' => strtr($subject, $replacements),
          'message' => strtr($message, $replacements),
        );
        email::notify($member->email, 'finance_charge_reminder', $email, $email['subject']);
      }
      message::add(TRUE, 'An email reminder has been sent to all members who have an outstanding balance.');
      url::redirect('finances/members');
    }
  }
    
  /**
   * Process a charge payment.
   */
  function payment() {
    $this->secondary = NULL;
    $this->title = 'Pay Online';
    if ( ! $this->site->collections_enabled()) // Make sure finances are enabled.
      Event::run('system.404');
    
    // Load the users outstanding charges.
    $this->charges = ORM::factory('finance_charge_member')->unpaid($this->user->id);
    if ($this->charges->count() == 0) {
      message::add(TRUE, 'You cannot make an online payment because you have no outstanding charges.');
      url::redirect('dues');
    }
    
    // Default form values.
    $this->form['month'] = date('m');
    $this->form['year'] = date('Y');
    $this->form['sum'] = '0.00';
    $this->form['payment_method'] = 'echeck';
    
    if ($_POST) {
      // Setup payment form and card validation, processes credit card in last callback if no errors were found.
      $post = new Validation($_POST);
      $post->pre_filter('trim', TRUE);
      $post->add_rules('first_name');
      $post->add_rules('last_name');
      $post->add_rules('address', 'required', 'length[3,90]');
      $post->add_rules('city', 'standard_text');
      $post->add_rules('state', 'standard_text');
      $post->add_rules('zip', 'required', 'numeric', 'length[5,10]');
      if ($post['payment_method'] == 'echeck') {
        $post->pre_filter(array('text', 'searchable'), 'RoutingNumber');
        $post->pre_filter(array('text', 'searchable'), 'AccountNumber');
        $post->pre_filter(array('text', 'searchable'), 'Phone');
        $post->add_rules('RoutingNumber', 'required', 'length[9]', 'numeric');
        $post->add_rules('AccountNumber', 'required', 'numeric');
        $post->add_rules('Phone', 'required', 'numeric');
      }
      elseif ($post['payment_method'] == 'credit') {
        $post->add_rules('card_num', 'required', 'numeric', 'credit_card[american express,mastercard,visa,discover]');
        $post->add_rules('month', 'required', 'numeric');
        $post->add_rules('year', 'required', 'numeric');
        $post->add_rules('card_code', 'required', 'numeric', 'length[3,4]');
        $post->add_callbacks('month', array($this, '_expiration_date'));
      }
      $post->add_callbacks('amount', array($this, '_amount_valid'));
      $post->add_callbacks('card_num', array($this, '_process')); // Fire off the card process request last.

      // Make sure the form passed validation and the card was processed succesfully.
      if ($post->validate()) {
        message::add(TRUE, 'Your payment has been recorded. You will receive an email receipt for your records. Thank you for paying your dues online.');
        
        $transaction_id = $this->payment->get_transaction_id() == 'TEST TRANSACTION' ? '999999999999'. rand(0, 50000) : $this->payment->get_transaction_id();
        $total = $this->payment->get_value('amount') ? $this->payment->get_value('amount') : $this->payment->get_value('Amount');
        $fee = $post['payment_method'] == 'echeck' ? $this->user->site->fee_echeck : $this->user->site->fee_credit;
        $fee_rate = $fee / 100;
        $gross_profit = money::round($total * $fee_rate);
        
        // Setup payment record data to record the individual charge payments.
        $data = array(
          'transaction_id' => $transaction_id,
          'user_id' => $this->user->id,
          'note' => 'Paid online.',
          'type' => $post['payment_method'] ? $post['payment_method'] : 'credit',
          'card_type' => $post['payment_method'] == 'credit' ? $this->_card_type($post['card_num']) : '',
        );
        
        foreach ($this->charges as $charge) {
          if ($post['amount'][$charge->id] > 0) {
            $data['finance_charge_id'] = $charge->finance_charge_id;
            $data['deposit_account_id'] = $charge->finance_charge->deposit_account_id;
            $data['amount'] = $post['amount'][$charge->id];
            $data['collection_fee'] = money::round($post['amount'][$charge->id] * $fee_rate);
            $data['amount_payable'] = money::round($post['amount'][$charge->id] - $data['collection_fee']);
            
            $payment = ORM::factory('finance_payment');
            if ($payment->validate($data, TRUE)) {
              ORM::factory('deposit_transaction')->insert(array(
                'object_type' => 'finance_payment',
                'object_id' => $payment->id,
                'site_id' => $payment->site_id,
                'deposit_account_id' => $payment->deposit_account_id,
                'transaction_id' => $payment->transaction_id,
                'payment_type' => $payment->type,
                'card_type' => $payment->card_type,
                'amount' => $payment->amount,
                'collection_fee' => $payment->collection_fee,
                'amount_payable' => $payment->amount_payable,
                'name' => $payment->user->name()
              ));
            }
            else {
              log::system('payment', 'Failed adding payment record chapter tracking.', 'error', array('errors' => $data->errors(), 'form_values' => $data));
            }
          }
        }
        
        // Log payment activity.
        log::system('payment', sprintf('Payment processed via %s for $%s by %s (%s).', $post['payment_method'], $total, $this->user->name(), $this->site->name()));
        ga::add_item($transaction_id, sprintf('COLLECTION-%s', strtoupper($post['payment_method'])), 'Dues', 'Dues', $gross_profit, 1);
        ga::add_trans($transaction_id, 'Online Collections', $gross_profit);
        ga::track_trans();
        
        if (request::is_ajax()) {
          response::json(TRUE, 'Your payment has been recorded.');
        }
        url::redirect('dues');
      }
      else {
        $this->form = $post->as_array();
        foreach ($this->form['amount'] as $value) {
          $this->form['sum'] += $value;
        }
        $this->form['sum'] = money::display($this->form['sum']);
        $this->errors = $post->errors('form_finance_payment');
        log::system('payment', sprintf("Payment attempt failed validation for %s (%s).", $this->user->name(), $this->site->name()), 'notice', array('user_id' => $this->user->id, 'form_values' => $this->form, 'errors' => $post->errors('form_finance_payment')));
        if (request::is_ajax()) {
          if (method_exists($this->payment, 'get_response_reason') && $this->payment->get_response_reason()) {
            $this->errors['message'] = $this->payment->get_response_reason();
          }
          else {
            $this->errors['message'] = array_shift($this->errors);
          }
          $this->errors['message'] = strip_tags($this->errors['message']);
          response::json(FALSE, 'Payment attempt failed.', $this->errors);
        }
      }
    }
  }
  
  /**
   * Validate that the expiration date has not passed.
   */
  public function _expiration_date(Validation $post) {
    $now = date('Ym');
    if ($post['year'].$post['month'] < $now) {
      $post->add_error('month', 'expired');
    }
  }
  
  /**
   * Validate all of the amount values.
   *
   * Make sure no one over pays, or tries to pay for a charge
   * that does not belong to them.
   */
  public function _amount_valid(Validation $post, $field) {
    $total = 0;
    foreach ($post[$field] as $charge_id => $amount) {
      if ($amount > 0) {
        $charge = ORM::factory('finance_charge_member', $charge_id);
        if ($amount > $charge->balance()) {
          $post->add_error($field, 'overpaid');
        }
        else {
          $total += $amount;
        }
      }
    }
    // Set minimum value error only if overpaid error is not shown.
    $errors = $post->errors();
    if ($total < 10 && ! $errors[$field]) {
      $post->add_error($field, 'minimum');
    }
  }
  
  /**
   * Process the credit card.
   *
   * This is the last validation callback to run.  If we see any other 
   * errors we abort so that we don't try to process the card with known
   * errors.
   */
  public function _process(Validation $post) {
    $errors = $post->errors();
    if ( ! $errors) { // Don't proceed to process card if there were errors found above.
      
      // Setup payment
      $this->payment = new Payment($post['payment_method']);

      // Process form values into $post before adding to Payment.
      if ($post['payment_method'] == 'echeck') {
        $post['Address1'] = $post['address'];
        $post['Name'] = sprintf('%s %s', $post['first_name'], $post['last_name']);
      }
      else {
        $post['exp_date'] = $post['month'] . substr($post['year'], 2, 2); // Set exp_date for processing.
      }
      $post['description'] = $this->site->name();
      $post['cust_id'] = $this->user->id;
      $post['site_id'] = $this->user->site_id;

      $this->payment->set_fields($post->as_array());

      // Add the total charge value.
      foreach ($post['amount'] as $value) {
        $total += $value;
      }
      $this->payment->set_field('amount', $total);

      // Attempt to process the card.
      if ( ! $this->payment->process()) {
        log::system('payment', "Payment via {$post['payment_method']} failed to process transaction for $$total. User: {$this->user->email} ({$this->user->id})", 'error');
        $post->add_error('card_num', 'failed_processing');
      }
    }
  }
  
  public function _card_type($card_number) {
    switch (substr($card_number, 0, 1)) {
      case 3:
        return 'American Express';
      case 4:
        return 'Visa';
      case 5:
        return 'MasterCard';
      default:
        return 'Discover';
    }
  }
  
}