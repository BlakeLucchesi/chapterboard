<?php defined('SYSPATH') or die('No direct script access.');

$lang = array(
  'name' => array(
    'standard_text' => 'Please remove any non alpha numeric letters.',
    'required' => 'Account nickname is required.',
    'default' => 'Please enter an account nickname such as "Checking Account"'
  ),
  'bank_name' => array(
    'standard_text' => 'Please remove any non alpha numeric letters.',
    'required' => 'Bank name is required.',
    'default' => 'Please enter your bank\s name'
  ),
  'routing_number' => array(
    'length' => 'Routing numbers contain 9 digits.',
    'numeric' => 'Routing number can only contain digits.',
    'required' => 'Routing number is required',
    'default' => 'Please enter your bank\'s routing number.'
  ),
  'account_number' => array(
    'numeric' => 'Account number can only contain digits.',
    'required' => 'Account number is required.',
    'default' => 'Please enter your checking account number.'
  ),  
);