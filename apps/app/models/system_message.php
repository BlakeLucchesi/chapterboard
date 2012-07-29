<?php defined('SYSPATH') or die('No direct script access.');

class System_message_Model extends ORM {
  
  protected $sorting = array('created' => 'DESC');
  
  /**
   * Retrieve system notifications for the currently logged in user.
   */
  public function get() {
    return $this->custom_where("(post_until > '%s') AND (all_sites = 1 OR site_id = %d OR chapter_id = %d OR school_id = %d)",
      array(
        date::to_db('now'),
        kohana::config('chapterboard.site_id'),
        kohana::config('chapterboard.chapter_id'),
        kohana::config('chapterboard.school_id')
      ))->where('status', 1)->find_all();
  }
  
}