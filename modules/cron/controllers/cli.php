<?php defined('SYSPATH') or die('No direct script access.');

class CLI_Controller extends Controller {

	/**
	 * Constructor
	 *
	 * If we aren't running from the command line we throw a 404.
	 */
	public function __construct() {
	  if (PHP_SAPI != 'cli') {
	    Event::run('system.404');
	  }
		parent::__construct();
	}
  
  
	/**
	 * Includes a View within the controller scope.
	 *
	 * @param   string  view filename
	 * @param   array   array of view variables
	 * @return  string
	 */
	public function _kohana_load_view($kohana_view_filename, $kohana_input_data)
	{
		if ($kohana_view_filename == '')
			return;

		// Buffering on
		ob_start();

		// Import the view variables to local namespace
		extract($kohana_input_data, EXTR_SKIP);

		try
		{
			// Views are straight HTML pages with embedded PHP, so importing them
			// this way insures that $this can be accessed as if the user was in
			// the controller, which gives the easiest access to libraries in views
			include $kohana_view_filename;
		}
		catch (Exception $e)
		{
			// Display the exception using its internal __toString method
			echo $e;
		}

		// Fetch the output and close the buffer
		return ob_get_clean();
	}
}