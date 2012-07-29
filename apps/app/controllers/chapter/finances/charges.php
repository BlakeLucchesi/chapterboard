<?php defined('SYSPATH') or die('No direct script access.');

class Charges_Controller extends Finances_Controller {
  
  /**
   * View a list of all recent charges with details.
   */
  public function index() {
    $this->title = 'Charges';

    if ( ! A2::instance()->allowed('finance', 'manage'))
      Event::run('system.403');
    
    $this->pagination = new Pagination(array('total_items' => ORM::factory('finance_charge')->count_by_site($this->site->id)));
    $limit = $this->pagination->items_per_page;
    $offset = $this->pagination->sql_offset();
    
    $this->charges = ORM::factory('finance_charge')->find_all_by_site($this->site->id, $limit, $offset);
    
    if (request::is_ajax()) {
      $response['charges'] = array();
      foreach ($this->charges as $charge) {
        $total = $charge->finance_charge_members->sum('amount');
        $collected = $charge->finance_payments->sum('amount');
        $outstanding = $total - $collected;
        
        $response['charges'][] = array(
          'id' => $charge->id,
          'title' => $charge->title,
          'member_count' => number_format($charge->finance_charge_members->count()),
          'due_date' => date::display($charge->due, 'M d, Y', FALSE),
          'total_charged' => money::display($total),
          'total_collected' => money::display($collected),
          'total_outstanding' => money::display($outstanding),
        );
      }
      response::json(TRUE, null, $response);
    }
  }
  
  /**
   * Show the details for a charge.
   */
  public function show($id) {
    $this->charge = ORM::factory('finance_charge', $id);
    $this->details = $this->charge->details(); // Load our details data array.
    
    if ( ! $this->charge->loaded)
      Event::run('system.404');
    if ( ! A2::instance()->allowed('finance', 'manage'))
      Event::run('system.403');
    if ($this->charge->site_id != $this->site->id)
      Event::run('system.403');

    Router::$routed_uri = 'finances/charges';
    $this->title = sprintf('%s due %s', $this->charge->title, date::display($this->charge->due, 'M d, Y', FALSE));
    
    if (request::is_ajax()) {
      $response = array(
        'title' => $this->charge->title,
        'members' => array(),
      );
      foreach ($this->details['members'] as $id => $member) {
        $response['members'][] = array(
          'id' => $id,
          'name' => $member->name,
          'balance' => money::display($member->outstanding)
        );
      }
      response::json(TRUE, null, $response);
    }
  }
  
  /**
   * Send an email reminder.
   */
  public function reminder($id) {
    $this->charge = ORM::factory('finance_charge', $id);
    
    if ( ! $this->charge->loaded)
      Event::run('system.404');
    if ( ! A2::instance()->allowed('finance', 'manage'))
      Event::run('system.403');
    if ($this->charge->site_id != $this->site->id)
      Event::run('system.403');
    
    $this->title = sprintf('Send Email Reminder for "%s" due on %s', $this->charge->title, date::display($this->charge->due, 'M d, Y'));
    
    if ($post = $this->input->post()) {
      if ($this->charge->notify_members()) {
        message::add(TRUE, 'Balance reminders sent successfully.');
        url::redirect('finances/charges/'. $this->charge->id);
      }
      else {
        message::add(FALSE, 'There was an error sending outstanding balance reminders. Please try again.');
        $this->form = $post;
      }
    }
  }
  
  /**
   * Add a charge
   */
  public function add() {
    if ( ! A2::instance()->allowed('finance', 'manage'))
      Event::run('system.403');
    
    $this->view = 'finances/charges/form';
    $this->title = 'Add New Charge';
    $this->actives = ORM::factory('user')->search_profile(NULL, 'active');
    $this->pledges = ORM::factory('user')->search_profile(NULL, 'pledge');
    $this->alumni = ORM::factory('user')->search_profile(NULL, 'alumni');
    $this->budgets = ORM::factory('budget')->options();
    $this->deposit_accounts = ORM::factory('deposit_account')->find_all_active()->select_list();

    if ($post = $this->input->post()) {
      $this->charge = ORM::factory('finance_charge');
      if ($this->charge->validate($post, TRUE)) {
        $this->charge->assign_members($post);
        $this->charge->notify_members();
        message::add(TRUE, 'Charge added successfully.');
        url::redirect('finances/charges/'. $this->charge->id);
      }
      else {
        message::add(FALSE, 'Please fix the errors below and try again.');
        $this->form = $post->as_array();
        $this->errors = $post->errors('form_finance_charge');
      }
    }
  }
  
  /**
   * Edit
   */
  public function edit($type, $charge_id) {
    if ( ! A2::instance()->allowed('finance', 'manage'))
      Event::run('system.403');
    
    // Whether we are editing the parent finance_charge, or a member's charge.
    if ($type == 'charge') {
      $this->view = 'finances/charges/form';
      $this->charge = ORM::factory('finance_charge', $charge_id);
      $this->title = sprintf('Editing Charge: %s', $this->charge->title);
      $this->budgets = ORM::factory('budget')->options();
      $this->deposit_accounts = ORM::factory('deposit_account')->find_all_active()->select_list();
      $this->form = $this->charge->as_array();
      
      // Check permissions.
      if ( ! $this->charge->loaded)
        Event::run('system.404');
      if ($this->charge->site_id != $this->site->id)
         Event::run('system.403');
            
      // Format form output when data is coming from database.
      $this->form['due'] = date::display($this->charge->due, 'm/d/Y', FALSE);
      $this->form['amount'] = number_format($this->charge->amount, 2);
      
      // Gather list of members whom we can assess a charge.
      $this->actives = ORM::factory('user')->search_profile(NULL, 'active');
      $this->pledges = ORM::factory('user')->search_profile(NULL, 'pledge');
      $this->alumni = ORM::factory('user')->search_profile(NULL, 'alumni');
      
      // Setup which members have been charged and which have already made payments.
      foreach ($this->charge->finance_charge_members as $member) {
        if ($member->payments->sum('amount')) {
          $this->members_with_payments[$member->user_id] = 1;
        }
        $this->form['members'][$member->user_id] = 1;
      }

      if ($post = $this->input->post()) {
        if ($this->charge->update($post)) {
          message::add(TRUE, 'Changes to %s saved.', $this->charge->title);
          url::redirect('finances/charges/'. $this->charge->id);
        }
        else {
          message::add(FALSE, 'Please fix the errors below and try again.');
          $this->form = $post->as_array();
          $this->errors = $post->errors('form_finance_charge');
        }
      }
    }
    else if ($type == 'member') {
      $this->view = 'finances/charges/edit_member';
      $this->charge_member = ORM::factory('finance_charge_member', $charge_id);
      $this->charge = $this->charge_member->finance_charge;
      
      if ( ! $this->charge->loaded)
        Event::run('system.404');
      if ($this->charge->site_id != $this->site->id)
        Event::run('system.403');
        
      $this->title = sprintf('Editing Charge: %s for %s', $this->charge->title, $this->charge_member->user->name());

      // Setup default form data
      $this->form['amount'] = $this->charge_member->amount;

      if ($post = $this->input->post()) {
        // Make sure the new charge value is greater than any amounts paid on that charge.
        if (is_numeric($post['amount']) && $post['amount'] > 0 && $post['amount'] >= $this->charge_member->payments->sum('amount')) {
          $this->charge_member->amount = $post['amount'];
          $this->charge_member->save();
          message::add(TRUE, 'Changed charge: %s for %s to %s', $this->charge->title, $this->charge_member->user->name(), money::display($post['amount']));
          if ($_POST['redirect']) {
            url::redirect('finances/members/'. $this->charge_member->user_id);
          }
          url::redirect('finances/charges/'. $this->charge->id);
        }
        else {
          message::add(FALSE, 'Please enter an amount greater than or equal to what the member has already paid.');
          $this->form = $post;
        }
      }
    }
  }
  
  /**
   * Delete
   */
  public function delete($type, $charge_id) {
    // Delete a specific charge for all members.
    if ($type == 'charge') {
      $this->charge = ORM::factory('finance_charge')->find($charge_id);
      
      if ( ! $this->charge->loaded)
        Event::run('system.404');
      if ( ! A2::instance()->allowed($this->charge, 'delete'))
        Event::run('system.403');

      if ($this->charge->delete()) {
        message::add(TRUE, 'Charge removed successfully');
        url::redirect('finances/charges');
      }
      else {
        message::add(FALSE, 'You cannot delete a charge once a payment has been made for that charge.');
        url::redirect('finances/charges/'. $this->charge->id);
      }

    }
    // Delete a specific charge from a specific member. 
    else if ($type == 'member') {
      $this->charge_member = ORM::factory('finance_charge_member')->find($charge_id);
      $this->charge = $this->charge_member->finance_charge;
      $this->member = $this->charge_member->user;
      
      if ( ! $this->charge_member->loaded)
        Event::run('system.404');
      if ( ! A2::instance()->allowed($this->charge_member->finance_charge, 'delete'))
        Event::run('system.403');
      
      if ($this->charge_member->delete()) {
        message::add(TRUE, '%s charge removed from %s\'s account.', $this->charge->title, $this->member->name());
      }
      else {
        message::add(FALSE, '%s has already made a payment on this charge, you cannot delete the charge unless it has no payments.', $this->member->name());
      }
      if ($_GET['redirect']) {
        url::redirect('finances/members/'. $this->member->id);
      }
      url::redirect('finances/charges/'. $this->charge->id);
    }
  }
}