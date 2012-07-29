<div class="heading clearfix">
  <h2><?= $this->title ?></h2>
</div>

<div class="clearfix">
  <div class="split-left clearfix">
    <table>
      <thead>
        <tr>
          <th>Member Name</th>
          <th class="amount right">Past Due Amount</th>
        </tr>
      </thead>
      <?php foreach ($this->members as $member): ?>
        <?php if ($member->overdue_amount > 0): ?>
          <tr class="hoverable">
            <td><?= html::anchor('finances/members/'. $member->user_id, $member->name) ?></td>
            <td class="right"><?= money::display($member->overdue_amount) ?></td>
          </tr>
          <?php $members_count++; ?>
        <?php endif ?>
      <?php endforeach ?>
      <?php if ( ! $members_count): ?>
        <tr>
          <td colspan="2">You have no members with balances over 90 days past due.</td>
        </tr>
      <?php endif ?>
    </table>
  </div>
  <div class="split-right">
    <div class="block no-print">
      <div class="help clearfix">
        <p>Helpful text that lets you know how to work this page.</p>
        <p><div class="right"><a href="#TB_inline?width=600&amp;inlineId=debt-collection-help" class="thickbox">Read more&hellip;</a></div></p>
      </div>
    </div>
    <?= $this->sidebar; ?>
  </div>
</div>

<div id="debt-collection-help" class="help hidden no-print">
  <p>The members shown below have balances that are over 90 days past due. Click on the member's name to view a full summary of their account.  We have partnered with Parson-Bishop to help you collect the amounts due to your chapter.</p>
</div>