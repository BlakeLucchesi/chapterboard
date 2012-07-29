<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Email helper class.
 *
 * $Id: email.php 3769 2008-12-15 00:48:56Z zombor $
 *
 * @package    Core
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class email_Core {

	// SwiftMailer instance
	protected static $mail;
	
	public static $history = array();

	/**
	 * Creates a SwiftMailer instance.
	 *
	 * @param   string  DSN connection string
	 * @return  object  Swift object
	 */
	public static function connect($config = NULL)
	{
		if ( ! class_exists('Swift', FALSE))
		{
			// Load SwiftMailer
			require_once Kohana::find_file('vendor', 'swiftmailer/lib/swift_required');
		}

		// Load default configuration
		($config === NULL) and $config = Kohana::config('email');

		switch ($config['driver'])
		{
			case 'smtp':
				// Set port
				$port = empty($config['options']['port']) ? NULL : (int) $config['options']['port'];

				// Create a SMTP connection
				$connection = Swift_SmtpTransport::newInstance( $config['options']['hostname'], $port );

				if (!empty($config['options']['encryption']))
				{
					// Set encryption
					switch (strtolower($config['options']['encryption']))
					{
						case 'tls':
						case 'ssl':
							$connection->setEncryption( $config['options']['encryption'] );
							break;
					}
				}

				// Do authentication, if part of the DSN
				empty($config['options']['username']) or $connection->setUsername($config['options']['username']);
				empty($config['options']['password']) or $connection->setPassword($config['options']['password']);

				if ( ! empty($config['options']['auth']))
				{
					// Get the class name and params
					list ($class, $params) = arr::callback_string($config['options']['auth']);

					if ($class === 'PopB4Smtp')
					{
						// Load the PopB4Smtp class manually, due to its odd filename
						require Kohana::find_file('vendor', 'swift/Swift/Authenticator/$PopB4Smtp$');
					}

					// Prepare the class name for auto-loading
					$class = 'Swift_Authenticator_'.$class;

					// Attach the authenticator
					$connection->attachAuthenticator(($params === NULL) ? new $class : new $class($params[0]));
				}

				// Set the timeout to 5 seconds
				$connection->setTimeout(empty($config['options']['timeout']) ? 5 : (int) $config['options']['timeout']);
			break;
			case 'sendmail':
				// Create a sendmail connection
				$connection = Swift_SendmailTransport::newInstance( $config['options'] );
			break;
			default:
				// Use the native connection
				$connection = Swift_MailTransport::newInstance();
			break;
		}

		// Create the SwiftMailer instance
		return email::$mail = Swift_Mailer::newInstance($connection);
	}

	/**
	 * Send an email message.
	 *
	 * @param   string|array  recipient email (and name), or an array of To, Cc, Bcc names
	 * @param   string|array  sender email (and name)
	 * @param   string        message subject
	 * @param   string        message body
	 * @param   boolean       send email as HTML
	 * @return  integer       number of emails sent
	 */
	public static function send($to, $from, $subject, $text, $html = FALSE)
	{
		// Connect to SwiftMailer
		(email::$mail === NULL) and email::connect();

		// Create the message.
		if ($html) {
      $message = Swift_Message::newInstance($subject);
      $message->setBody($html, 'text/html');
      $message->addPart($text, 'text/plain');
		}
		else {
      $message = Swift_Message::newInstance($subject);
      $message->setBody($text, 'text/plain');
		}
		
	  $message->setEncoder(Swift_Encoding::get7BitEncoding());

		if (Kohana::config('email.debug')) {
		  $debug = array('email' => array(
		    'from' => $from,
		    'to' => $to,
		    'subject' => $subject,
		    'text' => $text,
		    'html' => $html,
		  ));
      log::system('email', sprintf('Sending email to: %s', $to), 'notice', $debug);
      return TRUE;
    }
		else if (Kohana::config('email.test_only')) {
      $to = Kohana::config('email.test_only_address');
    }
    
		if (is_string($to))
		{
			// Single recipient
			$recipients = $message->setTo($to);
		}
		elseif (is_array($to))
		{
			if (isset($to[0]) AND isset($to[1]))
			{
				// Create To: address set
				$to = array('to' => $to);
			}

			foreach ($to as $method => $set)
			{
				if ( ! in_array($method, array('to', 'cc', 'bcc')))
				{
					// Use To: by default
					$method = 'to';
				}

				// Create method name
				$method = 'add'.ucfirst($method);

				if (is_array($set))
				{
					// Add a recipient with name
					$message->$method($set[0], $set[1]);
				}
				else
				{
					// Add a recipient without name
					$message->$method($set);
				}
			}
		}

		if (is_string($from))
		{
			// From without a name
			$from = $message->setFrom($from);
		}
		elseif (is_array($from))
		{
			// From with a name
			$from = $message->setFrom( array($from[0] => $from[1]) );
		}
		
    self::$history[] = array(
      'to' => $recipients,
      'from' => $from,
      'subject' => $subject,
      'text' => $text,
      'html' => $html
    );
		return email::$mail->send($message);
	}

	/**
	 * Send an email message.
	 *
	 * @param   string|array  recipient email (and name), or an array of To, Cc, Bcc names
	 * @param   string|array  sender email (and name)
	 * @param   string        message subject
	 * @param   string        message body
	 * @param   boolean       send email as HTML
	 * @return  integer       number of emails sent
	 */
	public static function send_multipart($to, $from, $subject, $plain='', $html='', $attachments=array())
	{
		// Connect to SwiftMailer
		(email::$mail === NULL) and email::connect();

		// Create the message
		$message = Swift_Message::newInstance($subject);
		
		//Add some "parts"
		switch(true)
		{
			case (strlen($html) AND strlen($plain)):
				$message->setBody($html, 'text/html');
				$message->addPart($plain, 'text/plain');
				break;
				
			case (strlen($html)):
				$message->setBody($html, 'text/html');
				break;
				
			case (strlen($plain)):
				$message->setBody($plain, 'text/plain');
				break;
				
			default:
				$message->setBody('', 'text/plain');
		}

		if(!empty($attachments))
		{
			foreach( $attachments AS $file => $mime )
			{
				$filename = basename( $file );
				
				//Use the Swift_File class
				$message->attach(Swift_Attachment::fromPath( $file )->setFilename( $filename ));
			}
		}
		
		if (Kohana::config('email.debug')) {
      log::system('email', sprintf('Sending email to: %s', $to), 'notice', array('from' => $from, 'to' => $to, 'subject' => $subject, 'text' => $plain, 'html' => $html));
      return TRUE;
    }
		else if (Kohana::config('email.test_only')) {
      $to = Kohana::config('email.test_only_address');
    }

		if (is_string($to))
		{
			// Single recipient
			$recipients = $message->setTo($to);
		}
		elseif (is_array($to))
		{
			if (isset($to[0]) AND isset($to[1]))
			{
				// Create To: address set
				$to = array('to' => $to);
			}

			foreach ($to as $method => $set)
			{
				if ( ! in_array($method, array('to', 'cc', 'bcc')))
				{
					// Use To: by default
					$method = 'to';
				}

				// Create method name
				$method = 'add'.ucfirst($method);

				if (is_array($set))
				{
					// Add a recipient with name
					$message->$method($set[0], $set[1]);
				}
				else
				{
					// Add a recipient without name
					$message->$method($set);
				}
			}
		}

		if (is_string($from))
		{
			// From without a name
			$from = $message->setFrom($from);
		}
		elseif (is_array($from))
		{
			// From with a name
			$from = $message->setFrom( array($from[0] => $from[1]) );
		}

		return email::$mail->send($message);
	}
	
	/**
	 *
	 * Sends a system email using the system wide email templates.
	 *
   * @param $to
   * @param $template
   * @param $variables that are passed into the template and available in the lang file for the subject.
   * @param ... additional variables that will be passed into the Kohana::lang method for email subjects.
   *
   */
  static public function notify($to, $template, $vars) {
    $temp = func_get_args();
    $args = array_slice($temp, 3);
      
    // Email variables
    $from       = Kohana::config('email.from');
    $subject    = Kohana::lang('email.subjects.'. $template, $args);
    $template   = inflector::template($template);
    
    // Plain text emails
    $body       = View::factory("emails/$template")->bind('vars', $vars)->render();
    $footer     = View::factory('emails/global-footer')->bind('to', $to)->render();
    $text       = View::factory('emails/global-template')->bind('body', $body)->bind('footer', $footer)->bind('to', $to)->render();
    
    // If HTML email template exists, send it.
    if (Kohana::find_file('views', "emails/$template.html")) {
      $body     = View::factory("emails/$template.html")->bind('vars', $vars)->render();
      $html     = View::factory('emails/global-template.html')->bind('body', $body)->bind('to', $to)->render();
    }

    return email::send($to, $from, $subject, $text, $html);
  }
  
  /**
	 *
	 * Sends a system email using the system wide email templates. This differs
	 * from the email::send() method in that we can set a unique reply-to address
	 * as opposed to using a system defined email reply address.  This is helpful
	 * when users are sending announcements to one another instead of the system 
	 * sending out a notification.
	 *
	 * Also notice that the system wide footer is not included in announcement emails.
	 *
   * @param $to
   * @param $from
   * @param $template
   * @param $variables that are passed into the template and available in the lang file for the subject.
   * @param ... additional variables that will be passed into the Kohana::lang method for email subjects.
   * @param
   * the view as an email template.
   */
  static public function announcement($to, $from, $template, $vars) {
    $temp = func_get_args();
    $args = array_slice($temp, 4);
      
    // Email variables
    $from       = $from ? $from : Kohana::config('email.from');
    $subject    = Kohana::lang('email.subjects.'. $template, $args);
    $text       = View::factory('emails/'. inflector::template($template))->bind('vars', $vars)->render();
    
    // If HTML email template exists, send it.
    if (Kohana::find_file('views', "emails/$template.html")) {
      $body     = View::factory("emails/$template.html")->bind('vars', $vars)->render();
      $html     = View::factory('emails/global-template.html')->bind('body', $body)->bind('to', $to)->render();
    }

    return email::send($to, $from, $subject, $text, $html);
  }
  
  /**
   * Format the from address for notification emails.
   */
  static public function notification_address($object, $id) {
    return array(sprintf('notification+%s-%d@chapterboard.com', $object, $id), 'ChapterBoard Notification');
  }
} // End email