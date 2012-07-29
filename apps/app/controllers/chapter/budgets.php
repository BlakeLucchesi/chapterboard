<?php defined('SYSPATH') or die('No direct script access.');

class Budgets_Controller extends Private_Controller {
  
  public $secondary = 'menu/budgets';
  
  public function _pre_controller() {
    Router::$routed_uri = 'budgets';
    if ( ! A2::instance()->allowed('budget', 'manage'))
      Event::run('system.403');
  }
  
  /**
   * Budgets homepage.
   */
  public function index() {
    $this->title = 'Chapter Budgets';
    $this->budgets = ORM::factory('budget')->find_all();
  }
  
  /**
   * View and edit the details of a budget.
   */
  public function show($id) {
    $this->budget = ORM::factory('budget', $id);
    $this->title = $this->budget->name;
    javascript::add('scripts/budget.js');
    
    if ( ! $this->budget->loaded)
      Event::run('system.404');
    if ( ! A2::instance()->allowed($this->budget, 'edit'))
      Event::run('system.403');
    
    if ($post = $this->input->post()) {
      // Update budget meta data.
      $this->budget->starting_balance = text::number($this->input->post('starting_balance'));
      $this->budget->uncharged_dues = text::number($this->input->post('uncharged_dues'));
      $this->budget->expected_fees = text::number($this->input->post('expected_fees'));
      $this->budget->save();
      
      ORM::factory('budget_expected')->update_expected($this->budget->id, $post);
      message::add(TRUE, 'Changes saved.');
    }
    $this->form['starting_balance'] = $this->budget->starting_balance;
    $this->form['uncharged_dues'] = $this->budget->uncharged_dues;
    $this->form['expected_fees'] = $this->budget->expected_fees;
    $this->expected = $this->budget->expected();
    $this->actual = $this->budget->actual();
  }
  
  /**
   * Add new budget.
   */
  public function create() {
    $this->title = 'Create Chapter Budget';
    
    if ($post = $this->input->post()) {
      $budget = ORM::factory('budget');
      if ($budget->validate($post, TRUE)) {
        message::add(TRUE, 'Budget created successfully.');
        url::redirect('budgets/'. $budget->id);
      }
      else {
        message::add(FALSE, 'Please fix the errors below.');
        $this->form = $post->as_array();
        $this->errors = $post->errors('form_budget_create');
      }
    }
  }
  
  /**
   * Edit the budget name.
   */
  public function edit($id) {
    $this->budget = ORM::factory('budget', $id);
    if ( ! $this->budget->loaded || $this->budget->site_id != $this->site->id)
      Event::run('system.403');

    $this->view = 'budgets/create';
    $this->title = sprintf('Editing Budget: %s', $this->budget->name);
    $this->form['name'] = $this->budget->name;
    
    if ($post = $this->input->post()) {
      if ($this->budget->validate($post, TRUE)) {
        message::add(TRUE, 'Budget details updated successfully.');
        url::redirect('budgets/'. $this->budget->id);
      }
      else {
        $this->form = $post->as_array();
        $this->errors = $post->errors('form_budget_create');
        message::add(FALSE, 'Please fix the errors below.');
      }
    }
  }
  
  /**
   * Delete a budget that has not been used.
   */
  public function delete($id) {
    $this->budget = ORM::factory('budget', $id);
    if ( ! $this->budget->loaded || $this->budget->site_id != $this->site->id)
      Event::run('system.403');

    // If there are no transactions or charges for the budget we delete it and redirect.
    if ($this->budget->budget_transactions->count() == 0 AND $this->budget->finance_charges->count() == 0) {
      message::add(TRUE, 'The %s budget has been successfully deleted.', $this->budget->name);
      $this->budget->delete();
      url::redirect('budgets');
    }
    $budgets = ORM::factory('budget')->where('site_id', $this->site->id)->find_all();
    if ($budgets->count() == 1) {
      message::add(FALSE, 'You cannot delete a chapter budget with transactions or member charges unless you can reassign them to a different budget. <br />Please create a new budget and try again.');
      url::redirect('budgets');
    }

    $this->title = 'Confirm Deletion';
    // Move transactions and charges to a different budget.
    if ($post = $this->input->post()) {
      $budget_name = $this->budget->name;
      if ($this->budget->reassign_to_budget($post['budget_id'])) {
        $this->new_budget = ORM::factory('budget', $post['budget_id']);
        message::add(TRUE, 'Transactions and member charges have been reassigned from %s to %s.', $budget_name, $this->new_budget->name);
        url::redirect('budgets');
      }
      else {
        message::add(FALSE, 'Could not reassign budget. Please select a budget and try again.');
      }
    }
  }
  
  /**
   * Itemized spending report.
   *
   * @param int Budget Id.
   * @param int Category Id.
   */
  public function itemized($budget_id, $category_id) {
    $this->budget = ORM::factory('budget', $budget_id);
    $this->category = ORM::factory('budget_category', $category_id);
    $this->title = sprintf('Itemized Report for %s in %s', $this->category->name, $this->budget->name);
    $this->transactions = ORM::factory('budget_transaction')->itemized($budget_id, $category_id);
  }
  
}