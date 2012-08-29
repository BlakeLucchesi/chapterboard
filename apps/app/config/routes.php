<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * @package  Core
 *
 * Sets the default route to "welcome"
 */
$config['_default']                   = 'dashboard/index';

# Login
$config['login']                      = 'login/login';
$config['logout']                     = 'login/logout';
$config['reset']                      = 'login/reset';
$config['register/([0-9a-zA-Z]+)']    = 'login/register/$1';
$config['setup']                      = 'login/setup';

# Dashboard
$config['messages']                   = 'messages/inbox';
$config['messages/([0-9]+)']          = 'messages/show/$1';

# Profile
$config['profile']                    = 'profile/show';
$config['profile/([0-9]+)']           = 'profile/show/$1';

# Forum 
$config['forum/([0-9]+)']             = 'forum/show/$1';
$config['forum/topic/([0-9]+)']       = 'forum/topic/show/$1';

# Calendar
$config['calendar']                   = 'calendar';
$config['calendar/add']               = 'calendar/event/add';
$config['calendar/event/([0-9]+)']    = 'calendar/event/show/$1';
$config['calendar/edit/([0-9]+)']     = 'calendar/event/edit/$1';
$config['calendar/admin']             = 'calendar/admin/index';
$config['calendar/signups']           = 'calendar/signups/index';
$config['ical/([a-zA-Z0-9]*)']        = 'ical/index/$1';

# Finances
$config['finances/charges/([0-9]+)']         = 'finances/charges/show/$1';
$config['finances/members/([0-9]+)']         = 'finances/members/show/$1';
$config['finances/deposits/([0-9]+)']        = 'finances/deposits/show/$1';
$config['finances/members/([0-9]+)/history'] = 'finances/members/history/$1';
$config['finances/collections/past-due']     = 'finances/collections/past_due';

# Budgets
$config['budgets/([0-9]+)']               = 'budgets/show/$1';
$config['budgets/transactions/([0-9]+)']  = 'budgets/transactions/show/$1';

# Service
$config['service/events/([0-9]+)']     = 'service/events/show/$1';
$config['service/members/([a-zA-Z]+)'] = 'service/members/index/$1';
$config['service/members/([0-9]+)']    = 'service/members/show/$1';

# Recruitment
$config['recruitment/(active|bidded|not\-recruiting)'] = 'recruitment/index/$1';

# Members
$config['members/groups/([0-9]+)'] = 'members/groups/show/$1';

# Files
$config['files/study/department/([0-9]+)'] = 'files/study/department/show/$1';
$config['files/study/course/([0-9]+)']     = 'files/study/course/show/$1';
$config['files/photos/album/([0-9]+)']     = 'files/photos/album/show/$1';

# Chapters National Routes
$config['chapters/([0-9]+)']          = 'chapters/show/$1';
$config['chapters/([0-9]+)/([^\/]+)'] = 'chapters/show/$1/$2';
$config['service/members/([0-9]+)']   = 'service/members/show/$1';
$config['files/folder/([0-9]+)']      = 'files/folder/index/$1';

# Files and image thumbnails
$config['file/original/(.*)']         = 'file/index/$1';
$config['file/backups/(.*)']          = 'file/backups/$1';
$config['file/([a-zA-Z0-9-_]+)/(.*)'] = 'file/thumb/$1/$2';

# Administration
$config['feedback']                   = 'support/feedback';

$config['tests']                      = 'unit_test/index';

$config['share']                      = 'content/share';

# Cron Tasks
$config['cron/([a-zA-Z_-]+)']         = 'cron/index/$1';
