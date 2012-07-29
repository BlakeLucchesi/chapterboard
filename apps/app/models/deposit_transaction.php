<?php defined('SYSPATH') or die('No direct script access.');

class Deposit_transaction_Model extends ORM {
  
  protected $belongs_to = array('deposit', 'deposit_account', 'site');
  
  // Each record belongs to a record from a different table.
  protected $belongs_to_polymorphic = array(
    'finance_payment' => 'id',
    'donation' => 'id'
  );
  
  public function collection_type() {
    switch ($this->object_type) {
      case 'campaign_donation':
        return 'Fundraising';
      case 'finance_payment':
        return 'Dues';
    }
  }
  
  public function details() {
    return sprintf('%s #%s', ucwords($this->payment_type), $this->transaction_id);
  }
  
  public function find_unpaid($site_id = NULL) {
    $site_id = is_null($site_id) ? kohana::config('chapterboard.site_id') : $site_id;
    return $this->where('site_id', $site_id)->where('deposit_id', 0)->orderby('created', 'ASC')->find_all();
  }
  
  public function find_pending_deposits($site_id = NULL) {
    $site_id = is_null($site_id) ? kohana::config('chapterboard.site_id') : $site_id;
    return $this->with('deposit_account')->select('sum(amount_payable) AS payable, deposit_account.*')->where('deposit_id', 0)->where('deposit_transactions.site_id', $site_id)->groupby('deposit_account_id')->find_all();
  }
  
  public function insert($fields) {
    foreach ($fields as $key => $value) {
      try {
        if ($key != 'id') {
          $this->$key = $value;
        }
      }
      catch (Exception $e) {
        // do nothing.
      }
    }
    return $this->save();
  }
  
  public function before_insert() {
    $this->deposit_id = 0;
    $this->created = date::to_db();
  }
  
  
}