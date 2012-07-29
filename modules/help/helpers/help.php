<?php defined('SYSPATH') or die('No direct script access.');

class Help_Core {
  
  static $match = NULL;
  
  /**
   * 
   * @return boolean Whether or not there is a help file for the current view.
   */
  function find_match() {
    if ( ! Kohana::config('help.enabled'))
      return FALSE;
    
    if (is_null(self::$match)) {
      self::$match = FALSE;
      $rules = (array) Kohana::config('help.rules');
      foreach ($rules as $regex => $rule) {
        if (preg_match($regex, Router::$routed_uri) === 1) {
          self::$match = $rule;
          continue;
        }
      }
    }
    // var_dump(Router::$routed_uri);
    // var_dump(self::$match);
    return self::$match;
  }
  
  /**
   * Show a link to the help doc.
   */
  function link($match = NULL, $link_text = NULL) {
    if ($match = is_null($match) ? self::find_match() : $match) {
      $link_text = ! is_null($link_text) ? $link_text : Kohana::config('help.link_text');
      # TODO: Put in a file lookup here to make sure that there is indeed help text available.
      if (TRUE) {
        return html::thickbox_anchor('help/'. $match, $link_text, array('class' => 'help-link'));
      }
    }
  }
}