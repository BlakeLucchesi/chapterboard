<?php defined('SYSPATH') or die('No direct script access.');

class Transactions_Controller extends Budgets_Controller {
  
  public function _pre_controller() {
    Router::$routed_uri = 'budgets/transactions';
    if ( ! A2::instance()->allowed('budget', 'manage'))
      Event::run('system.403');
  }
  
  /**
   * List all recent transactions for a site.
   */
  public function index() {
    $this->title = 'Transactions';
    $this->pagination = new Pagination(array('items_per_page' => 40, 'total_items' => ORM::factory('budget_transaction')->count_transactions()));
    $limit = $this->pagination->items_per_page;
    $offset = $this->pagination->sql_offset();

    if ($post = $this->input->post()) {
      $transaction = ORM::factory('budget_transaction');
      if ($transaction->validate($post, TRUE)) {
        
        message::add(TRUE, 'Transaction recorded successfully.');
      }
      else {
        $this->form = $post->as_array();
        $this->errors = $post->errors('form_budget_transaction');
        message::add(FALSE, 'Please fix the errors below.');
      }
    }

    $this->budgets = ORM::factory('budget')->select_list();
    $this->categories = ORM::factory('budget_category')->select_list();
    $this->transactions = ORM::factory('budget_transaction')->find_all($limit, $offset);
  }
  
  /**
   * Show a list of transactions for a budget.
   *
   * @param int Budget ID.
   */
  public function budget($id) {
    $this->budget = ORM::factory('budget', $id);

    if ( ! $this->budget->loaded)
      Event::run('system.404');
    if ($this->budget->site_id != $this->site->id)
      Event::run('system.403');
      
    $this->title = sprintf('Transactions in %s', $this->budget->name);
    $this->transactions = $this->budget->budget_transactions;
  }
  
  /**
   * Alias of budget(), but shows /budgets as the primary nav.
   */
  public function show($id) {
    Router::$routed_uri = 'budgets';
    $this->budget($id);
    $this->view = 'budgets/transactions/show';
  }
  
  /**
   * Show a list of transactions for a category.
   *
   * @param int Category ID.
   */
  public function category($id) {
    $this->category = ORM::factory('budget_category', $id);
    
    if ( ! $this->category->loaded)
      Event::run('system.404');
    if ($this->category->site_id != $this->site->id)
      Event::run('system.403');
    
    $this->title = sprintf('Transactions in %s', $this->category->name);
    $this->transactions = $this->category->budget_transactions;
  }
  
  /**
   * Edit a transaction record.
   *
   * @param int Transaction ID.
   */
  public function edit($id) {
    $this->title = 'Edit Transaction Details';
    $this->transaction = ORM::factory('budget_transaction', $id);

    if ( ! $this->transaction->loaded)
      Event::run('system.404');
    if ($this->transaction->budget->site_id != $this->site->id)
      Event::run('system.403');
      
    $this->form = $this->transaction->as_array();
    $this->budgets = ORM::factory('budget')->select_list();
    $this->categories = ORM::factory('budget_category')->select_list();

    if ($post = $this->input->post()) {
      if ($this->transaction->validate($post, TRUE)) {
        message::add(TRUE, 'Transaction recorded successfully.');
        url::redirect('budgets/transactions');
      }
      else {
        $this->form = $post->as_array();
        $this->errors = $post->errors('form_budget_transaction');
        message::add(FALSE, 'Please fix the errors below.');
      }
    }
  }
  
  /**
   * Delete a transaction.
   */
  public function delete($id) {
    $this->transaction = ORM::factory('budget_transaction', $id);
    if ( ! $this->transaction->loaded)
      Event::run('system.403');
    if ($this->transaction->budget->site_id != $this->site->id)
      Event::run('system.403');
    
    message::add(TRUE, 'Transaction %s deleted successfully.', $this->transaction->description);
    $this->transaction->delete();
    url::redirect('budgets/transactions');
  }
}