<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Migrate Class
 *
 * Utility main controller.
 */

class Migrations_Controller extends Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->migrations = new Migrations(TRUE);
	}
	
	public function index()
	{
		$view = new View('migrations/index');
		$view->current_version = $this->migrations->get_schema_version();
		$view->last_version    = $this->migrations->last_schema_version();
		echo $view;
	}
	
	public function update()
	{
		if (isset($_POST['version']))
		{
			$this->migrations->version($_POST['version']);
		}
		else
		{
			$this->migrations->install();
		}
		
		echo '<p>'.html::anchor('migrations/index', 'OK').'</p>';
	}
}