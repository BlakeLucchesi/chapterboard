<?php defined('SYSPATH') or die('No direct script access.');

class School_Model extends ORM {
  
  protected $has_many = array('sites');
  
  protected $sorting = array('name' => 'ASC');
  
  function before_insert() {
    $this->search_name = $this->_token($this->name);
  }
  
  function before_update() {
    $this->search_name = $this->_token($this->name);
  }
  
  protected function _token($string) {
    return strtolower(preg_replace('/[^a-zA-Z0-9\s]/i', '', $string));
  }
  
}