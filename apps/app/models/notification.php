<?php defined('SYSPATH') or die('No direct script access.');

class Notification_Model extends ORM {
  
  protected $belongs_to = array('user');
  
  /**
   * Show which notifications a user has signed up for.
   * 
   * @param $object a string representing a resource such as 'forum' or 'calendar'.
   */
  public function find_by_object($object, $user_id = NULL) {
    $user_id = is_null($user_id) ? kohana::config('chapterboard.user_id') : $user_id;
    $results = $this->where('user_id', $user_id)->where('object_type', $object)->find_all();
    $items = array();
    if ($results->count()) {
      foreach ($results as $result) {
        $items[$result->object_id] = $result;
      }
    }
    return $items;
  }

  /**
   * Return all notifications by user id.
   */
  public function find_by_user($user_id = NULL) {
    $user_id = is_null($user_id) ? kohana::config('chapterboard.user_id') : $user_id;
    return $this->where('user_id', $user_id)->find_all();
  }

  /**
   * Signup a user to receive a notification.
   */
  public function signup($object, $object_id, $user_id = NULL, $value = 1) {
    $user_id = is_null($user_id) ? kohana::config('chapterboard.user_id') : $user_id;
    $this->user_id = $user_id;
    $this->object_type = $object;
    $this->object_id = $object_id;
    $this->value = $value;
    return $this->save();
  }
  
  
  /**
   * Update notification subscriptions based on permissions.
   *
   * Remove any subscriptions to resources which the user no
   * longer has access to view.
   *
   * @param int, object $user.
   */
  public function sync_user_permissions($user) {
    $user = is_numeric($user) ? ORM::factory('user', $user) : $user;
    if ($user instanceof User_Model) {
      $notifications = ORM::factory('notification')->find_by_user($user->id);
      foreach ($notifications as $notification) {
        switch ($notification->object_type) {
          case 'forum':
          case 'calendar':
            $object = ORM::factory($notification->object_type, $notification->object_id);
            if ( ! A2::instance()->is_allowed($user, $object, 'view')) {
              $notification->delete();
            }
        }
      }
    }
  }
  
  /**
   * Update the notification subscriptions for all users on the site.
   */
  public function sync_site_permissions($site_id = NULL) {
    $site_id = is_null($site_id) ? kohana::config('chapterboard.site_id') : $site_id;
    $users = ORM::factory('user')->where('site_id', $site_id)->where('status', 1)->find_all();
    foreach ($users as $user) {
      ORM::factory('notification')->sync_user_permissions($user);
    }
  }

}