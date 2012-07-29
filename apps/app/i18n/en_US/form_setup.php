<?php defined('SYSPATH') or die('No direct script access.');

$lang = array(
  'confirm_token' => array(
    'invalid' => 'Incorrect secret code.',
    'required' => 'Please enter the secret code we sent to your phone.'
  ),
  'password' => array(
    'length' => 'Your password must be 6 characters or longer.',
    'required' => 'Please enter a password for your new account.',
  ),
  'password_confirm' => array(
    'matches' => 'Password confirmation field does not match.',
    'required' => 'Please confirm your password.'
  ),
  'timezone' => array(
    'default' => 'Select the timezone your school is located in.',
    'required' => 'Select the timezone your school is located in.'
  ),
  'bank_name' => array(
    'depends_on' => 'Routing and account numbers are required.'
  ),
  'routing_number' => array(
    'length' => 'Routing number must be 9 digits.',
    'depends_on' => 'Please enter you bank\'s routing number.',
  ),
  'account_number' => array(
    'numeric' => 'Please remove any non numeric characters.',
    'depends_on' => 'Please enter your bank account number.',
  ),
  'slug' => array(
    'unique' => 'Sorry but that nickname is already taken, please choose another.',
    'reserved' => 'Sorry but that nickname is already taken, please choose another.',
    'required' => 'Please enter a unique nickname for your chapter.',
    'default' => 'Please enter a unique nickname for your chapter.'
  ),
);