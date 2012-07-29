<?php defined('SYSPATH') or die('No direct script access.');

class Members_Controller extends Finances_Controller {
  
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
   * Show a summary of an individual member.
   */
  function show($id = NULL) {
    $this->member = is_numeric($id) ? ORM::factory('user', $id) : $this->user;

    if ( ! $this->member->loaded)
      Event::run('system.404');
    if ( ! A2::instance()->allowed($this->member, 'finances'))
      Event::run('system.403');

    // If viewing another member's account, set the "member balances" nav item as active.
    if ($id != $this->user->id) {
      Router::$routed_uri = 'finances/members';
    }

    $this->title = sprintf("%s's Outstanding Charges", $this->member->name());
    $this->unpaid_charges = ORM::factory('finance_charge_member')->charges($this->member->id, FALSE);
    $this->payments = ORM::factory('finance_payment')->where('user_id', $this->member->id)->find_all();
    
    if (request::is_ajax()) {
      $response = array(
        'name' => $this->member->name(),
        'charges' => array(),
        'payments' => array(),
        'balance' => 0,
      );
      foreach ($this->unpaid_charges as $charge) {
        $response['charges'][] = array(
          'id' => $charge->id,
          'title' => $charge->title,
          'total_formatted' => money::display($charge->amount),
          'due_formatted' => money::display($charge->amount - $charge->payments->sum('amount')),
          'due_amount' => number_format($charge->amount - $charge->payments->sum('amount'), 2, '.', ''),
          'due_date' => date::display($charge->due, 'M d, Y', FALSE),
        );
        $response['balance'] += $charge->amount - $charge->payments->sum('amount');
      }
      $response['balance'] = money::display($response['balance']);
      
      foreach ($this->payments as $payment) {
        $response['payments'][] = array(
          'id' => $payment->id,
          'charge_title' => $payment->finance_charge->title,
          'created' => date::display($payment->created, 'M d, Y'),
          'received' => date::display($payment->received, 'M d, Y', FALSE),
          'amount' => money::display($payment->amount),
          'note' => $payment->note,
        );
      }
      response::json(TRUE, null, $response);
    }
  }
  
  /**
   * Member History.
   */
  public function history($id) {
    $this->member = ORM::factory('user', $id);

    if ( ! $this->member->loaded)
      Event::run('system.404');
    if ( ! A2::instance()->allowed($this->member, 'finances'))
      Event::run('system.403');
    
    $this->title = sprintf("%s's Payment History", $this->member->name());
    $this->payments = ORM::factory('finance_payment')->where('user_id', $this->member->id)->find_all();
  }
  
  /**
   * Record member's payment.
   */
  public function payment($user_id) {
    $this->member = ORM::factory('user', $user_id);
    
    if ( ! $this->member->loaded)
      Event::run('system.404');
    if ( ! A2::instance()->allowed('finance', 'manage'))
      Event::run('system.403');
    if ( ! A2::instance()->allowed($this->member, 'finances'))
      Event::run('system.403');
      
    $this->title = sprintf('Record Payment for %s', $this->member->name());
    $this->charges = ORM::factory('finance_charge_member')->unpaid($this->member->id);
    
    if ($post = $this->input->post()) {
      
      if ($post['check_no'] && ! is_numeric($post['check_no'])) {
        message::add(FALSE, 'Check number must be an integer.');
        $this->form = $post;
        return;
      }

      // Validate charge values.
      $amount = 0;
      foreach ($this->charges as $charge) {
        $amount += $post['amount'][$charge->id];
        if ($post['amount'][$charge->id] > $charge->balance()) {
          message::add(FALSE, Kohana::lang('form_finance_payment.amount.overpaid'));
          return;
        }
      }
      
      if ( ! $amount) {
        message::add(FALSE, 'Please enter an amount for this payment.');
        return;
      }

      // Record payments.
      foreach ($this->charges as $charge) {
        if ($post['amount'][$charge->id] > 0) {
          $payment = ORM::factory('finance_payment');
          $data = array(
            'finance_charge_id' => $charge->finance_charge_id,
            'user_id' => $this->member->id,
            'amount' => $post['amount'][$charge->id],
            'received' => $post['received'],
            'note' => $post['note'],
            'type' => $post['type'],
            'check_no' => $post['check_no'],
          );
          $payment->validate($data, TRUE);
        }
      }
      message::add(TRUE, 'Payment recorded.');
      url::redirect('finances/members/'. $this->member->id);
    }
  }

  public function export() {
    if ( ! A2::instance()->allowed('finance', 'manage'))
      Event::run('system.403');

    $rows[] = array('Member', 'Phone', 'Email', 'Type', 'Account Balance', 'Amount Past Due', 'Notes');

    $this->members = ORM::factory('finance_charge_member')->balances();
    foreach ($this->members as $member) {
      $rows[] = array($member->name, $member->phone, $member->email, $member->type, $member->balance, $member->overdue_amount);
    }
    response::csv($rows, 'chapterboard-member-balances');
  }

}