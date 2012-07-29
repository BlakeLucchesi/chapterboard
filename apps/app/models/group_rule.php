<?php defined('SYSPATH') or die('No direct script access.');

class Group_rule_Model extends ORM {
  
  protected $belongs_to = array('site', 'group');
  
  /**
   * Build an array containing all access rules for the current site.
   */
  public function get_rules($resource, $site_id = NULL) {
    $site_id = is_null($site_id) ? kohana::config('chapterboard.site_id') : $site_id;
    
    $groups = $this->where(array('resource' => $resource, 'site_id' => $site_id))->find_all();
    
    $resources = array();
    foreach ($groups as $group) {
      $resource_ids = unserialize($group->resource_ids);
      foreach ($resource_ids as $id) {
        $resources[$id][$group->group_id] = TRUE;
      }
    }
    return $resources;
  }
  
  /**
   * Return an array of groups who are allowed to access the given resource.
   */
  public function get_rules_for_resource($resource, $resource_id, $privilege) {
    $rules = $this->get_rules($resource);
    return $rules[$resource_id];
  }
  
  /**
   * Return an ORM_Iterator of group names that are allowed to access a resource.
   */
  public function get_groups_for_resource($resource, $resource_id, $privilege) {
    $rules = $this->get_rules($resource);
    return ORM::factory('group')->in('id', array_keys($rules[$resource_id]))->find_all();
  }
  
  /**
   * Perform a save operation setting which resource ids each group
   * is granted access to.
   */
  public function set_rules($resource, $privilege, $permissions) {
    $rules = array();

    // Gather the groups and resources that belong to this site.
    $temp->groups = ORM::factory('group')->where('site_id', kohana::config('chapterboard.site_id'))->find_keyed_object();
    $temp->resource_ids = ORM::factory($resource)->where('site_id', kohana::config('chapterboard.site_id'))->find_keyed_object();

    // Loop through all the new values and check for permissions
    // to make sure no values are used that actually correlate to ids
    // from other sites resources or groups.
    foreach ($permissions as $resource_id => $groups) {
      foreach ($groups as $group_id => $value) {
        if (isset($temp->groups[$group_id]) && isset($temp->resource_ids[$resource_id])) {
          $rules[$group_id][] = $resource_id;
        }
        else {
          return FALSE;
        }
      }
    }

    // Remove all existing privileges and insert new priveleges.
    ORM::factory('group_rule')->where('site_id', kohana::config('chapterboard.site_id'))->where('resource', $resource)->where('privilege', $privilege)->delete_all();
    foreach ($rules as $group_id => $resource_ids) {
      $values = array(
        'resource' => $resource,
        'site_id' => kohana::config('chapterboard.site_id'),
        'group_id' => $group_id,
        'privilege' => $privilege,
        'resource_ids' => serialize($resource_ids),
      );
      $this->insert($values);
    }

    // Before syncing notifications with permissions, we need to clear existing loaded permissions and reload new ones.
    auth::setup_permissions();
    ORM::factory('notification')->sync_site_permissions();
    return TRUE;
  }

  /**
   * Like set_rules but allows us to update the rules for a single resource id.
   *
   * @param string  resource name
   * @param int     resource id
   * @param string  privilege
   * @param array   group_id => group_id, unchecked values DO NOT get passed.
   */
  public function set_rules_for_resource($resource, $resource_id, $privilege, $permissions) {
    // Make sure we're only changing permissions for groups that belong to this site.
    $groups = ORM::factory('group')->where('site_id', kohana::config('chapterboard.site_id'))->find_keyed_object();
    $new_rules = array();
    foreach ($groups as $group_id => $group) {
      if (in_array($group_id, $permissions)) {
        $new_rules[$group_id] = $group_id;
      }
      else {
        $new_rules[$group_id] = NULL;
      }
    }

    // Now we merge the existing rules array with our new one, and save using set_rules.
    $rules = $this->get_rules($resource);
    foreach ($new_rules as $key => $value) {
      if ($value) {
        $rules[$resource_id][$key] = $key;
      }
      else {
        unset($rules[$resource_id][$key]);
      }
    }
    return ORM::factory('group_rule')->set_rules($resource, $privilege, $rules);
  }
  
  /**
   * Insert a rule record.
   */
  public function insert($values) {
    $row = ORM::factory('group_rule');
    foreach ($values as $key => $value) {
      $row->$key = $value;
    }
    return $row->save();
  }
}