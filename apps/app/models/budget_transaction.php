<?php defined('SYSPATH') or die('No direct script access.');

class Budget_transaction_Model extends ORM {
  
  protected $belongs_to = array('budget', 'budget_category', 'user');

  protected $sorting = array('date' => 'DESC');
  
  public function count_transactions() {
    return $this->with('budget')->select('COUNT(*) as count')->where('budget.site_id', kohana::config('chapterboard.site_id'))->find()->count;
  }
  
  /**
   * Override default find_all().
   */
  public function find_all($limit = NULL, $offset = NULL) {
    $this->with('category')->with('budget')->where('budget.site_id', kohana::config('chapterboard.site_id'));
    return parent::find_all($limit, $offset);
  }
  
  /**
   * Itemized list of transactions for a budget category.
   *
   * @param int Budget Id.
   * @param int Category Id.
   */
  public function itemized($budget_id, $category_id) {
    return $this->where('budget_id', $budget_id)->where('budget_category_id', $category_id)->find_all();
  }
  
  /**
   * Validation.
   */
  public function validate(array &$array, $save = FALSE) {
    $array = Validation::factory($array)
    ->pre_filter('trim')
    ->add_rules('description', 'required')
    ->add_rules('date', 'required', 'date')
    ->add_rules('amount', 'required', 'numeric')
    ->add_rules('check_no', 'numeric')
    ->add_rules('budget_category_id', 'required', 'numeric')
    ->add_rules('budget_id', 'required', 'numeric');
    return parent::validate($array, $save);
  }
  
  /**
   * Before insert hook.
   */
  public function before_insert() {
    $this->user_id = kohana::config('chapterboard.user_id');
    $this->created = date::to_db();
    $this->date = date::input_to_db($this->date);
  }
  
  /**
   * Before update hook.
   */
  public function before_update() {
    $this->date = date::input_to_db($this->date);
  }
  
}