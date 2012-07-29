<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Money helper class.
 *
 */
class money_Core {

  static function valid($amount) {
    
    return preg_match('/^[-\$\.\d]\.[\d]{1,2}/i', $amount);
  }
  
  static function display($amount) {
    $amount = money::cleanse($amount);
    $sign = $amount >= 0 ? '' : '-';
    return $sign .'$'. number_format(money::round(abs($amount)), 2);
  }
  
  static function round($amount) {
    $amount = money::cleanse($amount);
    return num::round($amount, .01);
  }
  
  static function percent($amount, $rate, $raw = TRUE) {
    $amount = money::cleanse($amount);
    $value = ($amount) * ((100 - $rate) / 100);
    $value = substr(number_format(round($value, 3), 3), 0, -1);
    return $raw ? $value : money::display($value);
  }

  static function cleanse($amount) {
    return preg_replace('/([^\d\.-])/i', '', $amount);
  }
  
}