<?php defined('SYSPATH') or die('No direct script access.');

class Budget_expected_Model extends ORM {
  
  protected $table_name = 'budget_expected';
  
  protected $primary_key = 'budget_category_id';
  
  protected $primary_val = 'amount';
  
  /**
   * Update the expected budget amounts.
   */
  public function update_expected($budget_id, $post) {
    $budget = ORM::factory('budget', $budget_id);
    if ($budget->loaded) {
      $this->db->query("DELETE FROM budget_expected WHERE budget_id = ?", array($budget_id));
      foreach ($post['category'] as $id => $value) {
        if ($value > 0) {
          $value = preg_replace('/[^0-9\.]/i', '', $value); // strip any non numeric or period characters.
          $this->db->query("INSERT INTO budget_expected (budget_id, budget_category_id, amount) VALUES (?, ?, ?)", array($budget_id, $id, $value));
        }
      }
      return TRUE;
    }
    return false;
  }
}