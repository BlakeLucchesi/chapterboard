<div id="finances">
  <div class="heading clearfix">
    <h2><?= $this->title ?></h2>
  </div>

  <?= message::get() ?>


  <?php if ($this->charge->id): ?>
    <div class="help">
      <p><b>NOTE:</b> When editing a charge, changes will only affect members who <b>have not</b> made a payment on this charge.  If you wish to edit a member who has made a payment (members with an X next to their name), you must click on "cancel" and use the edit icon that shows up when you hover over their name. Also note, you cannot edit a member's charge if they have paid the complete balance.</p>
    </div>    
  <?php endif ?>
  
  <?= form::open() ?>
    <div id="finance-charge-form" class="clearfix">
      <div class="split-left">
        <fieldset>
          <h3>1. Select Members</h3>
          <div class="block">
            <table class="select">
              <thead>
                <tr>
                  <th class="table-select {sorter: false}"><?= form::checkbox() ?></th>
                  <th>Actives</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($this->actives as $member): ?>
                  <tr class="hoverable">
                    <td>
                      <?php if ( ! $this->members_with_payments[$member->id]): ?>
                        <?= form::checkbox('members['. $member->id .']', 1, $this->form['members'][$member->id]) ?>
                      <?php else: ?>
                        &nbsp;X
                      <?php endif ?>
                    </td>
                    <td>
                      <?= $member->name() ?>
                    </td>
                  </tr>
                <?php endforeach ?>
              </tbody>
            </table>
            
            <table class="sort select">
              <thead>
                <tr>
                  <th class="table-select {sorter: false}"><?= form::checkbox() ?></th>
                  <th>New Members</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($this->pledges as $member): ?>
                  <tr class="hoverable">
                    <td>
                      <?php if ( ! $this->members_with_payments[$member->id]): ?>
                        <?= form::checkbox('members['. $member->id .']', 1, $this->form['members'][$member->id]) ?>
                      <?php else: ?>
                        &nbsp;X
                      <?php endif ?>
                    </td>
                    <td><?= $member->name() ?></td>
                  </tr>
                <?php endforeach ?>
              </tbody>
            </table>
            
            <table class="sort select">
              <thead>
                <tr>
                  <th class="table-select {sorter: false}"><?= form::checkbox() ?></th>
                  <th>Alumni</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($this->alumni as $member): ?>
                  <tr class="hoverable">
                    <td>
                      <?php if ( ! $this->members_with_payments[$member->id]): ?>
                        <?= form::checkbox('members['. $member->id .']', 1, $this->form['members'][$member->id]) ?>
                      <?php else: ?>
                        &nbsp;X
                      <?php endif ?>
                    </td>
                    <td><?= $member->name() ?></td>
                  </tr>
                <?php endforeach ?>
              </tbody>
            </table>
            
          </div>
        </fieldset>
      </div>
      <div class="split-right">
        <fieldset>
          <h3>2. Charge Details</h3>
          <div class="clearfix">
            <?= form::label('title', 'Title:')?>
            <?= form::input('title', $this->form['title']) ?>
            <span class="error"><?= $this->errors['title'] ?></span>
          </div>
          <div class="clearfix">
            <?= form::label('due', 'Due Date:')?>
            <?= form::input('due', $this->form['due'], 'class="date-pick"') ?>
            <span class="error"><?= $this->errors['due'] ?></span>
          </div>
          <div class="clearfix">
            <?= form::label('amount', 'Amount:')?>
            <?= form::input('amount', $this->form['amount'], 'class="amount-input"') ?>
            <span class="error"><?= $this->errors['amount'] ?></span>
          </div>
          <div class="clearfix">
            <?= form::label('budget_id', 'Budget:')?>
            <?= form::dropdown('budget_id', $this->budgets, $this->form['budget_id']) ?>
            <span class="error"><?= $this->errors['budget_id'] ?></span>
          </div>
          <?php if ($this->site->collections_enabled()): ?>
            <div class="clearfix">
              <?= form::label('deposit_account_id', 'Deposit Account:') ?>
              <?= form::dropdown('deposit_account_id', $this->deposit_accounts, $this->form['deposit_account_id']) ?>
              <span class="error"><?= $this->error['deposit_account_id'] ?></span>
            </div>            
          <?php endif ?>

          <br />
          <div id="late-fee-format">
            <h3>3. Automatic Late Fee</h3>
            <p><em>Late fee may be a fixed amount, such as $10.00, or a percentage of the amount that has not been paid.</em></p>
            <div class="clearfix checkbox">
              <label><?= form::radio('late_fee_type', '', $this->form['late_fee_type'] == '' ? TRUE : FALSE) ?> No Late Fee</label>&nbsp; &nbsp;
              <label><?= form::radio('late_fee_type', 'amount', $this->form['late_fee_type'] == 'amount' ? TRUE : FALSE) ?> Fixed Amount</label>&nbsp; &nbsp;
              <label><?= form::radio('late_fee_type', 'percent', $this->form['late_fee_type'] == 'percent' ? TRUE : FALSE) ?> Percent</label>
            </div>        
            <div id="late-fee-amount" class="clearfix">
              <?= form::label('late_fee', 'Amount:')?><span class="dollar-symbol">$</span>
              <?= form::input('late_fee', $this->form['late_fee'], 'class="amount-input"') ?> <span class="percent-symbol">%</span>
              <div class="error"><?= $this->errors['late_fee'] ?></div>
            </div>
          </div>
                  
        </fieldset>
        <div class="right">
          <?php if ($this->charge->id): ?>
              <?= form::submit('submit', 'Save Changes') ?> or <?= html::anchor('finances/charges/'. $this->charge->id, 'Cancel') ?>
          <?php else: ?>
              <?= form::submit('submit', 'Add Charge') ?> or <?= html::anchor('finances/charges', 'Cancel') ?>
          <?php endif ?>
        </div>
      </div>
    </div>
  <?= form::close() ?>
</div>