<h2 class="title"><?= $this->title ?></h2>

<div class="block help">
  <p><strong>Directions:</strong> You may use the following address to access your calendar from other applications such as Apple iCal or Google Calendar. You can copy and paste this into any calendar product that supports the iCal format.</p>
  <pre class="content"><? echo url::base() .'ical/'. $this->user->calendar_token() ?></pre>
  <p><strong>WARNING:</strong> This address allows unauthenticated access to your calendar.  Make sure you do not share it with other individuals. If you believe the privacy of your calendar url has been compromised <?= html::thickbox_anchor('calendar/feed_reset', 'click here to reset it') ?>.</p>
</div>

<!-- <div class="block">
<h3>Additional Help and Resources</h3>
<ul>
  <li><?= html::thickbox_anchor('help/calendar/ical/iphone', 'Syncing ChapterBoard calendars with your iPhone') ?></li>
  <li><?= html::thickbox_anchor('help/calendar/ical/blackberry', 'Syncing ChapterBoard calendars with your BlackBerry') ?></li>
</ul>
</div> -->