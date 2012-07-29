<?php defined('SYSPATH') or die('No direct script access.');

class Deposit_Model extends ORM {
  
  protected $has_many = array('deposit_transactions');
  
  protected $belongs_to = array('site', 'deposit_account');
  
  protected $sorting = array('created' => 'DESC');
  
  protected $load_with = array('deposit_account');
  
  public function count() {
    return $this->db->query("SELECT COUNT(*) count FROM deposit_transactions WHERE site_id = ?", array(kohana::config('chapterboard.site_id')))->current()->count;
  }
  
  /**
   * Find deposits for the site.
   */
  public function find($limit = NULL, $offset = NULL) {
    $this->where('deposits.site_id', kohana::config('chapterboard.site_id'));
    return parent::find($limit, $offset);
  }

  /**
   * Find all.
   */
  public function find_all($limit = NULL, $offset = NULL) {
    $this->where('deposits.site_id', kohana::config('chapterboard.site_id'));
    return parent::find_all($limit, $offset);
  }
  
}