<?php defined('SYSPATH') or die('No direct script access.');

class response {
  
  /**
   * Provide a json encoded ajax response.
   */
  function json($status = TRUE, $message = NULL, $data = NULL) {
    header('Cache-Control: no-cache, must-revalidate');
    header('Content-type: application/json');
    $response = array(
      'status' => $status == TRUE ? 'success' : 'error',
      'message' => $message,
      'data' => $data
    );
    print json_encode($response);
    Event::run('system.shutdown');
    die;
  }
  
  function html($content) {
    print $content;
    Event::run('system.shutdown');
    die;
  }
  
  function csv($rows, $filename = 'chapterboard-export') {
    header("Content-type: application/text/x-csv");
    header("Content-Disposition: attachment; filename=$filename.csv");
    foreach ($rows as $row) {
      $output .= '"'. implode('","', $row) .'"'. "\n";
    }
    print $output;
    Event::run('system.shutdown');
    die();
  }
}