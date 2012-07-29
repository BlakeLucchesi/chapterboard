<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * CSRF helper class.
 *
 *
 * @package    Core
 * @author     Kohana Team
 * @copyright  (c) 2009 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */

class csrf_Core {

  public static function token()
  {
    if (($token = Session::instance()->get('csrf')) === FALSE)
    {
      Session::instance()->set('csrf', ($token = text::random('alnum', 16)));
    }

    return $token;
  }

  public static function valid($token)
  {
    return ($token === Session::instance()->get('csrf'));
  }

}