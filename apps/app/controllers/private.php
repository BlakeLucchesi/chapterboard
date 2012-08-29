<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Allows a template to be automatically loaded and displayed. Display can be
 * dynamically turned off in the controller methods, and the template file
 * can be overloaded.
 *
 * To use it, declare your controller to extend this class:
 * `class Your_Controller extends Template_Controller`
 *
 * $Id: template.php 3769 2008-12-15 00:48:56Z zombor $
 *
 * @package    Core
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */

abstract class Private_Controller extends Controller_Core {

  // Template view name
  public $template = 'page';

  public $title = '';

  public $view = NULL; // Define an overridable view name
  
  public $site;

  public $user;
  
  // Default to do auto-rendering
  public $auto_render = TRUE;

  /**
   * Template loading and setup routine.
   */
  public function __construct()
  {
    parent::__construct();
    $this->session = Session::instance();
    $this->cache = new Cache;
    if ( ! IN_PRODUCTION && $this->input->get('dbg')) {
      new Profiler;
    }
    
    $this->_auth();
    
    $this->_inbox();
    
    // Load javascript files.
    javascript::add('https://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js');
    $scripts = array(
      // 'jquery/jquery-1.3.2.min.js',
      'jquery/jquery-ui-datepicker.js',
      'jquery/jquery.thickbox-compressed.js',
      'jquery/jquery.qtip-1.0.0-rc1.min.js',
      'jquery/jquery.tablesorter.min.js',
      'jquery/jquery.form.js',
      'jquery/jquery.tinysort.min.js',
      'jquery/jquery.metadata.js',
      'jquery/jquery.cookie.js',
      'scripts/site.js'
    );
    foreach ($scripts as $script) {
      javascript::add($script);
    }

    css::add('styles/thickbox.css', 'module');
    css::add('styles/dashboard.css', 'theme');
    css::add('styles/style.css', 'theme');
    css::add('styles/print.css', 'system', 'print');
    css::add('jquery/theme/ui.core.css', 'module');
    css::add('jquery/theme/ui.theme.css', 'module');
    css::add('jquery/theme/ui.datepicker.css', 'module');

    $this->_pre_controller();

    if ($this->auto_render == TRUE)
    {
      // Render the template immediately after the controller method
      Event::add('system.post_controller', array($this, '_render'));
    }
  }
  
  /**
   * Stub pre-controller function().
   */
  public function _pre_controller() {}
  
  /**
   * Query database for inbox count.
   */
  public function _inbox() {
    $this->inbox_count = ORM::factory('message')->count_unread($this->user->id);
    $this->inbox_indicator = $this->inbox_count > 0 ? sprintf('Inbox (%s)', $this->inbox_count) : 'Inbox';
  }
  
  /**
   * Control who is allowed to access which pages.
   */
  public function _auth() {
    // Setup default variables available to all controllers and views.
    $this->acl = A2::instance();
    $this->auth = A1::instance();
    $this->user = $this->acl->get_user();

    // If the user account or site is no longer active or does not exist, logout and redirect.
    if ( ! is_object($this->user) || $this->user->id < 1 || $this->user->status == 0 || $this->user->site->status != 1) {
      $this->auth->logout();
    }
    else if ($this->user->id && $this->user->status) {
      
      // Setup some sitewide vars including: $this->site.
      if ($site_id = $this->session->get('site_id')) {
        $site = ORM::factory('site', $site_id);
        if ($site->loaded) {
          $this->site = $site;
        }
      }
      if ( ! $this->site->loaded) {
        $this->site = $this->user->site;
      }
    }
    
    // If user is not authenticated: send them to login, register or reset password.
    $all_public_routes = $public_routes = array_merge(Kohana::config('a2.public_routes'), Kohana::config('a2.public_only_routes'));
    if ( ! $this->auth->logged_in() && ! in_array(Router::$controller .'/'. Router::$method, $all_public_routes)) {
      if (request::is_ajax()) {
        response::json(FALSE, 'Please log in.', (object)array());
      }

      $this->session->set('redirect', URI::$current_uri); // Setup url redirection upon login.
      url::redirect('login');
    }
    // If user IS authenticated: don't show login, reset or register pages.
    else if ( $this->auth->logged_in() && in_array(Router::$controller .'/'. Router::$method, Kohana::config('a2.public_only_routes'))) {
      url::redirect('');
    }
  }
  
  /**
   * Render the loaded template.
   */
  public function _render() {
    // Load the template and perform auto rendering.
    $this->template = new View('templates/'. $this->template .'.tpl');
    $this->template->primary = new View('menu/primary');
    $this->template->secondary = ''; // Initialize secondary menu variable.

    if (isset($this->secondary)) {
      $this->template->secondary = View::factory($this->secondary)->render();
    }

    // If content is defined, don't load a view.
    if (isset($this->content)) {
      $this->template->content = $this->content;
    }
    else {
      // Load up the default view based on the controller and action.
      // Override the default by defining the view in $this->view
      if ( ! $this->view) {
        $rsegments = Router::$rsegments;
        $this->view = implode('/', array_slice($rsegments, 0, count($rsegments) - count(Router::$arguments)));
        if (Router::$method == 'index' && substr($this->view, -5) != 'index') {
          $this->view .= '/index';
        }
      }
      $this->template->content = View::factory(inflector::template($this->view))->render();      
    }
    
    if ($this->auto_render == TRUE)
    {
      // Render the template when the class is destroyed
      $this->template->render(TRUE);
    }
  }

}