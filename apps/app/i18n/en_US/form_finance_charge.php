<?php defined('SYSPATH') or die('No direct script access.');

$lang = array(
  'title' => array(
    'required' => 'Please enter a title.',
    'default' => 'Please enter a title.',
  ),
  'due' => array(
    'required' => 'Due date required',
    'default' => 'Due date required.'
  ),
  'amount' => array(
    'default' => 'Amount must be greater than 0.',
    'required' => 'Amount must be greater than 0.',
    'zero_negative' => 'Amount must be greater than 0.'
  ),
  'budget_id' => array(
    'site_id' => 'Please select a valid budget.'
  ),
  'deposit_account_id' => array(
    'site_id' => 'Please select a valid deposit account.',
    'required' => 'Please select a deposit account.',
    'default' => 'Please select a deposit account.',
  ),
  'late_fee' => array(
    'numeric' => 'Please enter a valid number.',
    'default' => 'Please enter a valid number.',
  ),
  'late_fee_type' => array(
    'standard_text' => 'Please select a late fee amount type.',
    'default' => 'Please select a late fee amount type.',
  )
);