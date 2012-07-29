<div class="heading clearfix">
  <span class="right header-span"><a href="#TB_inline?width=600&amp;inlineId=donations-info" class="thickbox">Info</a></span>
  <h2 class="title"><?= $this->title ?></h2>
  <?php if ($this->site->fundraising_enabled()): ?>
    <ul>
      <li><span><?= html::anchor('finances/fundraising/add', 'Create New Campaign') ?></span></li>
    </ul>
  <?php endif ?>
</div>

<?= message::get(); ?>

<div class="clearfix">
  <table>
    <thead>
      <tr>
        <th>Campaign</th>
        <th class="date right">End Date</th>
        <th class="amount right">Goal</th>
        <th class="amount right">Amount Raised</th>
      </tr>
    </thead>
    <?php if ($this->site->fundraising_enabled()): ?>
      <?php if ($this->campaigns->count()): ?>
        <?php foreach ($this->campaigns as $campaign): ?>
          <tr class="hoverable">
            <td>
              <strong><?= html::anchor($campaign->url(), $campaign->title, array('target' => '_blank')) ?></strong>
              <span class="action-links right">
                <?= html::anchor('finances/fundraising/show/'. $campaign->id, 'details') ?> · 
                <?= html::anchor('finances/fundraising/edit/'. $campaign->id, 'edit') ?>
              </span>
            </td>
            <td class="right">
              <?php if ($campaign->expires): ?>
                <?= date::display($campaign->expires, 'm/d/Y', FALSE) ?>
              <?php endif ?>
            </td>
            <td class="right">
              <?php if ($campaign->goal): ?>
                <?= money::display($campaign->goal) ?>
              <?php endif ?>
            </td>
            <td class="right"><?= money::display($campaign->campaign_total) ?></td>
          </tr>
        <?php endforeach ?></tbody>
      <?php else: ?>
        <tr>
          <td colspan="4"><p><?= html::anchor('finances/fundraising/add', 'Create your first fundraising campaign') ?> and start raising money!</p></td>
        </tr>
      <?php endif ?>
    <?php else: ?>
      <tr>
        <td colspan="4">In order to create your first campaign you need to <?= html::anchor('finances/banking', 'enter your chapter bank account information') ?>. Once your account is linked we'll be able to deposit the money you raise online directly into your chapter bank account.</td>
      </tr>
    <?php endif ?>
  </table>
</div>

<div id="donations-info" class="hidden">
  <h2 class="title">Online Fundraising</h2>
  <h4 class="title">What you can do:</h4>
  <ul>
    <li>Create campaigns to collect money for just about anything: fundraising events, ticket sales, event t-shirts, house renovations, and general alumni donations.</li>
    <li>Easily share your campaign with everyone you know. We provide you with an unique URL that's easy to share in emails, text messages, tweets and wall posts so anyone (students, alumni, friends, even your mom) can see your campaign and participate.</li>
    <li>Track all your campaigns and export all your donor information for follow ups</li>
  </ul>
  <h4 class="title">What we do:</h4>
  <ul>
    <li>ChapterBoard charges a flat rate on all money collected online (<?= $this->site->fee_credit() ?> for credit card and <?= $this->site->fee_echeck() ?> for online checks)</li>
      <blockquote><em>Example:</em> Alumnnus Bill contributes $100 using your ChapterBoard online fundraising campaign form and pays via credit card. ChapterBoard keeps <?= money::display($this->site->fee_credit) ?> and deposits <?= money::display(100 - $this->site->fee_credit) ?> into your chapter's bank account.</blockquote>
    <li>ChapterBoard makes bi-weekly deposits into your chapter's bank account.</li>
    <li>ChapterBoard is here, round the clock, if you have questions about payments, deposit requests, or just need a different perspective on life.</li>
  </ul>

  <h4 class="title">What we NEVER do:</h4>
  <ul>
    <li>Charge additional fees. We don't charge an annual fee, usage fee, per member rate based on the size of your chapter, or even a fee for spilling your beer (although there really ought to be).</li>
    <li>Ignore your phone calls and emails. Seriously, everyone here at ChapterBoard lives and breathes this company every day. We love what we do and we love talking to our users.</li>
    <li>Require long term contracts. Honestly, we don't. You can use ChapterBoard as much or as little as you want. And when you're done, or just want to try something else for a while, we will never charge you a cancellation or early termination fee. But we will definitely bet that you'll be back :)</li>
  </ul>
</div>