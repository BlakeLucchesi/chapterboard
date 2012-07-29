<div id="breadcrumbs">
  <?= html::anchor('finances/fundraising', 'Fundraising') ?> &raquo; <?= $this->title ?>
</div>

<div class="heading clearfix">
  <h2 class="title"><?= $this->title ?></h2>
  <ul>
    <li><span><?= html::anchor('finances/fundraising/receive?campaign_id='. $this->campaign->id, 'Record In-Person Payment') ?></span></li>
  </ul>
  <div class="right no-print">
    <span class="excel-link"><?= html::anchor('finances/fundraising/export/'. $this->campaign->id, 'Export Campaign Transactions') ?></span>
  </div>
</div>

<table class="sort">
  <thead>
    <tr>
      <th>Name</th>
      <th class="amount">Date</th>
      <th class="amount">Method</th>
      <th>Item</th>
      <th class="amount right">Amount</th>
      <th class="amount right">Deposited</th>
    </tr>
  </thead>
  <?php if ($this->donations->count()): ?>
    <?php foreach ($this->donations as $donation): ?>
      <tr class="hoverable">
        <td class="title">
          <?= $donation->name() ?>
          <?php if ($donation->note): ?>
            <?php $token = text::random() ?>
            <div id="<?= $token ?>" class="hidden"><div><?= $donation->note ?></div></div>
            &mdash; <a href="#TB_inline?inlineId=<?= $token ?>" class="thickbox">view note</a>
          <?php endif ?>
        </td>
        <td><?= date::display($donation->created, 'm/d/Y') ?></td>
        <td><?= $donation->payment_type() ?></td>
        <td><?= $donation->item_label ?></td>
        <td class="right"><?= money::display($donation->amount) ?></td>
        <td class="right"><?= money::display($donation->amount_payable) ?></td>
      </tr>
    <?php endforeach ?>
  <?php else: ?>
    <tr>
      <td colspan="6">There have been no donations through this form yet.  Start promoting your donation form using the share links on the <?= html::anchor($this->campaign->url(), 'donation page', array('target' => '_blank')) ?>.</td>
    </tr>
  <?php endif ?>
</table>