<?php foreach ($this->sms->sms_log as $log): ?>
  <div><?= $log->user->name(TRUE) ?> - <?= $log->user->site->name() ?></div>
<?php endforeach ?>