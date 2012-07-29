<?php defined('SYSPATH') or die('No direct script access.');

class List_Controller extends Recruitment_Controller {
  
  public function _auth_check($id) {
    $this->recruit = ORM::factory('recruit', $id);
    if ( ! $this->recruit->loaded)
      Event::run('system.404');
    if ( ! A2::instance()->allowed($this->recruit, 'admin'))
      Event::run('system.403');
  }
  
  public function recruiting($id) {
    $this->_auth_check($id);
    $this->recruit->update_list(0);
    message::add('success', '%s has been moved to the "Actively Recruiting" list.', $this->recruit->name);
    url::redirect('recruitment/show/'. $id);
  }
  
  public function bidded($id) {
    $this->_auth_check($id);    
    $this->recruit->update_list(1);
    // Make sure they are set to pending any time they are moved to the bidded list.
    $this->recruit->update_bid_status(0);
    message::add('success', '%s has been moved to the "Bidded Members" list.', $this->recruit->name);
    url::redirect('recruitment/show/'. $id);
  }
  
  public function not_recruiting($id) {
    $this->_auth_check($id);
    $this->recruit->update_list(2);
    message::add('success', '%s has been moved to the "No Longer Recruiting" list.', $this->recruit->name);
    url::redirect('recruitment/show/'. $id);
  }
  
}