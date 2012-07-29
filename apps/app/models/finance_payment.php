<?php defined('SYSPATH') or die('No direct script access.');

class Finance_payment_Model extends ORM {
  
  protected $belongs_to = array('user', 'site', 'finance_charge');

  protected $sorting = array('received' => 'DESC');

  public function is_editable() {
    return in_array($this->type, array('credit', 'echeck')) ? FALSE : TRUE;
  }

  /**
   * Format the payment note.
   */
  public function note() {
    if ($this->transaction_id) {
      return sprintf('#%s (%s)', $this->transaction_id, $this->method());
    }
    else if ($this->note) {
      $output = $this->note;
    }
    if ($this->type == 'check') {
      $output .= ' [Check';
      if ($this->check_no) {
        $output .= ' #'. $this->check_no;
      }
      $output .= ']';
    }
    else if ($this->type == 'cash') {
      $output .= ' [Cash]';
    }
    if ($output) {
      return $output;
    }
    return 'Payment Received';
  }
  
  public function method() {
    if ($this->type == 'echeck') {
      return 'E-Check';
    }
    else if ($this->type == 'credit') {
      return ucwords($this->card_type);
    }
    else {
      return ucwords($this->type);
    }
  }

  /**
   * Override the find_all() method to filter out results only for 
   * a particular site_id.
   */
  public function find_all($limit = NULL, $offset = NULL) {
    $this->where('finance_payments.site_id', kohana::config('chapterboard.site_id'));
    return parent::find_all($limit, $offset);
  }

  /**
   * Count all the payments for a site.
   */
  public function payments_count() {
    return $this->db->query("SELECT COUNT(*) count FROM finance_payments WHERE site_id = ?", array(kohana::config('chapterboard.site_id')))->current()->count;
  }

  /**
   * Validation.
   */
  public function validate(array &$array, $save = FALSE) {
    $array['amount'] = preg_replace('/[^0-9\.]/i', '', $array['amount']);
    $array['received'] = date::input_to_db($array['received']);
    $array = Validation::factory($array)
    ->pre_filter('trim')
    ->pre_filter('strtolower', 'type')
    ->add_rules('finance_charge_id', 'required', 'numeric')
    ->add_rules('deposit_account_id', 'numeric')
    ->add_rules('transaction_id', 'standard_text')
    ->add_rules('user_id', 'required', 'numeric')
    ->add_rules('amount', 'required', 'numeric')
    ->add_rules('collection_fee', 'numeric')
    ->add_rules('amount_payable', 'numeric')
    ->add_rules('type', 'standard_text')
    ->add_rules('card_type', 'standard_text')
    ->add_rules('check_no', 'numeric')
    ->add_rules('note', 'standard_text')
    ->add_rules('received', 'date')
    ->add_callbacks('amount', array($this, '_amount_check'));
    return parent::validate($array, $save);
  }
  
  /**
   * Make sure that we don't allow an overpayment when editing by
   * setting the amount to a value larger than the outstanding 
   * balance of the charge.
   */
  public function _amount_check(Validation &$array, $field) {
    if ($this->id) {
      $charge = ORM::factory('finance_charge_member')
                  ->where('user_id', $this->user_id)
                  ->where('finance_charge_id', $this->finance_charge_id)
                  ->find();
      if ($array[$field] > ($charge->balance() + $this->amount)) {
        $array->add_error($field, 'overpayment');
      }
      if (abs($array[$field]) == 0) {
        $array->add_error($field, 'zero');
      }
     }
  }
  
  /**
   * Before insert hook.
   */
  function before_insert() {
    $this->site_id = kohana::config('chapterboard.site_id');
    $this->created = date::to_db();
    $this->updated = $this->created;
    if ( ! $this->received) {
      $this->received = $this->created;
    }
  }
  
  /**
   * After Insert
   */
  function after_insert() {
    $this->update_paid();
    Cache::instance()->delete('user:balance:'. $this->user_id);
    Cache::instance()->delete('site:balances:'. kohana::config('chapterboard.site_id'));
  }
  
  function after_update() {
    $this->update_paid();
    Cache::instance()->delete('user:balance:'. $this->user_id);
    Cache::instance()->delete('site:balances:'. kohana::config('chapterboard.site_id'));
  }
  
  /**
   * Update paid -- When saving or inserting a new record
   * we need to make sure to sync the 'paid' attribute for
   * the related charge.  This keeps us from having to to do
   * more complex queries when looking for members with past
   * due charges.
   */
  function update_paid() {
    $member = ORM::factory('finance_charge_member')->where('user_id', $this->user_id)->where('finance_charge_id', $this->finance_charge_id)->find();
    $member->update_paid();
  }
  
  public function delete($id = NULL) {
    $member = ORM::factory('finance_charge_member')->where('user_id', $this->user_id)->where('finance_charge_id', $this->finance_charge_id)->find();
    parent::delete($id = NULL);
    $member->update_paid();
  }
}