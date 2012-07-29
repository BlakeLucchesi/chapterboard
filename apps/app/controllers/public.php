<?php defined('SYSPATH') OR die('No direct access allowed.');
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
abstract class Public_Controller extends Controller {

	// Template view name
	public $template = 'template';

	// Default to do auto-rendering
	public $auto_render = TRUE;

	/**
	 * Template loading and setup routine.
	 */
	public function __construct()
	{
		parent::__construct();
		
		$this->_pre_controller();

    // Load javascript files.
    if ($_SERVER['HTTPS']) {
      javascript::add('https://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js');
    }
    else {
      javascript::add('http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js');
    }
    $scripts = array(
      'jquery/jquery-ui-datepicker.js',
      'jquery/jquery.thickbox-compressed.js',
      'jquery/jquery.qtip-1.0.0-rc1.min.js',
      'jquery/jquery.tablesorter.min.js',
      'jquery/jquery.form.js',
      'jquery/jquery.tinysort.min.js',
      'jquery/jquery.metadata.js',
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

		if ($this->auto_render == TRUE)
		{
			// Render the template immediately after the controller method
			Event::add('system.post_controller', array($this, '_render'));
		}
	}
	
	// Override this in your sub-class to setup things for all controller actions.
	public function _pre_controller() {}

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

} // End Template_Controller