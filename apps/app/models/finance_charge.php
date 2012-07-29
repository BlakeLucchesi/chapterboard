<?php defined('SYSPATH') or die('No direct script access.');

class Finance_charge_Model extends ORM implements Acl_Resource_Interface {
  
  protected $belongs_to = array('site', 'user', 'budget', 'deposit_account');
  
  protected $has_many = array('finance_charge_members', 'finance_payments');

  protected $sorting = array('due' => 'DESC');

  /**
   * Find all by site.
   */
  public function find_all_by_site($site_id = NULL, $limit = NULL, $offset = NULL) {
    $site_id = is_null($site_id) ? kohana::config('chapterboard.site_id') : $site_id;
    $this->where('site_id', $site_id);
    return parent::find_all($limit, $offset);
  }
  
  /**
   * Formatted late fee amount.
   */
  public function late_fee_text() {
    switch ($this->late_fee_type) {
      case 'percent':
        return sprintf('%s%%', number_format($this->late_fee));
      case 'amount':
        return money::display($this->late_fee);
      default:
        return '';
    }
  }
  
  /**
   * Assign charge to members.
   */
  public function assign_members($data) {
    // Make sure that the user is a member of the current users site.
    $users = $this->db->select('id')->from('users')->where('site_id', kohana::config('chapterboard.site_id'))->where('status', 1)->get();
    foreach ($users as $user) {
      $ids[$user->id] = $user->id;
    }
    
    // Loop through checked members as submitted by the user.
    foreach ($data['members'] as $user_id => $value) {
      if ($value && array_key_exists($user_id, $ids)) {
        $post = array(
          'finance_charge_id' => $this->id,
          'user_id' => $user_id,
          'amount' => $this->amount,
        );
        $charge = ORM::factory('finance_charge_member');
        $charge->validate($post, TRUE);
      }
    }
  }
  
  /**
   * Notify members with balance.
   */
  public function notify_members() {
    $from = array($this->site->user->email, $this->site->user->name());
    $subject = Kohana::lang('finance_reminder.charge.subject');
    if ($this->site->collections_enabled()) {
      $message = Kohana::lang('finance_reminder.charge.message_finances');
    }
    else {
      $message = Kohana::lang('finance_reminder.charge.message_basic');
    }

    foreach ($this->finance_charge_members as $member) {
      if ( ! $member->paid) {
        $replacements = array(
          '!name' => $member->user->name(),
          '!due_date' => date::display($member->due, 'M d, Y', FALSE),
          '!due_amount' => money::display($member->balance()),
          '!charge_title' => $member->title,
          '!pay_link' => url::base(),
        );
        $email = array(
          'subject' => strtr($subject, $replacements),
          'message' => strtr($message, $replacements),
        );
        email::announcement($member->user->email, $from, 'finance_charge_reminder', $email, $email['subject']);
      }
    }
    return TRUE;
  }
  
  /**
   * Assign any late fees. Find all charges that were due yesterday that also
   * have an automatic late fee set.
   *
   * @return number of late fees assigned.
   */
  public function assess_late_fees() {
    // Setup finance_charge record.
    $late_charge = ORM::factory('finance_charge');
    $data = array(
      'title' => sprintf('Late Fee: %s', $this->title),
      'due' => date::to_db(),
      'site_id' => $this->site_id,
      'user_id' => $this->user_id,
      'amount' => $this->late_fee_type == 'percent' ? number_format($this->amount * $this->late_fee / 100, 2) : $this->late_fee, // Fix to use percentage as well.
      'budget_id' => $this->budget_id,
      'deposit_account_id' => $this->deposit_account_id
    );
    $late_charge->validate($data, TRUE);
      
    // Assess charge to the individual members.
    foreach ($this->finance_charge_members as $member_charge) {
      $charge = ORM::factory('finance_charge_member');
      $data = array(
        'finance_charge_id' => $late_charge->id,
        'site_id' => $this->site_id,
        'user_id' => $member_charge->user_id,
        'amount' => $this->late_fee_type == 'percent' ? number_format($member_charge->balance() * $this->late_fee / 100, 2) : $this->late_fee,      
      );
      $charge->validate($data, TRUE);
    }
    
    // Send out emails to all members who have received the late fee.
    $late_charge->notify_members();
    
    // Update late_fee_assessed record so we don't assess this late fee again.
    $this->late_fee_assessed = TRUE;
    $this->save();
  }
  
  
  /**
   * Get details.
   */
  public function details() {
    foreach ($this->finance_charge_members as $member) {
      $details['members'][$member->user_id] = (object) array(
        'finance_charge_id' => $member->id,
        'user_id' => $member->user_id,
        'name' => $member->user->name(),
        'type' => $member->user->type(),
        'collected' => $member->payments->sum('amount'),
        'outstanding' => $member->amount - $member->payments->sum('amount'),
        'total' => $member->amount,
        'paid' => $member->paid,
      );
      $details['totals']['collected'] += $details['members'][$member->user_id]->collected;
      $details['totals']['outstanding'] += $details['members'][$member->user_id]->outstanding;
      $details['totals']['total'] += $details['members'][$member->user_id]->total;
    }
    return $details;
  }
  
  /**
   * Provide custom getters.
   */
  public function __get($column) {
    switch ($column) {
      case 'expected':
      case 'total':
        $total = $this->db->query("SELECT SUM(amount) as total FROM finance_charge_members WHERE finance_charge_id = ?", array($this->id))->current()->total;
        return $total;
      case 'collected':
        $collected = $this->db->query("SELECT SUM(amount) as collected FROM finance_payments WHERE finance_charge_id = ?", array($this->id))->current()->collected;
        return $collected;
      case 'fees':
        return $this->db->query("SELECT SUM(collection_fee) as fee FROM finance_payments WHERE finance_charge_id = ?", array($this->id))->current()->fee;
      case 'outstanding':
        return $this->total - $this->collected;
      case 'percentage':
        return $this->collected / $this->total * 100;
    }
    return parent::__get($column);
  }
  
  /**
   * Validation.
   */
  public function validate(array &$array, $save = FALSE) {
    $array['amount'] = preg_replace('/[^0-9\.]/i', '', $array['amount']);
    $array['late_fee'] = preg_replace('/[^0-9\.]/i', '', $array['late_fee']);
    $array['due'] = date::input_to_db($array['due']);
    if ($array['late_fee_type'] == '') {
      unset($array['late_fee']);
      unset($array['late_fee_type']);
    }
    $array = Validation::factory($array)
    ->pre_filter('trim')
    ->add_callbacks('amount', array($this, '_check_amount'))
    ->add_callbacks('budget_id', array($this, '_check_budget'))
    ->add_callbacks('deposit_account_id', array($this, '_check_deposit_account'))
    ->add_rules('title', 'required', 'standard_text')
    ->add_rules('due', 'required', 'date')
    ->add_rules('amount', 'required', 'numeric')
    ->add_rules('budget_id', 'numeric')
    ->add_rules('deposit_account_id', 'numeric')
    ->add_rules('user_id', 'numeric')
    ->add_rules('late_fee', 'numeric')
    ->add_rules('late_fee_type', 'standard_text');
    return parent::validate($array, $save);
  }
  
  public function _check_amount(Validation $array, $field) {
    if ($array[$field] <= 0) {
      $array->add_error($field, 'zero_negative');
    }
  }
  
  public function _check_budget(Validation $array, $field) {
    if ($array[$field] > 0) {
      $budget = ORM::factory('budget', $array[$field]);
      if ($budget->site_id != kohana::config('chapterboard.site_id')) {
        $array->add_error($field, 'site_id');
      }
    }
  }
  
  public function _check_deposit_account(Validation $array, $field) {
    if ($array[$field] > 0) {
      $deposit_account = ORM::factory('deposit_account')->where('id', $array[$field])->where('status', 1)->find();
      if ($deposit_account->site_id != kohana::config('chapterboard.site_id')) {
        $array->add_error($field, 'site_id');
      }
    }
    else {
      $site = ORM::factory('site', kohana::config('chapterboard.site_id'));
      if ($site->collections_enabled()) {
        $array->add_error($field, 'required');
      }
    }
  }
  
  /**
   * ORM->before_insert()
   */
  public function before_insert() {
    if ( ! $this->user_id) {
      $this->user_id = kohana::config('chapterboard.user_id');
    }
    if ( ! $this->site_id) {
      $this->site_id = kohana::config('chapterboard.site_id');
    }
    $this->created = date::to_db();
    $this->updated = date::to_db();
  }
  
  /**
   * ORM->before_update()
   */
  public function before_update() {
    $this->updated = date::to_db();
  }
  
  /**
   * Update a charge.
   *
   * In order to do this, we must validate the charge form and then
   * make adjustments ONLY to the members who have not made a payment
   * on this charge.  This is important because if someone has made a 
   * payment and the total charge amount is set to below what they paid
   * then the user has overpaid and there is no way to reconcile this.
   */
  public function update(array &$array) {
    if ($this->validate($array)) {
      $this->save(); // Save finance_charge record.
      
      // Remove the charge from anyone who does not have a payment.
      foreach ($this->finance_charge_members as $member) {
        if ( ! $member->payments->sum('amount')) {
          $member->delete();
        }
      }
      
      // Assign the updated charge values to the checked members.
      $this->assign_members($array->as_array());
      return TRUE;
    }
    return FALSE;
  }
  

  /**
   * Count the number of charges the site has.
   */
  public function count_by_site($site_id = NULL) {
    $site_id = is_null($site_id) ? kohana::config('chapterboard.site_id') : $site_id;
    $result = $this->db->select('COUNT(*) as total')->from('finance_charges')->where('site_id', $site_id)->get()->current();
    return $result->total;
  }
    
  /**
   * ORM->delete() Override
   *
   * Perform checking to make sure that payments for the charge
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
    if ($this->finance_payments->count()) {
      return FALSE;
    }

		// Delete this object
		$this->db->where($this->primary_key, $id)->delete($this->table_name);
		
		// Delete all the charges in the finance_charge_members table.
		$this->db->where('finance_charge_id', $id)->delete('finance_charge_members');

    $this->clear(); // Empty the object
		return TRUE;
  }
  
  /**
   * Acl_Resource_Interface method.
   */
  public function get_resource_id() {
    return 'finance_charge';
  }
}