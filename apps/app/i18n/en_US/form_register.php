<?php defined('SYSPATH') or die('No direct access allowed.');
 
$lang = array(
  'first_name' => array(
    'required' => 'Please fill in your name.',
    'length' => 'First name must be longer than 1 character.',
    'default' => 'Invalid Input.',
  ),
  'last_name' => array(
    'required' => 'Please fill in your last name.',
    'length' => 'Last name must be longer than 1 character.'
  ),
  'email' => array(
    'unique' => 'An account with that email already exists.',
    'default' => 'Invalid email address.',
  ),
  'school_id' => array(
    'default' => 'Please choose a school.',
  ),
  'chapter_id' => array(
    'default' => 'Please choose a chapter.'
  ),
  'password' => array(
    'length' => 'Password must contain 6 or more characters.',
    'required' => 'Please enter a password.',
  ),
  'password_confirm' => array(
    'matches' => 'Passwords entered do not match.',
    'required' => 'Please confirm your password.',
  ),
  'reg_code' => array(
    'required' => 'Please enter a registration code.',
    'code' => 'Invalid registration code.',
    'default' => 'Invalid Input.',
  ),
  'agreement' => array(
    'required' => 'You must <b>read</b> and agree before you can continue.',
    'default' => 'You must <b>read</b> and agree before you can continue.',
  ),
  'default' => array(
    'default' => 'Required'
  ),
);