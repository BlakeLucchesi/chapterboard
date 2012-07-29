<?php defined('SYSPATH') or die('No direct script access.');

class Cardnumber_Validation_Test extends Unit_Test_Case {
  
  /**
   * Define an array of the accepted card types.
   */
  public $types = array('american express', 'mastercard', 'visa', 'discover');
    
  /**
   * Test known valid numbers.
   */
  public function valid_credit_card_test() {
    $valid = array(
      '6011000579730239' => 'discover',
      '4128003616565381' => 'visa',
      '5590330000480265' => 'mastercard',
      '371555109651004' => 'american express'
    );
    
    foreach ($valid as $number => $type) {
      $this->assert_equal(TRUE, valid::credit_card($number, $this->types));      
    }
  }
  
  /**
   * Test known invalid numbers.
   */
  public function invalid_credit_card_test() {
    $invalid = array(
      '444433332222111' => 'visa', // 15 digit.
      '6011000579730238' => 'discover',  // incorrect last digit.
    );
    foreach ($invalid as $number => $type) {
      $this->assert_equal(FALSE, valid::credit_card($number, $this->types));
    }
  }
}