<div class="heading clearfix">
  <h2><?= $this->title ?></h2>
</div>

<div class="clearfix">
  <div class="split-left">
    <fieldset>
      <?= form::open() ?>
      <h4>Phase I</h4>
      <p>We hereby assign accounts from this date forward (as we select and submit) to Parson-Bishop for
      collection. Parson-Bishop may proceed with whatever steps are necessary for collection of these accounts. We warrant to Parson-Bishop
      the accuracy of the information furnished to them on accounts submitted.</p>

      <p>The Preferred Plan will make several collection contacts, with each account submitted, for a period of
      approximately 45 days. Many accounts do not need extensive collection action for collection and all accounts
      collected in Phase I will have a low contingency fee of only 18% (balances under $100 have a fee if collected of
      35%). These fees are due on all amounts actually collected or paid direct while in Phase I. No collection, no fee.
      All fees are subject to reasonable increase over time.</p>

      <h4>Phase II and III (Final Stage Service)</h4>

      <p>Some accounts will need more extensive and time-consuming follow up to achieve collection. We agree that
      accounts that do not respond in Phase I will be transferred to Phase II, Final Stage collection service at a
      contingency fee of 35% of all amounts actually collected, paid direct to the client or withdrawn after assignment
      into Final Stage. Parson-Bishop’s collection staff will work all accounts in this phase for an unlimited time. Phase III: once
      Parson-Bishop determines that accounts cannot be collected in Phase I-II, we give them permission to report all unpaid
      accounts to national credit reporting agencies for a period of time up to seven years and to transfer selected
      accounts to their legal department for additional follow-up by their legal collection staff and, if necessary,
      attorney members of the Commercial Law League of America. No suit action will ever be taken without our
      specific knowledge and approval. Fees for legal and credit reporting are 50% if collected.</p>
      <br />
      <div class="clearfix">
        <?= form::label('address', 'Address:*')?>
        <?= form::input('address', $this->form['address']) ?>
        <span class="error"><?= $this->errors['address'] ?></span>
      </div>
      <div class="clearfix">
        <?= form::label('city', 'City, State, Zip:*')?>
        <?= form::input('city', $this->form['city']) ?>
        <span class="error"><?= $this->errors['city'] ?></span>
      </div>
      
      <p>*For chapter use we recommend using the chapter address for future continuity. For House Corporations we
      recommend using the address of the officer. Reports and collection remittances are sent to the address listed.</p>
      <br />
      <div class="clearfix">
        <?= form::label('field', 'Officer Signature:')?>
        <?= form::input('field', $this->form['field'], 'class="medium"') ?>
        <div><em>Enter your name as a digital signature.</em></div>
        <div class="error"><?= $this->errors['field'] ?></div>
      </div>
      
      <div class="right"><?= form::submit('submit', 'Sign Up'); ?></div>
      <?= form::close() ?>
    </fieldset>
  </div>
  
  <div class="split-right">
    <?= $this->sidebar ?>
  </div>
</div>