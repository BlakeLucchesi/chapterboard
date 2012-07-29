<?php defined('SYSPATH') or die('No direct script access.');

class Cron_Controller extends CLI_Controller {
  
  /**
   * Cron task dispatcher.
   */
  public function index($task = 'daily') {  
    $path = Kohana::config('cron.path');

    // Since we're running via cron we need to set server name manually.
    $_SERVER["SERVER_NAME"] = 'chapterboard.com';

    switch ($task) {
      case 'daily':
      case 'hourly':
      case 'monthly':
        $files = glob("$path/$task/*.cron.php");
        break;
      default:
        $files = array("$path/custom/$task.cron.php");      
    }

    // Load libraries.
    $this->db = new Database();
    
    // Run each of the cron tasks from the specified group.
    foreach ($files as $file) {
      include $file;
      print "\n$file\n";
    }
    print "\n";
  }
}