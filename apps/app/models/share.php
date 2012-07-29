<?php defined('SYSPATH') or die('No direct script access.');

class Share_Model extends ORM {
  
  protected $belongs_to = array('user');
  
  public function validate(array &$array, $save = FALSE) {
    $array = Validation::factory($array)
      ->pre_filter('trim', TRUE)
      ->pre_filter('ucwords', 'name')
      ->add_rules('email', 'required', 'email')
      ->add_rules('name', 'required', 'standard_text')
      ->add_rules('message', 'required');
    return parent::validate($array, $save);
  }
  
  public function before_insert() {
    $this->message = strtr($this->message, array('[Name]' => $this->name));
    $this->user_id = kohana::config('chapterboard.user_id');
    $this->created = date::to_db();
  }
}