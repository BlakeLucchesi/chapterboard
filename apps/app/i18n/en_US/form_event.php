<?php defined('SYSPATH') or die('No direct access allowed.');
 
$lang = array(
  'title' => array(
    'required' => 'Every event needs a title.',
    'alpha' => 'Only alphabetic characters are allowed.',
    'default' => 'Invalid Input.',
  ),
  'body' => array(
    'default' => 'Invalid email address.',
  ),
  'location' => array(
    'default' => 'Please enter a location.'
  ),
  'start' => array(
    'required' => 'Please select a date for this event.',
    'datetime' => 'Please enter a valid start day/time. Example: 11/20/2011 4pm.',
    'default' => 'Please enter a valid start day/time. Example: 11/20/2011 4pm.',
  ),
  'end' => array(
    'datetime' => 'Please enter a valid end day/time. Example: 4pm 11/20/2011.',
    'end_before_start' => 'The end date must not come before your start date. (You may need to enter an end time as well.)',
    'default' => 'Please enter a valid end day/time. Example: 4pm 11/20/2011.',
  ),
  'calendar_id' => array(
    'required' => 'Please choose a calendar from the list.',
    'default' => 'Please choose a calendar from the list.'
  ),
);