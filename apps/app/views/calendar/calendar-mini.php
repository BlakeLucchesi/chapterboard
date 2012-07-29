<?php defined('SYSPATH') OR die('No direct access allowed.');
  // Get the day names
  $days = Calendar::days(3);
?>
<h3><?= strftime('%B %Y', mktime(0, 0, 0, $month, 1, $year)) ?></h3>
<table class="calendar calendar-mini">
  <tr>
    <?php foreach ($days as $day): ?>
      <th><?php echo $day ?></th>
    <?php endforeach ?>
  </tr>
  <?php foreach ($weeks as $week): ?>
    <tr>
      <?php foreach ($week as $day):
        list ($number, $current, $data) = $day;
        if (is_array($data)) {
          $classes = $data['classes'];
          $output = empty($data['output']) ? '' : '<div>'. implode('</div><div>', $data['output']) .'</div>';
        }
        else {
          $classes = array();
          $output = '';
        }
      ?>
        <td class="day-box <?php echo implode(' ', $classes) ?>">
          <?php echo $day[0] ?>
        </td>
      <?php endforeach ?>
    </tr>
  <?php endforeach ?>
</table>
