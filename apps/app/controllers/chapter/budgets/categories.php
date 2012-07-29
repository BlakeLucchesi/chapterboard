<?php defined('SYSPATH') or die('No direct script access.');

class Categories_Controller extends Budgets_Controller {
  
  public function _pre_controller() {
    Router::$routed_uri = 'budgets/categories';
    if ( ! A2::instance()->allowed('budget', 'manage'))
      Event::run('system.403');
  }
  
  /**
   * List all of the available categories.
   */
  public function index() {
    $this->title = 'Budget Categories';

    if ($post = $this->input->post()) {
      $category = ORM::factory('budget_category');
      if ($category->validate($post, TRUE)) {
        message::add(TRUE, 'Category created successfully.');
      }
      else {
        $this->form = $post->as_array();
        $this->errors = $post->errors('form_budget_category');
        message::add(FALSE, 'Please fix any errors below.');
      }
    }

    $this->income_categories = ORM::factory('budget_category')->where('type', 'income')->find_all();
    $this->expense_categories = ORM::factory('budget_category')->where('type', 'expense')->find_all();
  }
  
  /**
   * Edit a budget category.
   */
  public function edit($id) {
    $this->category = ORM::factory('budget_category', $id);
    $this->title = sprintf('Editing Category: %s', $this->category->name);
    $this->form = $this->category->as_array();
    
    if ($post = $this->input->post()) {
      if ($this->category->validate($post, TRUE)) {
        message::add(TRUE, 'Category updated successfully.');
        url::redirect('budgets/categories');
      }
      else {
        $this->form = $post->as_array();
        $this->errors = $post->errors('form_budget_category');
        message::add(FALSE, 'Please fix any errors below.');
      }
    }
  }
  
  /**
   * Delete a budget category.
   */
  public function delete($id = NULL) {
    $this->category = ORM::factory('budget_category', $id);
    if ( ! $this->category->loaded || $this->category->site_id != $this->site->id)
      Event::run('system.403');

    // If there are no transactions or charges for the budget we delete it and redirect.
    if ($this->category->budget_transactions->count() == 0) {
      message::add(TRUE, 'The %s budget category has been successfully deleted.', $this->category->name);
      $this->category->delete();
      url::redirect('budgets/categories');
    }
    $categories = ORM::factory('budget_category')->where('site_id', $this->site->id)->find_all();
    if ($categories->count() == 1) {
      message::add(FALSE, 'You cannot delete a budget category with transactions unless you can reassign them to a different category. <br />Please create a new budget category and try again.');
      url::redirect('budgets/categories');
    }

    $this->title = 'Confirm Deletion';
    // Move transactions and charges to a different budget.
    if ($post = $this->input->post()) {
      $category_name = $this->category->name;
      if ($this->category->reassign_transactions($post['category_id'])) {
        $this->new_category = ORM::factory('budget_category', $post['category_id']);
        message::add(TRUE, 'Transactions have been reassigned from %s to %s.', $category_name, $this->new_category->name);
        url::redirect('budgets/categories');
      }
      else {
        message::add(FALSE, 'Could not reassign transactions to budget category. Please select a different budget category and try again.');
      }
    }
  }
}