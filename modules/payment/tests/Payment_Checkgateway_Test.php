<?php defined('SYSPATH') or die('No direct script access.');

class Payment_Checkgateway_Test extends Unit_Test_Case {
  
  protected $payment;
  
  protected $data = array();
  
  protected $assert_method = 'assert_false';
  
  public function setup() {
    $this->data = array(
      'Amount' => 18.68,
      'AccountNumber' => '2342387987',
      'RoutingNumber' => '999999992',
      'Name' => 'Blake Lucchesi',
      'Address1' => '1301 4th Ave #707',
      'City' => 'Seattle',
      'State' => 'WA',
      'Zip' => '98101',
      'Phone' => '9497849177'
    );
    $this->assert_method = 'assert_false';
  }
  
  public function should_pass__test() {
    $this->assert_method = 'assert_true';
    $this->process();
    $this->assert_equal('Transaction processed.', $this->payment->get_response_reason());
  }
  
  public function should_fail_routing_number__test() {
    $this->data['AccountNumber'] = 100;
    $this->process();
    $this->assert_equal('Bank routing number validation negative (ABA).', $this->payment->get_response_reason());
  }
  
  public function should_fail_routing_number_length__test() {
    $this->data['AccountNumber'] = 200;
    $this->process();
    $this->assert_equal('Bank routing number must be 9 digits.', $this->payment->get_response_reason());
  }
    
  /**
   * Perform the actual test result.
   *
   * Each test method defines the sample post data using $this->data, and
   * sets the expected return result of the process() method by setting
   * $this->expected.
   */
  public function process() {
    if ( ! empty($this->data)) {
      $this->payment = new Payment('echeck');
      $this->payment->set_fields($this->data);
      $this->{$this->assert_method}($this->payment->process());
    }
  }
  
}