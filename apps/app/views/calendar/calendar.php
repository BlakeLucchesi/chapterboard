<?= $this->user->help('calendar'); ?>

<?php 

// Get the day names
$days = Calendar::days();

// Previous and next month timestamps
$next = mktime(0, 0, 0, $month + 1, 1, $year);
$prev = mktime(0, 0, 0, $month - 1, 1, $year);

// Import the GET query array locally and remove the day
$qs = $_GET;
unset($qs['day']);

// Previous and next month query URIs
$prev = Router::$current_uri.'?'.http_build_query(array_merge($qs, array('month' => date('n', $prev), 'year' => date('Y', $prev))));
$next = Router::$current_uri.'?'.http_build_query(array_merge($qs, array('month' => date('n', $next), 'year' => date('Y', $next))));

?>

<div class="heading clearfix">
  <h2><?= strftime('%B %Y', mktime(0, 0, 0, $month, 1, $year)) ?></h2>
  <ul>
    <li><?= html::anchor($prev, '&laquo; '. strftime('%B', mktime(0, 0, 0, $month - 1, 1, $year))) ?></li>
    <li><?= html::anchor($next, strftime('%B', mktime(0, 0, 0, $month + 1, 1, $year)) .' &raquo;') ?></li>
    <li><?= html::anchor('calendar', 'Today') ?></li>
  </ul>
  <div class="right no-print">
    <span class="ical-link"><?= html::thickbox_anchor('calendar/feedurl', 'iCal Feed') ?></span>
    <span class="print-link"><a href="javascript:window.print();">Printer Friendly</a></span>
  </div>
</div>

<?= message::get(); ?>

<table class="calendar">
  <tr>
    <?php foreach ($days as $day): ?>
      <th><?= $day ?></th>
    <?php endforeach ?>
  </tr>
  <?php foreach ($weeks as $week): ?>
    <tr>
      <?php foreach ($week as $day):
        list ($number, $current, $data) = $day;
        if (is_array($data)) {
          $classes = $data['classes'];
          $output = empty($data['output']) ? '' : '<div class="event">'. implode('</div><div class="event">', $data['output']) .'</div>';
        }
        else {
          $classes = array();
          $output = '';
        }
      ?>
        <td class="day-box <?= implode(' ', $classes) ?>">
          <div class="day"><?= in_array('today', $classes) ? '<b>Today</b>' : ''; ?> <?= $day[0] ?></div>
          <?= $output ?>
        </td>
      <?php endforeach ?>
    </tr>
  <?php endforeach ?>
</table>
