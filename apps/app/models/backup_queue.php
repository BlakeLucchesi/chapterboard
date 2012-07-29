<?php defined('SYSPATH') or die('No direct script access.');

class Backup_queue_Model extends ORM {

  protected $table_name = 'backup_queue';
  
  protected $belongs_to = array('site', 'user');
  
  protected $_files = array();
  
  protected $_data = array();
  
  protected $expires_offset = '+10 days';
  
  public function queue_backup($site_id, $user_id) {
    $this->site_id = $site_id;
    $this->user_id = $user_id;
    $this->status = 0;
    $this->created = date::to_db();
    $this->updated = date::to_db();
    return $this->save();
  }
  
  public function is_expired($today = NULL) {
    $today = is_null($today) ? date::to_db() : $today;
    return $today > date::modify($this->expires_offset, $this->created) ? TRUE : FALSE;
  }
  
  /**
   * Get an unprocessed message from the queue.
   *
   * Because InnoDB doesn't have atomic operations we must run
   * an update query to specify that we are grabbing a record that
   * has yet to be processed or claimed and set a timeout of 10 minutes.
   * If the item is still incomplete after 10 minutes, another worker
   * may pick it up and begin processing.
   */
  public function get_from_queue() {
    $worker_id = text::random('alnum', 16);
    $query = $this->db->query("UPDATE backup_queue SET worker_id = ?, worker_timeout = ? WHERE status = 0 AND (worker_id IS NULL OR worker_timeout < ?) ORDER BY created ASC LIMIT 1", 
      array($worker_id, date::to_db('+10 minutes'), date::to_db()));
    if ($query->count()) {
      return $this->where('status', 0)->where('worker_id', $worker_id)->find();
    }
    return FALSE;
  }
  
  /**
   * Process the backup.
   */
  public function process() {
    $site_id = kohana::config('chapterboard.site_id');
    kohana::config_set('chapterboard.site_id', $this->site_id);
    $this->filename = sprintf('%s-%s.zip', strtolower(preg_replace('/[^\d\w:]/i', '-', $this->site->name())), date::display('now', 'Y-m-d'));
    $this->create_archive();
    $this->updated = date::to_db();
    $this->status = 1;
    $this->save();
    $this->notify();
    kohana::config_set('chapterboard.site_id', $site_id);
  }
  
  public function notify() {
    $vars = array(
      'download_link' => url::file('file/backups/'. $this->filename),
      'expires' => date::modify($this->expires_offset, $this->created),
    );
    email::notify($this->user->email, 'backup_ready', $vars);
  }
  
  protected function create_archive() {
    $destination = APPPATH .'files/backups/'. $this->filename;

    $archive = new ZipArchive();
    if ($archive->open($destination, ZIPARCHIVE::OVERWRITE) !== true) {
      return false;
    }
    
    $this->_bundle_files();
    if (is_array($this->_files) && ! empty($this->_files)) {
      foreach ($this->_files as $file => $local_file) {
        $archive->addFile($file, 'backup/'. $local_file);
      }
    }
    unset($this->_files);
    
    $this->_bundle_data();
    if (is_array($this->_data) && ! empty($this->_data)) {
      foreach ($this->_data as $object => $data) {
        $archive->addFromString('backup/'. $object .'.xml', $data);
      }
    }
    unset($this->_data);
    
    log::system('chapter_backup', sprintf('The zip archive %s contains %s files with a status of %s.', $destination, $archive->numFiles, $archive->getStatusString()));

    $archive->close();
    return file_exists($destination) ? TRUE : FALSE;
  }
  
  protected function _bundle_data() {
    $objects = array(
      'albums' => array(
        'has_many' => array(
          'files' => array(
            'has_many' => array('comments'),
          )
        ),
      ),
      'announcements' => array(),
      'sms' => array(),
      'campaigns' => array(
        'has_many' => array('campaign_donations'),
      ),
      'budget_categories' => array(),
      'budgets' => array(
        'has_many' => array('budget_expected', 'budget_transactions'),
      ),
      'courses' => array(
        'has_many' => array(
          'comments' => array(
            'has_many' => array('files'),
          ),
        ),
      ),
      'folders' => array(
        'has_many' => array('files'),
      ),
      'finance_charges' => array(
        'has_many' => array('finance_charge_members')
      ),
      'finance_payments' => array(),
      'forums' => array(
        'has_many' => array(
          'topics' => array(
            'has_many' => array(
              'votes',
              'files',
              'comments' => array(
                'has_many' => 'files',
              ),
            ),
            'with' => array(
              'poll' => array(
                'has_many' => array(
                  'poll_choices' => array(
                    'has_many' => array('poll_votes'),
                  ),
                ),
              ),
            ),
          ),
        ),
      ),
      'calendars' => array(
        'has_many' => array(
          'events' => array(
            'has_many' => array(
              'comments' => array(
                'has_many' => array('files')
              ),
            ),
          ),
        ),
      ),
      'recruits' => array(
        'has_many' => array('comments', 'votes'),
      ),
      'service_events' => array(
        'has_many' => array('service_hours')
      ),
      'users' => array(
        'has_many' => array('groups', 'roles'),
        'with' => array('profile')
      ),
    );
    
    foreach ($objects as $object => $relationships) {
      $results = ORM::factory(inflector::singular($object))->where('site_id', $this->site->id)->find_all();
      foreach ($results as $result) {
        $this->_data[$object] .= $result->to_xml($relationships);
      }
    }
  }
  
  protected function _bundle_files() {
    $assets = ORM::factory('file')->where('site_id', $this->site->id)->find_all();
    foreach ($assets as $asset) {
      $filepath = APPPATH .'files/original/'. $asset->filename;
      if (file_exists($filepath)) {
        $this->_files[$filepath] = 'files/'. $asset->object_type .'/'. $asset->filename;
      }
    }
    log::system('chapter_backup', sprintf('Files bundled: %s.', count($this->_files)));
  }

}