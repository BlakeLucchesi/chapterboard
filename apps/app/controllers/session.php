<?php defined('SYSPATH') or die('No direct script access.');

class Session_Controller extends Private_Controller {
  
  /**
   * Switch sites.
   */
  function teleport() {
    $id = $this->input->post('site_id');
    $this->site = ORM::factory('site', $id);
    
    if ( ! $this->site->loaded) {
      message::add(FALSE, 'Site not found.');
      url::redirect('dashboard');
    }
    
    if ( ! A2::instance()->allowed($this->site, 'teleport')) {
      message::add(FALSE, 'You do not have permission to view that chapter.');
      url::redirect('dashboard');
    }
    
    $this->session->set('site_id', $id);
    if ($this->user->has_role('root')) {
      message::add(TRUE, 'Now browsing %s', $this->site->name());
    }
    else {
      message::add(TRUE, 'Now browsing %s', $this->site->chapter_name());
    }
    url::redirect('dashboard');
  }
  
  /**
   * Change user account.
   */
  public function shapeshift() {
    $id = $this->input->post('user_id');
    $this->account = ORM::factory('user', $id);

    if ( ! $this->account->loaded) {
      message::add(FALSE, 'User not found.');
      url::redirect('dashboard');
    }
    
    if ( ! A2::instance()->allowed('site', 'shapeshift')) {
      message::add(FALSE, 'You do not have permission to change user accounts.');
      url::redirect('dashboard');
    }
    
    $this->session->delete('site_id'); // Make sure we aren't still teleporting under a new user id.
    A1::instance()->complete_login($this->account);
    message::add(TRUE, 'You are now logged in as %s from %s', $this->account->name(), $this->account->site->name());
    url::redirect('dashboard');
  }
  
}