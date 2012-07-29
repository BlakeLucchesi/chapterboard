<div class="heading clearfix">
  <h2><?= $this->title ?></h2>
</div>

<table>
  <thead>
    <tr>
      <th></th>
      <th>Name</th>
      <th>Chapter</th>
      <th class="right <?= $_GET['sort'] == 'dollars' ? '' : 'headerSortUp' ?>"><?= html::anchor('service/members?sort=hours', 'Hours') ?></th>
      <th class="right <?= $_GET['sort'] == 'dollars' ? 'headerSortUp' : '' ?>"><?= html::anchor('service/members?sort=dollars', 'Dollars') ?></th>
    </tr>
  </thead>
  <?php if ($this->members->count()): ?>
    <?php foreach ($this->members as $member): ?>
      <tr>
        <td><?= ++$i ?></td>
        <td><?= html::anchor('profile/'. $member->user->id, $member->user->name()) ?></td>
        <td><?= $member->user->site->chapter_name() ?></td>
        <td class="right"><?= number_format($member->hours) ?></td>
        <td class="right"><?= money::display($member->dollars) ?></td>
      </tr>
    <?php endforeach ?>
  <?php else: ?>
    <tr>
      <td colspan="4">No members found.</td>
    </tr>
  <?php endif ?>
</table>