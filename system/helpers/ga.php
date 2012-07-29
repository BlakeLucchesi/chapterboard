<?php defined('SYSPATH') or die('No direct script access.');

class ga_Core {
  
  public static $items;
  
  public static function custom_var($index, $name, $value, $opt_scope = 3) {
    $value = preg_replace('/\s/', '_', $value);
    $value = preg_replace('/[^_a-zA-Z]/i', '', $value);
    self::_add_item(json_encode(array('_setCustomVar', $index, $name, $value, $opt_scope)));
  }
  
  public static function add_trans($orderId, $affiliation, $total, $tax = '', $shipping = '', $city ='', $state = '', $country = '') {
    self::_add_item(json_encode(array('_addTrans', $orderId, $affiliation, $total, $tax, $shipping, $city, $state, $country)));
  }
  
  public static function add_item($orderId, $sku, $name, $category, $price, $quantity) {
    self::_add_item(json_encode(array('_addItem', $orderId, $sku, $name, $category, $price, $quantity)));
  }
  
  public static function track_trans() {
    self::_add_item(json_encode(array('_trackTrans')));
  }
  
  public static function add_event($category, $action, $label, $value) {
    $_SESSION['ga_events'][] = array(
      'category' => $category,
      'action' => $action,
      'label' => $label,
      'value' => $value ? $value : 1
    );
  }
  
  public static function render() {
    $o = '';
    if (Kohana::config('config.google_analytics_enabled')) {
      $id = Kohana::config('config.google_analytics_id');
      $domain = Kohana::config('config.google_analytics_domain');
      $o  = '<script type="text/javascript">';
      $o .= 'var _gaq = _gaq || [];';
      $o .= "_gaq.push(['_setAccount', '$id']);";
      if ($domain) {
        $o .= "_gaq.push(['_setDomainName', '$domain']);";
      }
      $o .= ga::_get_items();
      $o .= '_gaq.push(["_trackPageview"]);';
      $o .= ga::_get_events();
      $o .= "(function() {";
      $o .=  "var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;";
      $o .=  "ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';";
      $o .=  "var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);";
      $o .= "})();";
      $o .= "</script>";
    }
    return $o;
  } 
  
  private static function _add_item($item) {
    $data = empty($_SESSION['ga_data']) ? array() : $_SESSION['ga_data'];
    array_push($data, $item);
    $_SESSION['ga_data'] = $data;
  }

  /**
   * Prepare all the extra tracking data for output.
   */
  private static function _get_items() {
    if ($_SESSION['ga_data']) {
      foreach ($_SESSION['ga_data'] as $item) {
        $output .= "_gaq.push($item);";
      }
      unset($_SESSION['ga_data']);
      return $output;
    }
    return '';
  }
  
  private static function _get_events() {
    if ( ! empty($_SESSION['ga_events'])) {
      foreach ($_SESSION['ga_events'] as $item) {
        $output .= "_gaq.push(['_trackEvent', '{$item['category']}', '{$item['action']}', '{$item['label']}', {$item['value']}]);";
      }
      unset($_SESSION['ga_events']);
      return $output;
    }
    return '';
  }
 
}