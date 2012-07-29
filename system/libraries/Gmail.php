<?php

class Gmail {

  var $mbox;

  function Gmail($user, $pass) {
    $this->mbox = imap_open("{imap.gmail.com:993/imap/ssl/novalidate-cert}INBOX",$user,$pass)
      or die("can't connect: " . imap_last_error());
  }

  function openSentMail() {
    imap_reopen($this->mbox, "{imap.gmail.com:993/imap/ssl/novalidate-cert}[Gmail]/Sent Mail")
      or die("Failed to open Sent Mail: " . imap_last_error());
  }

  function openMailBox($mailbox) {
    imap_reopen($this->mbox, "{imap.gmail.com:993/imap/ssl/novalidate-cert}$mailbox")
      or die("Failed to open $mailbox: " . imap_last_error());
  }

  function getMailboxInfo() {
    $mc = imap_check($this->mbox);
    return $mc;
  }
  
  function __destruct() {
    imap_close($this->mbox);
  }

  /**
   * Read all the unseen emails.
   */
  function getNewEmails() {
    $uids = imap_search($this->mbox, 'UNSEEN');
    $messages = array();
    foreach ($uids as $k=>$uid) {
      $messages[] = $this->retrieve_message($uid);
    }
    return $messages;
  }
  
  /**
   * Search through emails.
   */
  function getEmailsBySearch($query) {
    $uids = imap_search($this->mbox, $query);
    $messages = array();
    foreach ($uids as $k => $uid) {
      $messages[] = $this->retrieve_message($uid);
    }
    return $messages;
  }
  
  /**
   * $date should be a string
   * Example Formats Include:
   * Fri, 5 Sep 2008 9:00:00
   * Fri, 5 Sep 2008
   * 5 Sep 2008
   * I am sure other's work, just test them out.
   */
  function getHeadersSince($date) {
    $uids = $this->getMessageIdsSinceDate($date);
    $messages = array();
    foreach ($uids as $k=>$uid) {
      $messages[] = $this->retrieve_header($uid);
    }
    return $messages;
  }

  /**
   * $date should be a string
   * Example Formats Include:
   * Fri, 5 Sep 2008 9:00:00
   * Fri, 5 Sep 2008
   * 5 Sep 2008
   * I am sure other's work, just test them out.
   */
  function getEmailSince($date) {
    $uids = $this->getMessageIdsSinceDate($date);
    $messages = array();
    foreach ($uids as $k=>$uid) {
      $messages[] = $this->retrieve_message($uid);
    }
    return $messages;
  }

  function getMessageIdsSinceDate($date) {
    return imap_search( $this->mbox, 'SINCE "'.$date.'"');
  }

  function retrieve_header($messageid) {
    $message = array();

    $header = imap_header($this->mbox, $messageid);
    $structure = imap_fetchstructure($this->mbox, $messageid);

    $message['subject'] = $header->subject;
    $message['fromaddress'] =   $header->fromaddress;
    $message['toaddress'] =   $header->toaddress;
    $message['ccaddress'] =   $header->ccaddress;
    $message['date'] =   $header->date;

    return $message;
  }

  function retrieve_message($messageid) {
    $message = array();

    $header = imap_header($this->mbox, $messageid);
    $structure = imap_fetchstructure($this->mbox, $messageid);

    $message['subject'] = $header->subject;
    $message['fromaddress'] =   $header->fromaddress;
    $message['toaddress'] =   $header->toaddress;
    $message['ccaddress'] =   $header->ccaddress;
    $message['date'] =   $header->date;

    if ($this->check_type($structure)) {
      $message['body'] = imap_fetchbody($this->mbox,$messageid,"1"); ## GET THE BODY OF MULTI-PART MESSAGE
      if ( ! $message['body']) {
        $message['body'] = '[NO TEXT ENTERED INTO THE MESSAGE]nn';
      }
    }
    else {
      $message['body'] = imap_body($this->mbox, $messageid);
      if ( ! $message['body']) {
        $message['body'] = '[NO TEXT ENTERED INTO THE MESSAGE]nn';
      }
    }
    return $message;
  }

  function check_type($structure) {
    if ($structure->type == 1) {
      return(TRUE); ## YES THIS IS A MULTI-PART MESSAGE
    }
    else {
      return(FALSE); ## NO THIS IS NOT A MULTI-PART MESSAGE
    }
  }

}