<?= message::get() ?>

<div class="heading clearfix">
  <h2 class="title"><?= $this->title ?></h2>
</div>

<div class="clearfix">
  <div class="split-left">
    <div class="body-text">
      <p>ChapterBoard offers two services which require you to provide us with your bank account information.  Read more about each of the services below:</p>
      <br />
      <div class="clearfix">
        <h3 class="title">Online Fundraising</h3>
        <p>Wouldn't it be great to raise money for your chapter from your campus, parents and alumni?  We thought so too, that's why we put together some really great tools to help you create and track your fundraising campaigns.  Best of all, you can easily promote your campaigns through Facebook and Twitter. <a href="#TB_inline?inlineId=more-fundraising" class="thickbox">More&hellip;</a></p>
      </div>
      <br />
      <div class="clearfix">
        <h3 class="title">Online Dues Collection</h3>
        <p>Do you want financial services that do more than just collect your money? Wouldn't it be nice to see in real-time a list of delinquents? Wouldn't it be nice to keep more of the money you collect? With ChapterBoard Online Collections you can do all of this and more. <a href="#TB_inline?inlineId=more-collections" class="thickbox">More&hellip;</a></p>
      </div>
      
      <div class="footnotes clearfix">
        <p>Questions? Give us a call at (949) 525-4432, or shoot us an <a href="mailto:team@chapterboard.com">email</a>. We're happy to talk about how ChapterBoard Online Fundraising and Dues Collections work and also answer any questions you have about online collections in general.</p>
      </div>
      
      <div id="more-fundraising" class="hidden">
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
      <div id="more-collections" class="hidden">
        <h2 class="title">Online Dues Collection</h2>
        <h4 class="title">What you can do:</h4>
        <ul>
          <li>Assess dues and fees online, set up payment plans, assess dues to certain "groups" of your chapter (pledges, etc.)</li>
          <li>Create budgets to organize where your money is spent</li>
          <li>When payments are made, view a real-time income report that is also integrated into your budgets (money collected for specific school periods, extra fees, etc.)</li>
          <li>View real-time delinquency reports to see who has and hasn't paid</li>
          <li>Send out payment reminders via email and text message to delinquent members</li>
          <li>Save your chapter money and be a hero!</li>
        </ul>

        <h4 class="title">What we do:</h4>
        <ul>
          <li>ChapterBoard charges a flat rate on all money collected online (<?= $this->site->fee_credit() ?> for credit card and <?= $this->site->fee_echeck() ?> for online checks)</li>
            <blockquote><em>Example:</em> Bill pays his $100 dues online with a credit card. ChapterBoard keeps <?= money::display($this->site->fee_credit) ?> and deposits <?= money::display(100 - $this->site->fee_credit) ?> into your chapter's bank account. Bill and his chapter are happy.</blockquote>
          <li>ChapterBoard makes bi-weekly deposits into your chapter's bank account.</li>
          <li>ChapterBoard integrates your budgets and collections so you can see what you have, or don't have, effortlessly</li>
          <li>ChapterBoard is here, round the clock, if you have questions about payments, deposit requests, or just need a different perspective on life.</li>
        </ul>

        <h4 class="title">What we NEVER do:</h4>
        <ul>
          <li>Charge additional fees. We don't charge an annual fee, usage fee, per member rate based on the size of your chapter, or even a fee for spilling your beer (although there really ought to be).</li>
          <li>Ignore your phone calls and emails. Seriously, everyone here at ChapterBoard lives and breathes this company every day. We love what we do and we love talking to our users.</li>
          <li>Require long term contracts. Honestly, we don't. You can use ChapterBoard as much or as little as you want. And when you're done, or just want to try something else for a while, we will never charge you a cancellation or early termination fee. But we will definitely bet that you'll be back :)</li>
        </ul>
      </div>
    </div>
  </div>
  <div class="split-right">
    
    
    <?php if ($this->site->bank_on_file()): ?>
      <div id="bank-add-service" class="block clearfix">
        <h3 class="title">Services</h3>
        <?= form::open('finances/banking/service') ?>
        <table>
          <tr>
            <td>Online Fundraising</td>
            <td>
              <?php if ($this->site->fundraising_enabled()): ?>
                <?= html::image('/minis/accept.png') ?>
              <?php else: ?>
                <?= form::checkbox('fundraising_enabled', TRUE) ?>
              <?php endif ?>
            </td>
          </tr>
          <tr>
            <td class="col-1">Online Dues Collection</td>
            <td>
              <?php if ($this->site->collections_enabled()): ?>
                <?= html::image('/minis/accept.png') ?>
              <?php else: ?>
                <?= form::checkbox('collections_enabled', TRUE) ?>
              <?php endif ?>
            </td>
          </tr>
          <?php if ( ! ($this->site->collections_enabled() && $this->site->fundraising_enabled())): ?>
            <tr><td colspan="2" class="no-border"><div class="right"><?= form::submit('signup', 'Signup') ?></div></td></tr>
          <?php endif ?>
        </table>
        
        <h3 class="title">Account Information</h3>
        <table>
          <tr>
            <td class="col-1">Deposit Schedule:</td>
            <td>Bi-Weekly</td>
          </tr>
          <tr>
            <td>Credit Card Fee:</td>
            <td><?= $this->site->fee_credit() ?> <span class="form-tip" title="Credit Card fee is only assessed when your chapter collects a payment via credit card."><?= html::image('minis/information.png'); ?></span></td>
          </tr>
          <tr>
            <td>E-Check Fee:</td>
            <td><?= $this->site->fee_echeck() ?> <span class="form-tip" title="E-Check fee is only assessed when your chapter collects a payment via electronic check."><?= html::image('minis/information.png'); ?></span></td>
          </tr>
          <?php if ($this->site->deposit_accounts()->count()): ?>
            <?php foreach ($this->site->deposit_accounts() as $account): ?>
              <tr>
                <td colspan="2">
                  <b><?= $account->name ?></b><br />
                  <small>
                    <?= $account->bank_name() ?> &mdash;
                    <?= html::anchor('finances/banking/edit/'. $account->id, 'edit name') ?>
                  </small>
                </td>
              </tr>
            <?php endforeach ?>
            <tr>
              <td class="no-border">
                <div class=""><small><?= html::anchor('finances/banking/add', 'Add New Account +') ?></small></div>
              </td>
            </tr>
          <?php endif ?>
        </table>
      </div>
    <?php else: ?>    
      <div id="bank-signup" class="block clearfix">
        <?= form::open(); ?>
          <h3 class="title">Services</h3>
          <table>
            <tr>
              <td class="checkbox" colspan="2"><label><?= form::checkbox('fundraising_enabled', TRUE) ?> Online Fundraising</label></td>
            </tr>
            <tr>
              <td class="checkbox" colspan="2"><label><?= form::checkbox('collections_enabled', TRUE) ?> Online Dues Collection</label></td>
            </tr>
          </table>
          <h3 class="title">Bank Account</h3>
          <table>
            <tr>
              <td><?= form::label('bank_name', 'Bank Name:*')?></td>
              <td>
                <?= form::input('bank_name', $this->form['bank_name'], 'class="medium"') ?>
                <span class="error"><?= $this->errors['bank_name'] ?></span>
              </td>
            </tr>
            <tr>
              <td><?= form::label('name', 'Account Name:*')?></td>
              <td>
                <?= form::input('name', $this->form['name']) ?>
                <span class="form-tip" title="This is mainly used for chapters with multiple bank accounts. When you assess dues or create fundraising campaigns, you can select which of your bank accounts the money will be deposited into. Give the account a descriptive name so that you know which account your money will be deposited into."><?= html::image('minis/information.png'); ?></span>
                <div class="error"><?= $this->errors['name'] ?></div>
              </td>
            </tr>
            <tr>
              <td><?= form::label('routing_number', 'Routing Number:*')?></td>
              <td>
                <?= form::input('routing_number', $this->form['routing_number'], 'class="small"') ?>
                <span class="form-tip"><?= html::thickbox_anchor('images/check.gif', html::image('minis/information.png')) ?></span>
                <span class="error"><?= $this->errors['routing_number'] ?></span>
              </td>
            </tr>
            <tr>
              <td><?= form::label('account_number', 'Account Number:*')?></td>
              <td>
                <?= form::input('account_number', $this->form['account_number'], 'class="small"') ?>
                <span class="error"><?= $this->errors['account_number'] ?></span>
              </td>
            </tr>
            <tr><td colspan="2" class="no-border"><div class="right"><?= form::submit('signup', 'Signup') ?></div></td></tr>
          </table>
        <?= form::close() ?>
      </div>
    <?php endif ?>
  </div>
</div>