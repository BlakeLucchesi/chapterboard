<?php defined('SYSPATH') or die('No direct script access.');

class Support_Controller extends Private_Controller {
  
  public function index() {
    $this->title = 'ChapterBoard Support';
    $this->view = 'support/form';
    $this->method = 'support';
    
    $this->_send('support');
    if ($this->input->post() && $this->input->get('redirect')) {
      url::redirect($this->input->get('redirect'));
    }
  }
  
  public function feedback() {
    $this->title = 'ChapterBoard Feedback';
    $this->view = 'support/form';
    $this->method = 'feedback';

    $this->_send('feedback');
    if ($this->input->post() && $this->input->get('redirect')) {
      url::redirect($this->input->get('redirect'));
    }
  }
  
  public function _send($template = 'support') {
    if ($body = $this->input->post('body')) {
      $to = Kohana::config('app.support_email');
      $vars['body'] = $body;
      $vars['id'] = $this->user->id;
      $vars['user'] = $this->user->name();
      $vars['email'] = $this->user->email;
      $vars['phone'] = $this->user->phone();
      $vars['site'] = $this->site->name();

      email::notify($to, 'support_'. $template, $vars, $this->user->name());
      if ($template == 'feedback') {
        email::notify($this->user->email, 'support_confirm', $vars);
      }
      message::add(TRUE, Kohana::lang('email.support.'.$template .'_success'));
    }
    else if (isset($_POST['body'])) {
      message::add(FALSE, 'Please fill out your message below before sending.');
    }
  }
  
  public function _redirect() {
    if ($this->input->get('redirect')) {
      url::redirect($this->input->get('redirect'));
    }
    else {
      url::redirect('dashboard');
    }
  }
}