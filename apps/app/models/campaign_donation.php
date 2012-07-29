<?php defined('SYSPATH') or die('No direct script access.');

class Campaign_donation_Model extends ORM {
  
  protected $belongs_to = array('campaign', 'deposit_account', 'site');
  
  protected $load_with = array('campaign');
  
  protected $sorting = array('created' => 'DESC');
  
  public function find_pending_by_site($site_id = NULL, $limit = NULL, $offset = NULL) {
    $site_id = is_null($site_id) ? kohana::config('chapterboard.site_id') : $site_id;
    return $this->where('campaign.site_id', $site_id)->where('donation_deposit_id', NULL)->find_all($limit, $offset);
  }
  
  public function find_by_site($site_id = NULL, $limit = NULL, $offset = NULL) {
    return $this->where('campaign.site_id', $site_id)->find_all($limit, $offset);
  }
  
  public function find_by_campaign_id($form_id, $limit = NULL, $offset = NULL) {
    return $this->where('campaign_id', $form_id)->find_all($limit, $offset);
  }
  
  public function name() {
    return sprintf('%s %s', $this->first_name, $this->last_name);
  }
  public function address() {
    return sprintf('%s, %s, %s %s', $this->address, $this->city, $this->state, $this->zipcode);
  }
  
  public function payment_type() {
    return $this->payment_type == 'credit' ? 'Credit' : 'E-Check';
  }

  /**
   * Validation.
   */
  public function validate(array &$array, $save = FALSE) {
    $array = Validation::factory($array)
    ->pre_filter('trim')
    ->add_rules('campaign_id', 'required', 'numeric')
    ->add_rules('deposit_account_id', 'numeric')
    ->add_rules('transaction_id', 'numeric')
    ->add_rules('payment_type', 'standard_text')
    ->add_rules('card_type', 'standard_text')
    ->add_rules('item_label', 'blob')
    ->add_rules('amount', 'required', 'numeric')
    ->add_rules('collection_fee', 'numeric')
    ->add_rules('amount_payable', 'numeric')
    ->add_rules('first_name', 'blob')
    ->add_rules('last_name', 'blob')
    ->add_rules('email', 'blob')
    ->add_rules('phone', 'phone[10]')
    ->add_rules('address', 'blob')
    ->add_rules('city', 'blob')
    ->add_rules('state', 'standard_text')
    ->add_rules('zip', 'standard_text')
    ->add_rules('note', 'blob');
    return parent::validate($array, $save);
  }

  public function before_insert() {
    $this->created = date::to_db();
    $this->updated = $this->created;
    if ( ! $this->amount_payable) {
      $this->amount_payable = $this->amount;
    }
  }

  public function after_insert() {
    $data = array(
      'object_type' => 'campaign_donation',
      'object_id' => $this->id,
      'site_id' => $this->campaign->site->id,
      'name' => $this->name(),
    );
    $data = array_merge($this->as_array(), $data);
    $transaction = ORM::factory('deposit_transaction')->insert($data);
  }

  public function before_update() {
    $this->updated = date::to_db();
  }

}