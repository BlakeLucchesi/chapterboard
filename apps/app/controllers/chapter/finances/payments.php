<?php defined('SYSPATH') or die('No direct script access.');

class Payments_Controller extends Finances_Controller {
  
  /**
   * Show all the payments the chapter has received from its members.
   */
  public function index() {
    $this->title = 'Member Payments';
    if ( ! A2::instance()->allowed('finance', 'manage'))
      Event::run('system.403');
    
    $this->pagination = new Pagination(array('items_per_page' => 50, 'total_items' => ORM::factory('finance_payment')->payments_count()));
    $limit = $this->pagination->items_per_page;
    $offset = $this->pagination->sql_offset();
    
    // Load with other tables since we know we'll be joining them anyways after the fact.
    $this->payments = ORM::factory('finance_payment')->with('finance_charge')->with('user')->find_all($limit, $offset);
  }
  
  public function edit($id) {
    $this->payment = ORM::factory('finance_payment', $id);
    
    if ( ! $this->payment->loaded)
      Event::run('system.404');
    if ( ! A2::instance()->allowed('finance', 'manage'))
      Event::run('system.403');
    if ( ! A2::instance()->allowed($this->payment->user, 'finances'))
      Event::run('system.403');
    if ($this->payment->type == 'credit') {
      message::add(FALSE, 'You cannot modify payments that were made online.');
      url::redirect('finances/payments');
    }
    
    $this->member = $this->payment->user;
    $this->charge_member = ORM::factory('finance_charge_member')->find_by_user_charge($this->member->id, $this->payment->finance_charge_id);
    $this->title = sprintf('Editing Payment from %s', $this->member->name());
    $this->form = $this->payment->as_array();
    
    if ($post = $this->input->post()) {
      
      if ($post['check_no'] && ! is_numeric($post['check_no'])) {
        message::add(FALSE, 'Check number must be an integer.');
        $this->form = $post;
        return;
      }

      // Validate charge values.
      $data = array(
        'finance_charge_id' => $this->payment->finance_charge_id,
        'user_id' => $this->payment->user_id,
        'amount' => $post['amount'],
        'received' => $post['received'],
        'note' => $post['note'],
        'type' => $post['type'],
        'check_no' => $post['check_no'],
      );
      if ($this->payment->validate($data, TRUE)) {
        message::add(TRUE, 'Payment updated succesfully.');
        url::redirect('finances/members/'. $this->payment->user_id);
      }
      else {
        $this->form = $data->as_array();
        $this->errors = $data->errors('form_finance_payment');
        foreach ($this->errors as $key => $error) {
          message::add(FALSE, $error);
        }
      }
    }
  }
  
  public function delete($id) {
    $this->payment = ORM::factory('finance_payment', $id);
    
    if ( ! $this->payment->loaded)
      Event::run('system.404');
    if ( ! A2::instance()->allowed('finance', 'manage'))
      Event::run('system.403');
    if ( ! A2::instance()->allowed($this->payment->user, 'finances'))
      Event::run('system.403');
    if ($this->payment->type == 'credit') {
      message::add(FALSE, 'You cannot modify payments that were made online.');
      url::redirect('finances/payments');
    }
    
    $user_id = $this->payment->user_id;
    message::add(TRUE, 'Payment from %s for %s deleted successfully.', $this->payment->user->name(), money::display($this->payment->amount));
    $this->payment->delete();
    url::redirect('finances/members/'. $user_id);
  }
}