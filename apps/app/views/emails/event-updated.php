Reply ABOVE THIS LINE to add a comment to this event

Details for an event that you signed up to attend have been changed.  The new information is shown below:

Title: <?= $vars->title ?>


Date: <?= $vars->show_date(); ?>
<?php if ($vars->location): ?>


Location: <?= $vars->location; ?>
<?php endif ?>


Details: <?= $vars->body ? $vars->body : 'No details provided.' ?>


Posted by: <?= $vars->user->name(); ?>


To view further details or follow up with a comment visit: <?= url::site('calendar/event/'. $vars->id) ?>