<?php

class Errors_Hook {
  
  public function __construct() {
    Event::add('system.403', array($this, 'show_403'));
    Event::replace('system.404', array('Kohana', 'show_404'), array($this, 'show_404'));
  }

  public function show_403() {
    header('HTTP/1.1 403 Forbidden');
    Kohana::$instance = new Error_Controller;
    if (request::is_ajax())
      response::json(FALSE, 'Forbidden.');
      
    View::factory('templates/403.tpl')->render(TRUE);
    die();
  }

  public function show_404() {
    header('HTTP/1.1 404 File Not Found');
    Kohana::$instance = new Error_Controller;
    if (request::is_ajax())
      response::json(FALSE, 'File not found.');
      
    View::factory('templates/404.tpl')->render(TRUE);
    die();
  }

}
new Errors_Hook;
