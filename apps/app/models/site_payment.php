<?php defined('SYSPATH') or die('No direct script access.');

class Site_payment_Model extends ORM {
  
  protected $belongs_to = array('site');
  
  protected $sorting = array('created' => 'DESC');
  
  public function find_all($limit = NULL, $offset = NULL) {
    $this->where('site_id', kohana::config('chapterboard.site_id'));
    return parent::find_all($limit, $offset);
  }
}