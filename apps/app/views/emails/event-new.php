Reply ABOVE THIS LINE to add a comment to this event

New Event Added

Title: <?= $vars->title ?>


Date: <?= $vars->show_date(); ?>
<?php if ($vars->location): ?>


Location: <?= $vars->location; ?>
<?php endif ?>


Details: <?= $vars->body ? $vars->body : 'No details provided.' ?>


Posted by: <?= $vars->user->name(); ?>


To read the post and follow up with a comment visit: <?= url::site('calendar/event/'. $vars->id) ?>


You can change your calendar email notifications by logging into your ChapterBoard account and clicking on "Calendar" and then "Notifications".