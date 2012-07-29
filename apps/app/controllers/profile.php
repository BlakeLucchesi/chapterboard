<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Default Kohana controller. This controller should NOT be used in production.
 * It is for demonstration purposes only!
 *
 * @package    Core
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Profile_Controller extends Private_Controller {
  
  /**
   * Display a user profile.
   */
  function show($id = NULL) {
    $this->account = $id ? ORM::factory('user')->profile()->find($id) : $this->user;
    javascript::add('scripts/tabs.js');
    
    if ( ! A2::instance()->allowed($this->account, 'view'))
      Event::run('system.403');
    
    $this->title = sprintf('%s\'s Profile', $this->account->name());
    
    $count = 15;
    $this->topics = ORM::factory('topic')->where('topics.status', 1)->where('user_id', $this->account->id)->orderby('created', 'DESC')->find_all($count);
    $this->comments = ORM::factory('comment')->where('comments.status', 1)->where('user_id', $this->account->id)->orderby('created', 'DESC')->find_all($count);
    $this->events = ORM::factory('event')->where('events.status', 1)->where('user_id', $this->account->id)->orderby('created', 'DESC')->find_all($count);
  }
  
  function edit($id = NULL) {
    $id = is_null($id) ? $this->user->id : $id;
    $this->title = 'Edit Profile';
    $this->profile = ORM::factory('profile', $id);  // Load through profile id instead of using $this->user (currently logged in user).
    
    if ( ! $this->profile->loaded)
      Event::run('system.404');
    if ( ! A2::instance()->allowed($this->profile->user, 'edit'))  // If they don't have access to edit the account throw error.
      Event::run('system.403');

    if ($post = $this->input->post()) {

      // Handle picture upload.
      $valid = upload::validate('picture', 'image');
      if ($valid->validate()) {
        $info = upload::info('picture');
        if ($fileinfo = upload::save('picture', $info['filename'], Kohana::config('upload.directory'))) {
          // var_dump($fileinfo);
          $this->profile->user->picture = empty($fileinfo) ? $this->profile->picture : $fileinfo['filename'];
          $this->profile->user->save();
        }
        
        // Handle profile update
        if ($this->profile->validate($post) && $this->profile->user->validate_login_update($post)) {
          $this->profile->save();
          $this->profile->user->email = $post['email'];
          $this->profile->user->password = $post['password'];
          $this->profile->user->save();
          message::add('success', 'Profile updated succesfully.');
          url::redirect('profile/'. $this->profile->user_id);
        }
        else {
          message::add(FALSE, 'Your changes could not be saved.  Please correct the fields noted below and try again.');
          $this->errors = $post->errors('form_profile');
        }
      }
      else {
        message::add(FALSE, 'Your changes could not be saved. Please upload a valid profile image of type jpg, gif or png.');
        // $this->errors['picture'] = 'Invalid image, please upload a valid .';
      }
    }

    $input = is_object($post) ? $post->as_array() : array();
    
    if ($this->user->id == $id) { // Editing their own account.
      $this->form = array_merge($this->user->as_array(), $this->user->profile->as_array(), $input);
    }
    else {
      $this->account = ORM::factory('user')->profile()->find($id);
      $this->form = array_merge($this->account->as_array(), $this->account->profile->as_array(), $input);
    }
    unset($this->form['password']);
    unset($this->form['password_confirm']);
  }
  
}