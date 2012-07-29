<div id="breadcrumbs">
  <?= html::anchor('budgets', 'Budgets') ?> &raquo; <?= $this->budget->name ?>
</div>

<div id="budget">
  <?= form::open() ?>
  <div class="heading clearfix">
    <div class="right no-print">
      <span class="print-link"><a href="javascript:window.print();">Printer Friendly</a></span>
    </div>
    <h2 class="title"><?= $this->title ?></h2>
    <ul>
      <li><?= html::anchor('budgets/edit/'. $this->budget->id, 'Edit Budget Name') ?></li>
      <li><?= html::anchor('budgets/transactions/'. $this->budget->id, 'View Transaction List') ?></li>
    </ul>
  </div>

  <?= message::get(); ?>
  
  <div class="clearfix">
    <table class="block" id="income">
      <thead>
        <tr>
          <th>Income</td>
          <th class="right amount">Expected</td>
          <th class="right amount">Collected</td>
          <th class="right amount">Outstanding</td>
        </tr>
      </thead>
      <tbody>
        <!-- Membership Dues -->
        <tr class="income-separator">
          <td colspan="4"><strong>Membership Dues</strong></td>
        </tr>
        <?php if ($this->budget->finance_charges->count()): ?>
          <?php foreach ($this->budget->finance_charges as $charge): ?>
            <tr class="hoverable">
              <td><?= html::anchor('finances/charges/'. $charge->id, $charge->title) ?></td>
              <td class="right"><?= money::display($charge->expected); ?></td>
              <td class="right"><?= money::display($charge->collected) ?></td>
              <td class="right"><?= money::display($charge->expected - $charge->collected) ?></td>
            </tr>
            <?php $expected += $charge->expected; $collected += $charge->collected; ?>
          <?php endforeach ?>
        <?php else: ?>
          <tr><td colspan="4"><em>** You have not charged any member dues/fees for this budget. Once you <?= html::anchor('finances/charges/add', 'assess a charge') ?> to your members it will be added to your budget. As members make their payments your budget will automatically update.  Use the "Uncharged Dues" field to add future income to your budget (charges you plan on assessing later in the academic period such as initiation fees, etc).</em></td></tr>
        <?php endif ?>
        <tr class="hoverable">
          <td><strong>Uncharged Dues</strong></td>
          <td class="right">$ <?= form::input('uncharged_dues', number_format($this->form['uncharged_dues'], 2),  'class="amount" tabindex="'. $i++ .'"') ?></td>
          <td class="right"></td>
          <td class="right"><span id="expected-dues" class="hidden"><?= $expected; ?></span></td>
        </tr>
        <?php $expected += $this->budget->uncharged_dues; ?>
        <tr class="hoverable">
          <td class="right"><strong>Collection Totals:</strong></td>
          <td class="right" id="expected-collections"><strong><?= money::display($expected) ?></strong></td>
          <td class="right"><strong><?= money::display($collected) ?></strong></td>
          <td class="right"><strong><?= money::display($expected - $collected)  ?></strong></td>
        </tr>
        
        <!-- Additional Income -->
        <tr class="income-separator">
          <td colspan="4"><strong>Additional Income</strong></td>
        </tr>
        <tr class="hoverable">
          <td colspan="2"><strong>Starting Balance/Previous Budget Surplus</strong></td>
          <td class="right">$ <?= form::input('starting_balance', number_format($this->form['starting_balance'], 2),  'class="amount" tabindex="'. $i++ .'"') ?></td>
          <td class="right"></td>
        </tr>
        <?php $expected_income += $this->budget->starting_balance; $actual_income = $this->budget->starting_balance; ?>
        <?php if ($this->budget->income_categories->count()): ?>
          <?php foreach ($this->budget->income_categories as $category): ?>
            <tr class="hoverable">
              <td class="title"><?= html::anchor('budgets/itemized/'. $this->budget->id .'/'. $category->id, $category->name) ?></td>
              <td class="right">$ <?= form::input('category['.$category->id.']', number_format($this->expected[$category->id] ? $this->expected[$category->id] : 0, 2), 'class="amount" tabindex="'. $i++ .'"') ?></td>
              <td class="right"><?= money::display($this->actual[$category->id]) ?></td>
              <td class="right"><?= money::display($this->actual[$category->id] > $this->expected[$category->id] ? 0 : $this->expected[$category->id] - $this->actual[$category->id]) ?></td>
            </tr>
            <?php $expected_income += $this->expected[$category->id]; $actual_income += $this->actual[$category->id]; ?>
          <?php endforeach ?>
        <?php else: ?>
          <tr><td colspan="4"><em>** You have no income categories setup. <?= html::anchor('budgets/categories', 'Add an income category') ?> to track non-dues income like fundraising or donations.</em></td></tr>
        <?php endif ?>
        <tr class="hoverable">
          <td class="right"><strong>Additional Income Totals:</strong></td>
          <td class="right" id="expected-income"><strong><?= money::display($expected_income) ?></strong></td>
          <td class="right"><strong><?= money::display($actual_income) ?></strong></td>
          <td class="right"><strong><?= money::display($actual_income > $expected_income ? 0 : $expected_income - $actual_income)  ?></strong></td>
        </tr>
      </tbody>
    </table>

    <!-- Expenses -->
    <table class="block" id="expenses">
      <thead>
        <tr>
          <th>Expenses</th>
          <th class="amount right">Expected</th>
          <th class="amount right">Spent</th>
          <th class="amount right">Remaining</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($this->budget->expense_categories->count()): ?>
          <?php foreach ($this->budget->expense_categories as $category): ?>
            <tr class="hoverable">
              <td class="title"><?= html::anchor('budgets/itemized/'. $this->budget->id .'/'. $category->id, $category->name) ?></td>
              <td class="right">$ <?= form::input('category['.$category->id.']', number_format($this->expected[$category->id] ? $this->expected[$category->id] : 0, 2), 'class="amount" tabindex="'. $i++ .'"') ?></td>
              <td class="right"><?= money::display($this->actual[$category->id]) ?></td>
              <td class="right <?= $this->actual[$category->id] > $this->expected[$category->id] ? 'red' : '' ?>"><?= money::display($this->expected[$category->id] - $this->actual[$category->id]) ?></td>
            </tr>
            <?php $expected_expenses += $this->expected[$category->id]; $actual_expenses += $this->actual[$category->id]; ?>
          <?php endforeach ?>
        <?php else: ?>
          <tr><td colspan="4"><em>** You have no expense categories setup. <?= html::anchor('budgets/categories', 'Add an expense category') ?> to record and categorize chapter expenses into committee budgets.</em></td></tr>
        <?php endif ?>
        <?php if ($this->site->collections_enabled()): ?>
          <tr class="hoverable">
            <td>ChapterBoard Collection Fees (Automatically calculated)</td>
            <td class="right">$ <?= form::input('expected_fees', number_format($this->budget->expected_fees), 'class="amount" tabindex="'. $i++ .'"') ?></td>
            <td class="right"><?= money::display($this->budget->collection_fees()) ?></td>
            <td class="right <?= $this->budget->collection_fees() > $this->budget->expected_fees ? 'red' : '' ?>"><?= money::display($this->budget->expected_fees - $this->budget->collection_fees()) ?></td>
          </tr>
          <?php $expected_expenses += $this->budget->expected_fees; $actual_expenses += $this->budget->collection_fees(); ?>   
        <?php endif ?>
        <tr class="hoverable">
          <td class="right"><strong>Expense Totals:</strong></td>
          <td class="right" id="expected-expenses"><strong><?= money::display($expected_expenses) ?></strong></td>
          <td class="right"><strong><?= money::display($actual_expenses) ?></strong></td>
          <td class="right <?= $expected_expenses < $actual_expenses ? 'red' : '' ?>"><strong><?= money::display($expected_expenses - $actual_expenses)  ?></strong></td>
        </tr>
      </tbody>
    </table>
    
    <table class="block">
      <tfoot class="budget-footer">
        <tr>
          <td class="right">Net Expected Profit/Loss:</td>
          <td id="expected-profit-loss" class="amount right <?= (($expected + $expected_income) < $expected_expenses) ? 'red' : '' ?>"><?= money::display($expected + $expected_income - $expected_expenses) ?></td>
          <td class="amount"></td>
          <td class="amount"></td>
        </tr>
        <tr>
          <td class="right">Net Actual Profit/Loss:</td>
          <td class="right <?= (($expected + $actual_income) < $actual_expenses) ? 'red' : '' ?>"><?= money::display($collected + $actual_income - $actual_expenses) ?></td>
          <td></td>
          <td></td>
        </tr>
      </tfoot>
    </table>
  </div>
  
  <div class="right no-print">
    <?= form::submit('save', 'Save Changes') ?> or <?= html::anchor('budgets', 'cancel') ?>
  </div>
  
  <?= form::close() ?>
</div>