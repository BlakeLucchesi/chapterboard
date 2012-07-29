<?php

/**
 * SMS Provider
 *
 * Options: twilio. (more to come as needed).
 */
$config['provider'] = 'twilio';

/**
 * Authentication strings.
 *
 * Define authentication settings using keys for
 * each of the values your provider expects.
 */
$config['auth'] = array(
  'sid' => 'AC1d2bdbf07368bc265eab84df9872932a',
  'token' => '8e94e5d1bc08dc42ccd68eae588f2418'
);

/**
 * Driver REST Version.
 *
 * This is an optional value specific to the providers implementation.
 */
$config['version'] = '2008-08-01';

/**
 * Origin Phone Number
 */
$config['number'] = '4158003041'; // 415-800-3041

###
### Email to SMS configuration.
###

/**
 * Carriers.
 */
$config['carriers'] = array(
  'alltel'      => array('name' => 'Alltel', 'address' => 'message.alltel.com'),
  'att'         => array('name' => 'AT&T', 'address' => 'txt.att.net'),
  'bell'        => array('name' => 'Bell', 'address' => 'txt.bell.ca'),
  'boost'       => array('name' => 'Boost Mobile', 'address' => 'myboostmobile.com'),
  'cricket'     => array('name' => 'Cricket', 'address' => 'sms.mycricket.com'),
  'koodo'       => array('name' => 'Koodo Mobile', 'address' => 'msg.koodomobile.com'),
  'metropcs'    => array('name' => 'MetroPCS', 'address' => 'mymetropcs.com'),
  'nextel'      => array('name' => 'Nextel', 'address' => 'messaging.nextel.com'),
  'ntelos'      => array('name' => 'nTelos Wireless', 'address' => 'pcs.ntelos.com'),
  'rogers'      => array('name' => 'Rogers', 'address' => 'pcs.rogers.com'),
  'sprint'      => array('name' => 'Sprint', 'address' => 'messaging.sprintpcs.com'),
  'telus'       => array('name' => 'Telus', 'address' => 'msg.telus.com'),
  'tmobile'     => array('name' => 'T-Mobile', 'address' => 'tmomail.net'),
  'uscellular'  => array('name' => 'US Cellular', 'address' => 'email.uscc.net'),
  'verizon'     => array('name' => 'Verizon', 'address' => 'vtext.com'),
  'virgin'      => array('name' => 'Virgin Mobile', 'address' => 'vmobl.com'),
  'wind_mobile' => array('name' => 'Wind Mobile', 'address' => 'txt.windmobile.ca'),
);


/**
 * Debug mode.
 */
$config['debug'] = TRUE;
if (IN_PRODUCTION) {
  $config['debug'] = FALSE;
}
