<?php defined('SYSPATH') or die('No direct script access.');

class Bid_Controller extends Recruitment_Controller {
  
  public function _auth_check($id) {
    $this->recruit = ORM::factory('recruit', $id);
    if ( ! $this->recruit->loaded)
      Event::run('system.404');
    if ( ! A2::instance()->allowed($this->recruit, 'admin'))
      Event::run('system.403');
  }
  
  public function pending($id) {
    $this->_auth_check($id);
    $this->recruit->update_bid_status(0);
    message::add('success', Kohana::lang('recruitment.bid.pending'), $this->recruit->name);
    url::redirect('recruitment/show/'. $id);
  }

  public function accepted($id) {
    $this->_auth_check($id);
    $this->recruit->update_bid_status(1);
    message::add('success', Kohana::lang('recruitment.bid.accepted'), $this->recruit->name);
    url::redirect('recruitment/show/'. $id);
  }
  
  public function declined($id) {
    $this->_auth_check($id);    
    $this->recruit->update_bid_status(2);
    message::add('success', Kohana::lang('recruitment.bid.declined'), $this->recruit->name);
    url::redirect('recruitment/show/'. $id);
  }
}