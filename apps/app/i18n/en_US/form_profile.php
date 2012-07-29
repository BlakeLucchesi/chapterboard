<?php defined('SYSPATH') or die('No direct script access.');

$lang = array(
  'email' => array(
    'unique' => 'A user with this email address already exists in our system.',
    'default' => 'Please enter a valid email address.'
  ), 
  'password' => array(
    'length' => 'Passwords must be at least 6 characters.'
  ),
  'password_confirm' => array(
    'matches' => 'Your password confirmation did not match. Please re-enter.',
    'default' => 'Your password confirmation did not match. Please re-enter.'
  ),
  'student_id' => array(
    'default' => ''
  ),
  'birthday' => array(
    'birthday_invalid' => 'Please enter your birthday.',
    'required' => 'Please enter a valid date in the format: MM/DD/YYYY',
    'default' => 'Please enter a valid date in the format: MM/DD/YYYY',
  ),
  'phone' => array(
    'phone' => 'Please enter a 10 digit phone number.',
    'required' => 'Please enter a 10 digit phone number.',
    'default' => 'Please enter a 10 digit phone number.'
  ),
  'address1' => array(),
  'address2' => array(),
  'city' => array(),
  'state' => array(),
  'zip' => array(
    'default' => 'Please enter a 5 digit zip code only.'
  ),
  'emergency1_name' => array(),
  'emergency1_phone' => array(
    'phone' => 'Please enter a 10 digit phone number.',
    'default' => 'Please enter a 10 digit phone number.'
  ),
  
  'emergency2_name' => array(),
  'emergency2_phone' => array(
    'phone' => 'Please enter a 10 digit phone number.',
    'default' => 'Please enter a 10 digit phone number.'
  ),
  'shirt_size' => array(
    'Please select a size.'),
  'school_year' => array(
    'default' => 'Please select your school year.'
  ),
  'initiation_year' => array(
    'initiation_year' => 'Please enter the 4 digit year you were initiated in.',
    'numeric' => 'Please enter the 4 digit year you were initiated in.',
    'default' => 'Please enter the 4 digit year you were initiated in.'
  ),
);