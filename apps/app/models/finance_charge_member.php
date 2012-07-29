<?php defined('SYSPATH') or die('No direct script access.');

class Finance_charge_member_Model extends ORM {
  
  protected $belongs_to = array('finance_charge', 'user');
  
  protected $foreign_key = array('payments' => 'finance_charge_id');
  
  protected $load_with = array('user');
  
  protected $sorting = array('user.searchname' => 'ASC');
  
  public function find_by_user_charge($user_id, $charge_id) {
    return $this->where('user_id', $user_id)->where('finance_charge_id', $charge_id)->find();
  }
  
  /**
   * Return the balance remaining for a charge.
   */
  public function balance() {
    return $this->amount - $this->payments->sum('amount');
  }
  
  /**
   * Return a set of charges for the given member.
   */
  public function charges($user_id, $paid = FALSE, $order = 'ASC') {
    $this->with('finance_charge');
    $this->where('finance_charge_members.user_id', $user_id);
    $this->where('paid', $paid);
    $this->orderby('paid', 'ASC');
    $this->orderby('finance_charge.due', $order);
    return $this->find_all();
  }
  
  /**
   * Balances -- Builds a structured array that shows the balances for all members with a balance.
   */
  public function balances() {
    // if ($cached = Cache::instance()->get('site:balances:'. kohana::config('chapterboard.site_id'))) {
    //   return $cached;
    // }
    $results = array();
    
    // Gather a list of all members who have unpaid charges.
    $charges = $this
      ->with('user')
      ->with('user:profile')
      ->select('SUM(finance_charge_members.amount) as charge_total, finance_charge_members.*')
      ->groupby('finance_charge_members.user_id')
      ->where('paid', 0)
      ->where('finance_charge_members.site_id', kohana::config('chapterboard.site_id'))
      ->find_all();

    // Build our base result object with total charges, payments, and outstanding balance.
    foreach ($charges as $charge) {
      $results[$charge->user_id] = (object) array(
        'user_id' => $charge->user->id,
        'charge_total' => $charge->charge_total,
        'paid_amount' => 0,
        'balance' => $charge->charge_total,
        'name' => $charge->user->name(),
        'phone' => $charge->user->phone(),
        'type' => $charge->user->type(),
        'email' => $charge->user->email
      );
    }
    
    // After we have our initial sorted list of members with unpaid charges, disable
    // the default sorting because it causes SQL errors.
    $this->sorting = array();
    
    
    // Gather a list of all the payments members have made on their unpaid charges. Subtract payment amounts from balance.
    $payments = $this
      ->select('SUM(fp.amount) AS paid_amount, fp.user_id')
      ->join('finance_payments AS fp', array('fp.finance_charge_id' => 'finance_charge_members.finance_charge_id', 'fp.user_id' => 'finance_charge_members.user_id'), 'LEFT')
      ->groupby('finance_charge_members.user_id')
      ->where('paid', 0)
      ->where('finance_charge_members.site_id', kohana::config('chapterboard.site_id'))
      ->find_all();
    foreach ($payments as $payment) {
      $results[$payment->user_id]->paid_amount = $payment->paid_amount;
      $results[$payment->user_id]->balance -= $payment->paid_amount;
    }

    // Calculate late charge values and payments made on those late charges.
    $past_due_charges = $this
      ->with('finance_charge')
      ->select('SUM(finance_charge_members.amount) AS overdue_amount, finance_charge_members.user_id as user_id')
      ->groupby('user_id')
      ->where('finance_charge.due <', date::now('now', 'Y-m-d'))
      ->where('finance_charge_members.site_id', kohana::config('chapterboard.site_id'))
      ->where('paid', 0)
      ->find_all();
    foreach ($past_due_charges as $charge) {
      $results[$charge->user_id]->overdue_amount = $charge->overdue_amount;
    }
    
    // Calculate the payments made on the past due charges.
    $past_due_payments = $this
      ->select('SUM(fp.amount) AS paid_amount, fp.user_id as user_id')
      ->join('finance_charges', array('finance_charges.id' => 'finance_charge_members.finance_charge_id'), NULL, 'LEFT')
      ->join('finance_payments AS fp', array('fp.finance_charge_id' => 'finance_charge_members.finance_charge_id', 'fp.user_id' => 'finance_charge_members.user_id'), NULL, 'LEFT')
      ->groupby('finance_charge_members.user_id')
      ->where('fp.user_id IS NOT NULL')
      ->where('finance_charges.due <', date::now('now', 'Y-m-d'))
      ->where('paid', 0)
      ->where('finance_charge_members.site_id', kohana::config('chapterboard.site_id'))
      ->find_all();
    foreach ($past_due_payments as $payment) {
      if($payment->user_id) {
        $results[$payment->user_id]->overdue_amount -= $payment->paid_amount;
      }
    }
    Cache::instance()->set('site:balances:'. kohana::config('chapterboard.site_id'), $results);
    return $results;
  }
  
  /**
   * If $user_id is given, returns any unpaid charges for that user.
   * Otherwise, a result of every member with a balance, along with
   * their balance is returned.
   * 
   * @return ORM_Iterator
   */
  public function unpaid($user_id = NULL) {
    // Return results for a specific user.
    if ($user_id) {
      return $this
        ->with('finance_charge')
        ->where('finance_charge_members.user_id', $user_id)
        ->where('paid', 0)
        ->orderby('finance_charge.due', 'ASC')
        ->find_all();
    }
    // Return list of members with unpaid charges.
    else {
      return $this
        ->with('user')
        ->with('user:profile')
        ->with('finance_charge')
        ->select('SUM(finance_charge_members.amount) as balance')
        ->groupby('finance_charge_members.user_id')
        ->where('paid', 0)
        ->where('user.site_id', kohana::config('chapterboard.site_id'))
        ->find_all();
    }
  }
  
  public function get($charge_id, $user_id) {
    return $this->where('finance_charge_id', $charge_id)->where('user_id', $user_id)->find();
  }
  
  /**
   * Validation.
   */
  public function validate(array &$array, $save = FALSE) {
    $array = Validation::factory($array)
    ->pre_filter('trim')
    ->pre_filter('money::cleanse', 'amount')
    ->pre_filter('abs', 'amount')
    ->add_rules('finance_charge_id', 'required', 'numeric')
    ->add_rules('user_id', 'required', 'numeric')
    ->add_rules('amount', 'required', 'numeric')
    ->add_callbacks('user_id', array($this, '_user_check'));
    return parent::validate($array, $save);
  }
  
  /**
   * Make sure that we aren't trying to add the same charge to a member twice.
   */
  public function _user_check(Validation $array, $field) {
    $exists = $this->db->query("SELECT * FROM finance_charge_members WHERE finance_charge_id = ? AND user_id = ?", array($array['finance_charge_id'], $array['user_id']))->count();
    if ($exists) {
      $array->add_error($field, 'duplicate');
    }
  }
  
  /**
   * Before insert function.
   */
  public function before_insert() {
    $this->site_id = kohana::config('chapterboard.site_id');
    $this->created = date::to_db();
  }
  
  /**
   * After insert.
   */
  public function after_insert() {
    $this->update_paid();
    Cache::instance()->delete('user:balance:'. $this->user_id);
    Cache::instance()->delete('site:balances:'. kohana::config('chapterboard.site_id'));
  }
  
  public function after_update() {
    $this->after_insert();
  }
  
  /**
   * Update paid or not.
   */
  function update_paid() {
    // Sum the member's payments
    $sum = $this->db
      ->select('SUM(amount) as paid')
      ->from('finance_payments')
      ->where('finance_charge_id', $this->finance_charge_id)
      ->where('user_id', $this->user_id)
      ->groupby('finance_charge_id')->get();
    $paid = $sum->current()->paid;

    // Update the record based on whether its been completely paid or not.
    $this->db->update(
      'finance_charge_members',
      array('paid' => $this->amount == $paid ? TRUE : FALSE),
      array('finance_charge_id' => $this->finance_charge_id, 'user_id' => $this->user_id)
    );
  }
  
  /**
   * ORM->delete() Override
   *
   * Perform checking to make sure that payments for this charge
   * have not been made. Otherwise you lose track of the payment
   * and thus money is _missing_ in the system.
   *
   * Also, the action must now be completed on a loaded object so that we
   * can use the ORM relationship to count the number of payments made for the
   * charge we are trying to delete.
   *
   * @param $id int Optional primary key id to delete if object is not loaded.
   *
   * @return boolean True on success, False on fail.
   */
  function delete($id = NULL) {
  	if ($id === NULL AND $this->loaded) {
			// Use the the primary key value
			$id = $this->object[$this->primary_key];
		}
		
		// If there are payments associated with the charge we cannot delete it.
    if ($this->payments->count()) {
      return FALSE;
    }

		// Delete this object
		$this->db->where($this->primary_key, $id)->delete($this->table_name);

    $this->clear(); // Empty the object
		return TRUE;
  }
  
  public function __get($column) {
    if ($column == 'payments') {
      // Join the payments table based on the user_id and charge_id -- IMPORTANT!!!
      return ORM::factory('finance_payment')
        ->where('user_id', $this->user_id)
        ->where($this->foreign_key($column), $this->object['finance_charge_id'])
        ->find_all();
    }
    else if ($column == 'title') {
      return $this->finance_charge->$column;
    }
    else if ($column == 'due') {
      return $this->finance_charge->$column;
    }
    return parent::__get($column);
  }
}