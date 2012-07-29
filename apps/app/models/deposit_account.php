<?php defined('SYSPATH') or die('No direct script access.');

class Deposit_account_Model extends ORM {
  
  protected $belongs_to = array('site');

  public function filter_active() {
    return $this->where('site_id', kohana::config('chapterboard.site_id'))->where('status', TRUE);
  }
  
  public function find_all_active() {
    return $this->where('site_id', kohana::config('chapterboard.site_id'))->where('status', TRUE)->find_all();
  }
  
  public function name() {
    return sprintf('%s: ***%s', $this->name, $this->last_four());
  }
  
  public function bank_name() {
    return sprintf('%s: ***%s', $this->bank_name, $this->last_four());
  }
  
  public function last_four() {
    return substr($this->account_number, -4);
  }
  
  /**
   * Validation.
   */
  public function validate(array &$array, $save = FALSE) {
    $array = Validation::factory($array)
    ->pre_filter('trim')
    ->add_rules('name', 'required', 'blob')
    ->add_rules('bank_name', 'required', 'blob')
    ->add_rules('account_number', 'required', 'numeric')
    ->add_rules('routing_number', 'required', 'numeric', 'length[9]');
    return parent::validate($array, $save);
  }
  
  /**
   * Validation.
   */
  public function validate_update(array &$array, $save = FALSE) {
    $array = Validation::factory($array)
    ->pre_filter('trim')
    ->add_rules('name', 'required', 'blob');
    return parent::validate($array, $save);
  }
  
  public function before_insert() {
    $this->site_id = kohana::config('chapterboard.site_id');
    $this->status = 1;
    $this->created = date::to_db();
    $this->updated = $this->created;
  }
  
  public function before_update() {
    $this->updated = date::to_db();
  }
  
}