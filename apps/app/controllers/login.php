<?php defined('SYSPATH') or die('No direct script access.');

class Login_Controller extends Private_Controller {
  
  public $template = 'login';
  public $title = 'Login';

  /**
   * Pre Controller.
   */
  public function _pre_controller() {
    css::add('styles/login.css');
  }
  
  /**
   * Provide a user login page.
   */
  public function login() {
    $this->title = 'Please Login';

    // Try and log the user in.
    if ($post = $this->input->post()) {
      if ($this->auth->login($post['email'], $post['pass'], $post['remember'] ? TRUE : FALSE)) {
        $this->user = $this->auth->get_user();

        if (request::is_ajax()) {
          response::json(TRUE, 'Logged in successfully.', $this->_user_info());
        }
        if ($this->user->logins == 1) {
          url::redirect('profile/edit/'. $this->user->id);
        }
        // Check to see if the user was prompted to login prior to going to their destination.
        $url = $this->session->get('redirect') ? $this->session->get_once('redirect') : 'dashboard';
        ga::add_event('User', 'Login', $this->user->id .' logged in.', 1);
        url::redirect($url); // send to user profile page.
      }
      else {
        if (request::is_ajax()) {
          response::json(FALSE, 'Invalid login credentials.');
        }
        // User failed authentication.
        $_SESSION['failed_logins']++;
        if (in_array($_SESSION['failed_logins'], array(3, 6, 9, 12, 15))) {
          log::system('user_login', "Failed login attempt #{$_SESSION['failed_logins']} from {$_SERVER['REMOTE_ADDR']}. {$post['email']} / {$post['pass']}");          
        }
        $this->error = $this->auth->get_error();
        $this->form = $post;
      }
    }
    if (request::is_ajax()) {
      response::json(FALSE, 'Please log in.', $this->_user_info());
    }
  }
  
  /**
   * Logout.
   */
  public function logout() {
    $this->auth->logout(TRUE);
    if (request::is_ajax()) {
      response::json(TRUE, 'Logged out successfully.');
    }
    url::redirect('login');
  }
  
  /**
   * Provide an account creation form.
   */
  public function register($token) {
    $this->invite = ORM::factory('invite')->where('token', $token)->find();
    if ( ! $this->invite->loaded) {
      $this->view = 'login/register-invalid';
      return;
    }

    if ($post = $this->input->post()) {

      // Set values from invite.
      $post['email'] = $this->invite->email;
      $post['site_id'] = $this->invite->site_id;
      $post['type'] = $this->invite->group->static_key;

      // If valid we create user account, delete invite, log the user in.
      $this->user = ORM::factory('user');
      if ($this->user->validate($post, TRUE)) {
        // Add default group from invite record.
        $this->user->add($this->invite->group);
        $this->user->save();
        $this->invite->delete();  // delete invite record.
        A1::instance()->complete_login($this->user);
        url::redirect('profile/edit/'. $this->user->id);
      }
      else {
        $this->form = $post->as_array();
        $this->errors = $post->errors('form_register');
      }
    }
    // Display registration form with any errors.
    $this->title = 'Create new user account';
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
    
  /**
   * New site confirmation.
   */
  public function setup() {
    $this->title = 'Last Step';
    
    if ($post = $this->input->post()) {
      if (ORM::factory('site')->validate_account_setup($post)) {
        // Create the new site.
        $this->signup = ORM::factory('site_signup')->where('confirmed', 0)->where('confirm_token', strtoupper($_POST['confirm_token']))->find();
        $vars = array_merge($_POST, $this->signup->as_array());
        $site = ORM::factory('site')->setup($vars);
        
        // Create the user's account, and make them the site admin
        $user = ORM::factory('user')->new_admin($vars, $site);
        
        $this->signup->disable(); // Show the signup as confirmed and thus no longer available.
        A1::instance()->complete_login($user); // Log the user in.
        ga::add_event('Signup', 'New Chapter', $site->name(), 1);
        url::redirect('profile/edit/'. $user->id);
      }
      else {
        $this->form = $post->as_array();
        $this->errors = $post->errors('form_setup');
        message::add(FALSE, 'There were errors with the information you entered. Please fix and try again.');
      }
    }
    $this->timezones = array('' => 'Please Choose') + date::timezones();
  }
  
  /**
   * Check whether or not a session is current.
   */
  public function check() {
    $user = A1::factory()->get_user();
    response::json($user->id, NULL, $this->_user_info());
  }
  
  public function _user_info() {
    if ( ! $this->user->loaded)
      return (object)array();
    $info = array(
      'name' => $this->user->name(),
      'id' => $this->user->id,
      'type' => $this->user->type(),
      'picture_url' => theme::image('small', $this->user->picture(), array(), TRUE),
      'permissions' => array(
        'finances' => A2::instance()->allowed('finance', 'manage'),
        'sms' => A2::instance()->allowed('sms', 'manage')
      ),
    );
    return $info;
  }
}