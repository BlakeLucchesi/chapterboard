<?php defined('SYSPATH') or die('No direct script access.');

class Login_Controller extends Web_Controller {
  
  public $template = 'login';
  public $title = 'Login';

  /**
   * Provide a user login page.
   */
  public function login() {
    // Try and log the user in.    
    if ($post = $this->input->post()) {
      $remember = $post['remember'] ? TRUE : FALSE;
      if ($this->auth->login($post['email'], $post['pass'], $remember)) {
        $this->user = $this->auth->get_user();
        if ($this->user->logins == 1) {
          url::redirect('profile/edit/'. $this->user->id);
        }

        // Check to see if the user was prompted to login prior to going to their destination.
        $url = $this->session->get('redirect') ? $this->session->get_once('redirect') : 'dashboard';
        url::redirect($url); // send to user profile page.
      }
      else {
        // User failed authentication.
        $this->error = $this->auth->get_error();
        $this->form = $post;
      }
    }
    $this->title = 'Login';
  }
  
  /**
   * Logout.
   */
  public function logout() {
    $this->auth->logout(TRUE);
    url::redirect('login');
  }

  /**
   * Password recovery.
   */
  function reset() {
    $this->title = 'Password Recovery';    
    if ($post = $this->input->post()) {
      if ($user = ORM::factory('user')->password_recover($post['email'])) {
        $this->view = 'login/reset-thank-you';
        return;
      }
      else {
        message::add('error', "Sorry, we don't have a record for that email address in our system.");
        $this->errors = TRUE;
      }
      $this->form = $post;
    }
  }
  
}