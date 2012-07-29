<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Test cases for the Authorize.net Payment Gateway.
 *
 * Using credit card number '4007000000027' performs a successful test.  This
 * allows you to test the behavior of your script should the transaction be
 * successful.  If you want to test various failures, use '4222222222222' as
 * the credit card number and set the x_amount field to the value of the 
 * Response Reason Code you want to test.  
 * 
 * For example, if you are checking for an invalid expiration date on the
 * card, you would have a condition such as:
 * if ($a->response['Response Reason Code'] == 7) ... (do something)
 *
 * Now, in order to cause the gateway to induce that error, you would have to
 * set x_card_num = '4222222222222' and x_amount = '7.00'
 */

class Payment_Authorize_Test extends Unit_Test_Case {
  
  // Store the $payment object for use in each test.
  protected $payment;
  
  protected $data = array(); // The sample request post data. 
    
  protected $assert_method = 'assert_false';
  
  /**
   * Perform the actual test result.
   *
   * Each test method defines the sample post data using $this->data, and
   * sets the assert_method using $this->assert_method.
   */
  public function teardown() {
    if ( ! empty($this->data)) {
      $this->payment = new Payment();
      $this->payment->set_fields($this->data);
      // $this->{$this->assert_method}($this->payment->process());
    }
  }
  
  /**
   * Test to make sure that we can successfully process a card.
   */
  public function visa_success_test() {
    $this->data = array(
      'amount' => 18.68,
      'card_num' => '4007000000027',
      'exp_date' => date('my', time() + (24*360))
    );
    $this->assert_method = 'assert_true';
  }
    
  /**
   * Test to make sure that a request with invalid payment data
   * gets rejected.
   */
  public function denied_exp_date_test() {
    $this->data = array(
      'amount' => 200.00,
      'card_num' => '4222222222222',
      'exp_date' => date('my', time() - 9000000),
    );
  }
  
  public function denied_card_num_test() {
    $this->data = array(
      'amount' => 100,
      'card_num' => '4007003000023',
      'exp_date' => date('my', time() + 234333)
    );
  }
  
  /**
   * Test for error responses.  This is when certain data is invalid or
   * if a connection is not available to the Authorize.net server.
   */
  public function error_test() {
    $data = array(
      'amount' => 100,
      'exp_date' => '1010'
    );
  }
  
}