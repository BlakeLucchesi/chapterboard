<?php defined('SYSPATH') or die('No direct script access.');

class Budget_category_Model extends ORM {
  
  protected $belongs_to = array('budget');
  
  protected $has_many = array('budget_transactions');
  
  protected $table_name = 'budget_categories';
  
  protected $sorting = array('name' => 'ASC');
  
  /**
   * Validation.
   */
  public function validate(array &$array, $save = FALSE) {
    $array = Validation::factory($array)
    ->pre_filter('trim')
    ->add_rules('name', 'required', 'standard_text')
    ->add_rules('type', 'required', 'length[6,7]');
    return parent::validate($array, $save);
  }
  
  /**
   * Before insert hook.
   */
  public function before_insert() {
    $this->site_id = kohana::config('chapterboard.site_id');
    $this->created = date::to_db();
  }
  
  /**
   * Reassign transactions to a different category before deleting.
   */
  public function reassign_transactions($category_id) {
    $new_category = ORM::factory('budget_category')->where('site_id', kohana::config('chapterboard.site_id'))->where('id', $category_id)->find();
    if ($new_category->loaded) {
      $this->db->query("UPDATE budget_transactions SET budget_category_id = ? WHERE budget_category_id = ?", array($new_category->id, $this->id));
      
      // Update all the expected amounts by combining values.
      $old_values = $this->db->query("SELECT * FROM budget_expected WHERE budget_category_id = ?", array($this->id));
      foreach ($old_values as $row) {
        $this->db->query("UPDATE budget_expected SET amount = amount + ? WHERE budget_id = ? AND budget_category_id = ?", array($row->amount, $row->budget_id, $category_id));
      }
      $this->db->query("DELETE FROM budget_expected WHERE budget_category_id = ?", array($this->id));
      $this->delete();
      return TRUE;
    }
    return FALSE;
  }
  
  /**
   * Override find_all to filter by site_id.
   */
  public function find_all($limit = NULL, $offset = NULL) {
    $this->where('site_id', kohana::config('chapterboard.site_id'));
    return parent::find_all($limit, $offset);
  }
  
  /**
   * Override select_list().
   */
	public function select_list($key = NULL, $val = NULL) {
		$items = array();
		$results = $this->select('name', 'type', 'id')->find_all();
		foreach ($results as $result) {
		  $items[$result->id] = sprintf('%s (%s)', $result->name, $result->type);
		}
		return $items;
	}
	
	public function options($id = NULL) {
	  $options = $this->select_list();
	  if ($id) {
	    unset($options[$id]);
	  }
	  return $options;
	}
}