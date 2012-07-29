<?php defined('SYSPATH') or die('No direct script access.');

class Campaigns_Controller extends Public_Controller {
  
  public $template = 'campaign';
  
  public function _pre_controller() {
    css::add('styles/campaign.css');
  }
  
  public function index() {
    $this->title = 'Fundraising Campaigns';
  }
  
  public function form($site, $campaign) {
    javascript::add('scripts/finances.js');
    $this->site = $site;
    $this->campaign = $campaign;
    $this->view = 'campaigns/form';
    
    if ( ! $this->campaign->loaded)
      url::redirect(Kohana::config('app.public_url'));
    
    $this->title = $this->campaign->title;
        
    // Default form values.
    $this->form['month'] = date('m');
    $this->form['year'] = date('Y');
    $this->form['payment_method'] = 'credit';
    if ($_POST) {
      if ($_POST['amount_option'] > -1) {
        $_POST['item_label'] = $this->campaign->payment_options[$POST['amount_option']]['label'];
        $_POST['amount'] = $this->campaign->payment_options[$_POST['amount_option']]['value'];
      }

      // Setup payment form and card validation, processes credit card in last callback if no errors were found.
      $post = new Validation($_POST);
      $post->pre_filter('trim', TRUE);
      $post->add_rules('first_name');
      $post->add_rules('last_name');
      $post->add_rules('address', 'required', 'length[3,90]');
      $post->add_rules('city', 'standard_text');
      $post->add_rules('state', 'standard_text');
      $post->add_rules('zip', 'required', 'numeric', 'length[5,10]');
      $post->add_rules('item_label', 'blob');
      $post->add_rules('note', 'blob');
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
        message::add(TRUE, 'Thank you! Your payment has been recorded. You will receive an email receipt for your records shortly.');
        
        $transaction_id = $this->payment->get_transaction_id() == 'TEST TRANSACTION' ? '999999999999'. rand(0, 50000) : $this->payment->get_transaction_id();
        $total = $this->payment->get_value('amount');
        $fee = $post['payment_method'] == 'echeck' ? $this->site->fee_echeck : $this->site->fee_credit;
        $fee_rate = $fee / 100;
        $gross_profit = money::round($total * $fee_rate);
        
        // Setup payment record data to record the individual charge payments.
        $data = array(
          'campaign_id' => $this->campaign->id,
          'deposit_account_id' => $this->campaign->deposit_account_id,
          'transaction_id' => $transaction_id,
          'payment_type' => $post['payment_method'],
          'card_type' => $post['payment_method'] == 'credit' ? $this->_card_type($post['card_num']) : '',
          'collection_fee' => money::round($post['amount'] * $fee_rate),
        );
        $data['amount_payable'] = money::round($post['amount'] - $data['collection_fee']);
        $data = array_merge($post->as_array(), $data);

        log::system('campaign', sprintf('Campaign payment processed via %s for $%s (%s).', $post['payment_method'], $total, $this->site->name()));
        $payment = ORM::factory('campaign_donation');
        if ( ! $payment->validate($data, TRUE)) {
          log::system('campaign', 'Payment processed but failed to be recorded in deposit_transactions.', 'fatal', array('values' => $data->as_array(), 'errors' => $data->errors()));
        }
        
        // Log to Google Analytics.
        ga::add_item($transaction_id, sprintf('CAMPAIGN-%s', strtoupper($post['payment_method'])), 'Campaigns', 'Campaigns', $gross_profit, 1);
        ga::add_trans($transaction_id, 'Online Fundraising Campaigns', $gross_profit);
        ga::track_trans();
      }
      else {
        $this->form = $post->as_array();
        $this->errors = $post->errors('form_finance_campaign');
        log::system('campaign', sprintf("Campaign payment attempt failed validation for %s (%s).", $this->campaign->title, $this->site->name()), 'notice', array('form_values' => $this->form, 'errors' => $post->errors('form_finance_campaign')));
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
    // Set minimum value error only if overpaid error is not shown.
    $errors = $post->errors();
    if ($post[$field] < 10 && ! $errors[$field]) {
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
      $post['exp_date'] = $post['month'] . substr($post['year'], 2, 2); // Set exp_date for processing.
      $post['description'] = sprintf('%s: %s', $this->site->name(), $this->campaign->title);
      $post['site_id'] = $this->site->id;
      $total = $post['amount'];

      $this->payment->set_fields($post->as_array());

      // Add the total charge value.
      $this->payment->set_field('amount', $total);

      // Attempt to process the card.
      if ( ! $this->payment->process()) {
        log::system('payment', "Payment via {$post['payment_method']} failed to process donation for $$total.", 'error');
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