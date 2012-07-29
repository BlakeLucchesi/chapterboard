<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Classic pagination style
 * 
 * @preview  ‹ First  < 1 2 3 >  Last ›
 */
?>

<p class="pagination">

  <span class="current">Page: <?= $current_page ?></span>

	<?php if ($previous_page): ?>
		<a href="<?php echo str_replace('{page}', $previous_page, $url) ?>">&lt; prev</a>
	<?php endif ?>

	<?php if ($next_page): ?>
		<a href="<?php echo str_replace('{page}', $next_page, $url) ?>">next &gt;</a>
	<?php endif ?>
	
</p>