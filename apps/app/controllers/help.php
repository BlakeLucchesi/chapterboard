<?php defined('SYSPATH') or die('No direct script access.');

class Help_Controller extends Private_Controller {
  
  public function __call($method, $args) {
    $this->index();
  }
  
  public function index() {
    $this->view = implode('/', Router::$segments);
    if ( ! file_exists(APPPATH.'views/'. $this->view.EXT)) {
      $this->view = 'help/home';
    }
    
    // If the help text is needed in a popup, don't use the layout.
    if (request::is_ajax()) {
      $output = View::factory('templates/help.tpl');
      $output->content = View::factory($this->view);
      $output->content .= View::factory('help/footer');
      response::html($output);
    }
    else {
      // We need to make html::primary_anchor think that the help text being viewed is
      // for the section they are in, so we adjust the Router's static variables.
      $this->title = 'Help';
      if (Router::$rsegments[1] == 'dashboard') {
        Router::$rsegments = array();
      }
      else {
        Router::$rsegments[0] = Router::$rsegments[1];
      }
      if (Kohana::find_file('views', 'menu/'. Router::$segments[1], FALSE)) {
        $this->secondary = 'menu/'. Router::$segments[1];
      }
    }
  }
  
  public function hide($key) {
    $this->user = A2::instance()->get_user();
    $this->user->help($key, TRUE);
    response::json(TRUE, 'Hidden.');
  }
  
}