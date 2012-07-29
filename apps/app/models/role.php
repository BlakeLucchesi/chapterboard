<?php defined('SYSPATH') or die('No direct script access.');

class Role_Model extends ORM {
  
  protected $has_and_belongs_to_many = array('users');
  
  protected $sorting = array('weight' => 'ASC', 'name' => 'ASC');

  public function unique_key($id = NULL) {
    if ( ! empty($id) AND is_string($id) AND ! ctype_digit($id) )
    {
      return 'key';
    }
    return parent::unique_key($id);
  }

}