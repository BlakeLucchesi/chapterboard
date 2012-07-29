<?php defined('SYSPATH') or die('No direct script access.');

class Javascript_Controller extends Controller {

  public $auto_render = FALSE;

	public function load($filename)
	{
		$cache = Cache::instance();
		$checksum = basename($filename, '.js');

		if ( ! $output = $cache->get($checksum))
			throw new Kohana_User_Exception('javascript', 'Fatal error : Could not load the cached file for : '.$checksum);

		header('Content-Type: application/x-javascript');
		echo $output;
	}

} // End Javascript Controller