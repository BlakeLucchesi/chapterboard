<?php defined('SYSPATH') or die('No direct script access.');

class Banking_Controller extends Finances_Controller {
  
  public function index() {
    $this->title = 'Bank Accounts';
    if ($post = $this->input->post()) {
      $account = ORM::factory('deposit_account');
      if ($account->validate($post, TRUE)) {
        $vars = array('site_name' => $this->site->name());
        email::notify(Kohana::config('email.admins'), 'admin_bank_added', $vars, $this->site->name());
        if ($_POST['fundraising_enabled']) {
          $this->_enable_fundraising();
        }
        if ($_POST['collections_enabled']) {
          $this->_enable_collections();
        }
      }
      else {
        $this->form   = $post->as_array();
        $this->errors = $post->errors('form_premium_signup');
        $this->view   = 'finances/banking/index';
      }
    }
    $this->deposit_account = $this->site->deposit_accounts()->current();
  }
  
  /**
   * One-click enable for collections or fundraising.
   */
  public function service() {
    if ($this->input->post('fundraising_enabled')) {
      $this->_enable_fundraising();
    }
    if ($this->input->post('collections_enabled')) {
      $this->_enable_collections();
    }
    url::redirect('finances/banking');
  }
  
  /**
   * Perform shared actions to enable collections or fundraising.
   */
  private function _enable_collections() {
    $this->site->collections_setup();
    $vars = array('site_name' => $this->site->name());
    email::notify(Kohana::config('email.admins'), 'admin_collections_enabled', $vars, $this->site->name());
    message::add(TRUE, 'Thank you for enabling online collections.  Your members are now able to pay their dues online. You can <a href="/finances/charges">begin adding charges</a> to members accounts. If you have questions email us at team@chapterboard.com or use our <a href="/support">support form</a>.');
  }
  
  private function _enable_fundraising() {
    $this->site->fundraising_setup();
    $vars = array('site_name' => $this->site->name());
    email::notify(Kohana::config('email.admins'), 'admin_fundraising_enabled', $vars, $this->site->name());
    message::add(TRUE, 'Thank you for enabling online fundraising. Get started by <a href="/finances/fundraising/add">creating your first campaign</a>. If you have questions email us at team@chapterboard.com or use our <a href="/support">support form</a>.');
  }
  
  public function add() {
    $this->title = 'Add Bank Account';
    if ($post = $this->input->post()) {
      $account = ORM::factory('deposit_account');
      if ($account->validate($post, TRUE)) {
        message::add(TRUE, 'Your Bank Account has been added.');
        $vars = array('site_name' => $this->site->name());
        email::notify(Kohana::config('email.admins'), 'admin_bank_added', $vars, $this->site->name());
        url::redirect('finances/banking');
      }
      else {
        message::add(FALSE, 'Please fix the errors below.');
        $this->form   = $post->as_array();
        $this->errors = $post->errors('form_premium_signup');
      }
    }
  }
  
  public function edit($id) {
    $this->account = ORM::factory('deposit_account', $id);

    if ( ! $this->account->loaded)
      Event::run('system.404');
    if ($this->account->site_id != $this->site->id)
      Event::run('system.404');

    $this->title = 'Edit Account Name';
    $this->form = $this->account->as_array();
    if ($post = $this->input->post()) {
      if ($this->account->validate_update($post, TRUE)) {
        message::add(TRUE, 'Bank Account Name has been updated.');
        url::redirect('finances/banking');
      }
      else {
        message::add(FALSE, 'Please fix the errors below.');
        $this->form   = $post->as_array();
        $this->errors = $post->errors('form_premium_signup');
      }
    }
  }
  
  public function delete($id) {
    url::redirect('finances/banking');
    $this->account = ORM::factory('deposit_account', $id);

    if ( ! $this->account->loaded)
      Event::run('system.404');
    if ($this->account->site_id != $this->site->id)
      Event::run('system.404');

    $vars = array('site_name' => $this->site->name());
    email::notify(Kohana::config('email.admins'), 'admin_bank_removed', $vars, $this->site->name());
    message::add(TRUE, 'Bank account has been archived. ');
  }
}