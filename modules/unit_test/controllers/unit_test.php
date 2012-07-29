<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Unit_Test controller.
 *
 * $Id: unit_test.php 3769 2008-12-15 00:48:56Z zombor $
 *
 * @package    Unit_Test
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Unit_test_Controller extends Controller {

	const ALLOW_PRODUCTION = FALSE;

  /**
   * Perform all unit tests.
   */
	public function index() {

	  // Change database access to the test connection.
    // Kohana::config_set('database.default', Kohana::config('database.test'));
	  
		// Run tests and show results!
		echo new Unit_Test;
	}

}