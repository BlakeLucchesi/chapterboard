<?php defined('SYSPATH') or die('No direct script access.');

class Dues_Controller extends Private_Controller {

  protected $secondary = 'menu/dashboard';
  
  /**
   * Show a member's financial summary.
   */
  function index() {
    $this->member = $this->user;

    $this->title = 'Outstanding Charges';
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
}