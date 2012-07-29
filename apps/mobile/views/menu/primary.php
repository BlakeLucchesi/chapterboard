<ul>
  <li><?= html::anchor('', 'Home') ?></li>
  <li><?= html::anchor('forum', 'Discuss') ?></li>
  <li><?= html::anchor('events', 'Events') ?></li>
  <li><?= html::anchor('recruitment', 'Recruits', array('class' => Router::$controller == 'recruitment' ? 'active' : '')) ?></li>
  <li><?= html::anchor('members', 'Members', array('class' => Router::$controller == 'members' ? 'active' : '')) ?></li>
</ul>