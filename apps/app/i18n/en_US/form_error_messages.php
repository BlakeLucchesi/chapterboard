<?php defined('SYSPATH') or die('No direct access allowed.');
 
$lang = array(
  'name' => array(
    'required' => 'Please fill in your name.',
    'alpha' => 'Only alphabetic characters are allowed.',
    'length' => 'Your name has to be longer than that.',
    'default' => 'Invalid Input.',
  ),
  'mail' => array(
    'default' => 'Invalid email address.',
  ),
  'mail_confirm' => array(
    'default' => 'Invalid email address.',
    'matches' => 'Email address does not match.',
  ),
  'code' => array(
    'numeric' => 'Only numbers are allowed.',
    'default' => 'Invalid Input.',
  ),
  'password' => array(
    'required' => 'You must supply a password.',
    'pwd_check' => 'The password is not correct.',
    'default' => 'Invalid Input.',
  ),
);