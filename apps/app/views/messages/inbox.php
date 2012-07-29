<div id="messages" class="heading clearfix icon">
  <h2>Inbox</h2>
  <ul>
    <li><?= html::anchor('messages/send', 'Send Message') ?></li>
  </ul>
</div>

<table class="inbox">
  <?php if ($this->messages->count()): ?>
    <?php foreach ($this->messages as $message): ?>
      <tr class="hoverable <?= $message->is_unread() ? 'unread' : 'read' ?> admin-hover">
        <td class="status">&nbsp;</td>
        <td class="avatar"><?= theme::image('mini', $message->sendee_picture, array('class' => 'userphoto')) ?></td>
        <td class="from">
          <div class="username"><?= $message->sendees ?></div>
          <div class="created"><?= date::display($message->updated, 'daytime') ?></div>
        </td>
        <td class="message">
          <div class="admin-links">
            <?= html::anchor('messages/delete/'. $message->id, 'Delete', array('class' => 'delete alert', 'title' => 'Are you sure you wish to delete this message?')) ?>
          </div>
          <div class="subject"><?= html::anchor('messages/show/'. $message->id, $message->subject, array('class' => 'hoverclick')) ?></div>
          <div class="body"><?= text::limit_words($message->body, 25) ?></div>
        </td>
      </tr>
    <?php endforeach ?>
  <?php else: ?>
    <tr><td><p>You do not have any messages in your inbox.</p></td></tr>
  <?php endif; ?>
</table>

<?= $this->pagination ?>