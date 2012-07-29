<?php defined('SYSPATH') or die('No direct script access.');

class Deposits_Controller extends Finances_Controller {
  
  /**
   * Show a list of all deposits.
   */
  public function index() {
    if ( ! A2::instance()->allowed('finance', 'manage'))
      Event::run('system.403');

    $this->title = 'Chapter Deposits';
    
    $this->pagination = new Pagination(array('total_items' => ORM::factory('deposit')->count()));
    $limit = $this->pagination->items_per_page;
    $offset = $this->pagination->sql_offset();

    $this->deposits = ORM::factory('deposit')->find_all($limit, $offset);
    $this->pending = ORM::factory('deposit_transaction')->find_pending_deposits($this->site->id);
  }
  
  /**
   * Show the details of a deposit.
   */
  public function show($id) {
    $this->deposit = ORM::factory('deposit', $id);
    
    if ( ! $this->deposit->loaded)
      Event::run('system.404');
    if ( ! (A2::instance()->allowed('finance', 'manage') && $this->deposit->site_id == $this->site->id))
      Event::run('system.403');
    
    Router::$routed_uri = 'finances/deposits';
    $this->title = sprintf('Deposit from %s', date::display($this->deposit->created, 'M d, Y', FALSE));
    $this->transactions = $this->deposit->deposit_transactions;
  }
  
}