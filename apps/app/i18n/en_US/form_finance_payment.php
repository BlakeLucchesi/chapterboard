<?php defined('SYSPATH') or die('No direct script access.');

$lang = array(
  
  'message' => 'There were errors with your request, please make the suggested changes and try again:', // Shown at the top of the following errors.
  
  'amount' => array(
    'overpaid' => 'Your <b>Total Amount</b> is larger than what you owe. Please adjust your payment amounts and try again.',
    'overpayment' => 'You cannot record a payment larger than the amount owed on the charge.',
    'zero' => 'You cannot have a payment for $0.00, instead, delete the payment record if you wish to zero the payment.',
    'negative' => 'You cannot pay a negative amount. Please adjust your payment amounts and try again.',
    'minimum' => 'The minimum online payment amount is $10.00.  Contact your treasurer to pay amounts less than $10.00.',
  ),

  'first_name' => array(
    'required' => '<b>First Name</b> is required.',
    'default' => '<b>First Name</b> is required.'
  ),
  'last_name' => array(
    'required' => '<b>Last Name</b> is required.',
    'default' => '<b>Last Name</b> is required.'
  ),
  'address' => array(
    'required' => '<b>Billing Address</b> is required.',
    'default' => '<b>Billing Address</b> is required.'
  ),
  'city' => array(
    'required' => '<b>City</b> is required.',
    'default' => '<b>City</b> is required.'
  ),
  'state' => array(
    'length' => '<b>State</b> is required.',
    'required' => '<b>State</b> is required.',
    'default' => '<b>State</b> is required.'
  ),
  'zip' => array(
    'length' => 'Incorrect <b>Zip Code</b> length.',
    'required' => 'Incorrect zip code length.',
    'default' => 'Incorrect zip code length.'
  ),
  
  'card_num' => array(
    'credit_card' => '<b>Credit Card Number</b> is in an invalid format. Please check your number and try again.',
    'numeric' => '<b>Credit Card Number</b> is in an invalid format. Please check your number and try again.',
    'required' => '<b>Credit Card Number</b> is required.',
    'failed_processing' => 'Your payment was declined by the payment processor.  Please fix the error below:',
  ),
  'card_code' => array(
    'required' => '<b>CVV</b> must be 3 digits for Visa or Mastercard and 4 digits for American Express.',
    'default' => '<b>CVV</b> must be 3 digits for Visa or Mastercard and 4 digits for American Express.'
  ),
  'month' => array(
    'expired' => 'Your card\'s <b>expiration date</b> has past.'
  ),
  
  'RoutingNumber' => array(
    'numeric' => 'Please enter the 9 digit routing number of your bank.',
    'required' => 'Please enter the 9 digit routing number of your bank.',
    'default' => 'Please enter the 9 digit routing number of your bank.'
  ),
  'AccountNumber' => array(
    'numeric' => 'Please enter your checking account number.',
    'required' => 'Please enter your checking account number.',
    'default' => 'Please enter your checking account number.'
  ),
  'Phone' => array(
    'numeric' => 'Please enter your 10 digit phone number.',
    'required' => 'Please enter your 10 digit phone number.',
    'default' => 'Please enter your 10 digit phone number.',
  ),  
);