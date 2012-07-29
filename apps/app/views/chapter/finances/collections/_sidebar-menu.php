<?php if ($this->site->parson_bishop): ?>
  <div class="block clearfix no-print">
    <h3 class="title">Account Information</h3>
    <table>
      <tr>
        <td><label>Account Number:</label></td>
        <td>#<?= $this->site->parson_bishop ?></td>
      </tr>
    </table>
  </div>
<?php endif ?>
<div class="block no-print">
  <h3 class="title">Debt Collection Resources</h3>
  <table>
    <tr>
      <td><img src="/minis/page_white_text.png" /> <?= html::anchor('finances/collections/intro', 'Introduction to Collection Services') ?></td>
    </tr>
    <tr>
      <td><img src="/minis/page_white_text.png" /> <?= html::anchor('finances/collections/concerns', 'Common Debt Collection Concerns') ?></td>
    </tr>
    <tr>
      <td><img src="/minis/page_white_text.png" /> <?= html::anchor('finances/collections/suggestions', 'Suggestions to Improve Collections') ?></td>
    </tr>
    <tr>
      <td><img src="/minis/page_white_text.png" /> <?= html::anchor('finances/collections/testimonials', 'Parson-Bishop Testimonials') ?></td>
    </tr>
  </table>
</div>

<div class="block" style="padding: 0 10px;">
  <h4>Parson-Bishop Services Inc.</h4>
  <p>7870 Camargo Road<br />Cincinnati, Ohio 45243</p>
  <p>Phone: 800-543-0468<br />Fax: 513-527-8919<br />E-mail: <?= html::mailto('clientservices@parsonbishop.com') ?></p>
</div>

<?php if (Router::$method != 'signup' && ! $this->site->parson_bishop): ?>
  <div class="block clearfix">
    <ul>
      <li class="button"><?= html::anchor('finances/collections/signup', 'Signup Now'); ?></li>
    </ul>
  </div>
<?php endif ?>