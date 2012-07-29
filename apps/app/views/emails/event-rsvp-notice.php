A new event requiring an RSVP has been added to your calendar.


<?= $vars->title ?>


Added by: <?= $vars->user->name() ?>


<?= html::anchor('calendar/event/'. $vars->id, 'RSVP for this event'); ?>


You can change your calendar email notifications by logging into your ChapterBoard account and clicking on "Calendar" and then "Notifications".