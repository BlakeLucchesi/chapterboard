<?php defined('SYSPATH') or die('No direct script access.');

$lang = array(
  'charge' => array(
    'subject' => 'Payment due for !charge_title, due on !due_date',
    'message_finances' => "!name,\r\n\r\nYou currently owe !due_amount for the charge: !charge_title.  This amount is/was due on !due_date. Use the link below to make a credit card payment for this charge:\r\n\r\n!pay_link",
    'message_basic' => "!name,\r\n\r\nYou currently owe !due_amount for the charge: !charge_title.  This amount is/was due on !due_date."
  ),
  'members' => array(
    'subject' => 'Outstanding Balance Reminder',
    'message_finances' => "!name,\r\n\r\nYou currently have an outstanding balance of !due_amount. Use the link below to make a credit card payment.\r\n\r\n!pay_link",
    'message_basic' => "!name,\r\n\r\nYou currently have an outstanding balance of !due_amount. Please make payment to your chapter Treasurer.",
  ),
  'success' => 'Reminders have been sent.',
  'errors' => 'There was an error sending reminders. Please try again.',  
);