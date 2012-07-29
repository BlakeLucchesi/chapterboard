<?php defined('SYSPATH') or die('No direct script access.');

class Budget_Model extends ORM implements Acl_Resource_Interface {
  
  protected $has_many = array('budget_transactions', 'finance_charges', 'budget_expected');
  
  protected $belongs_to = array('site');
  
  protected $sorting = array('id' => 'DESC');
  
  public function __get($column) {
    switch ($column) {
      case 'expense_categories':
        return ORM::factory('budget_category')->where('site_id', $this->site_id)->where('type', 'expense')->find_all();
      case 'income_categories':
        return ORM::factory('budget_category')->where('site_id', $this->site_id)->where('type', 'income')->find_all();
    }
    return parent::__get($column);
  }
  
  
  /**
   * Return a keyed array of expected amounts for a budget's categories.
   */
  public function expected() {
    $items = array();
    $results = $this->db->query('SELECT budget_category_id, amount FROM budget_expected WHERE budget_id = ?', array($this->id));
    foreach ($results as $result) {
      $items[$result->budget_category_id] = $result->amount;
    }
    return $items;
  }
  
  /**
   * Return a keyed array of actual amounts spent on a budget's categories.
   */
  public function actual() {
    $items = array();
    $results = $this->db->query("SELECT budget_category_id, SUM(amount) total FROM budget_transactions WHERE budget_id = ? GROUP BY budget_category_id", array($this->id));
    foreach ($results as $result) {
      $items[$result->budget_category_id] = $result->total;
    }
    return $items;
  }
    
  /**
   * Calculate the total ChapterBoard fees based on collections
   * from a given budget.
   */
  public function collection_fees() {
    static $total;
    if (is_null($total)) {
      $total = 0;
      if ($this->finance_charges->count()) {
        foreach ($this->finance_charges as $charge) {
          $total += $charge->fees;
        }
      }
    }
    return $total;
  }
    
  /**
   * Validation.
   */
  public function validate(array &$array, $save = FALSE) {
    $array = Validation::factory($array)
    ->pre_filter('trim')
    ->add_rules('name', 'required', 'standard_text');
    return parent::validate($array, $save);
  }
  
  /**
   * Before save filter.
   */
  public function before_insert() {
    $this->site_id = kohana::config('chapterboard.site_id');
  }
  
  /**
   * Reassign budget transactions and charges to a new budget.
   */
  public function reassign_to_budget($budget_id) {
    $new_budget = ORM::factory('budget')->where('site_id', kohana::config('chapterboard.site_id'))->where('id', $budget_id)->find();
    if ($new_budget->loaded) {
      $this->db->query("UPDATE finance_charges SET budget_id = ? WHERE budget_id = ?", array($new_budget->id, $this->id));
      $this->db->query("UPDATE budget_transactions SET budget_id = ? WHERE budget_id = ?", array($new_budget->id, $this->id));
      $this->delete();
      return TRUE;
    }
    return FALSE;
  }
  
  /**
   * Override find_all() to filter out by site_id.
   */
  public function find_all($limit = NULL, $offset = NULL) {
    $this->where('site_id', kohana::config('chapterboard.site_id'));
    return parent::find_all($limit, $offset);
  }
  
  /**
   * Select options.
   */
  public function options($blank_option = TRUE, $remove_id = NULL) {
    $budgets = ORM::factory('budget')->orderby('id', 'DESC')->find_all();
    if ($blank_option) {
      $results['0'] = '-- ';
    }
    foreach ($budgets as $budget) {
      if ($remove_id != $budget->id) {
        $results[$budget->id] = $budget->name;
      }
    }
    return $results;
  }
  
  /**
   * ACL
   */
  public function get_resource_id() {
    return 'budget';
  }
}