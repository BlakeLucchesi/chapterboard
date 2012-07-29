<?php defined('SYSPATH') or die('No direct script access.');

class Settings_Controller extends Private_Controller {

  public $secondary = 'menu/settings';
  
  public function __construct() {
    parent::__construct();
    if ( ! A2::instance()->allowed($this->site, 'admin'))
      Event::run('system.404');
  }
  
  /**
   * Provide some general chapter settings and provide billing status.
   */
  public function index() {
    $this->title = 'Chapter Settings';
    $this->members = ORM::factory('user')->select_members($this->user);
    
    $this->form['slug'] = $this->site->slug;
    if ($post = $this->input->post()) {
      $this->errors = $form = array();
      if ($this->site->validate_account_update($post, TRUE)) {
        message::add(TRUE, Kohana::lang('form_settings.success'));
        url::redirect('settings');
      }
      else {
        message::add(FALSE, Kohana::lang('form_settings.error'));
        $this->form = $post->as_array();
        $this->errors = $post->errors('form_settings');
      }
    }
  }
  
  /**
   * Queue a backup.
   */
  public function backup() {
    $queue = ORM::factory('backup_queue')->queue_backup($this->site->id, $this->user->id);
    message::add(TRUE, 'Your backup has been queued. You will receive an email with more details when the process is complete.');
    url::redirect('settings');
  }

}