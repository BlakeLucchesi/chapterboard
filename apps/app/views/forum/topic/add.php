<div id="topic-form">

  <div id="breadcrumbs">
    <div><?= html::anchor('forum', 'Forum Boards') ?> &raquo; Post New Topic</div>
  </div>

  <div class="heading clearfix">
    <h2><?= $this->title ?></h2>
  </div>
  
  <?= message::get(); ?>

  <?= form::open_multipart('forum/topic/add', array('id' => 'topic-form-id')); ?>
  <div class="clearfix">
    <div class="split-left">
      <div class="clearfix">
        <?= form::label('title', 'Subject:'); ?>
        <?= form::input('title', $this->form['title']); ?>
        <span class="error"><?= $this->errors['title'] ?></span>
      </div>
      <div class="clearfix">
        <?= form::label('form_id', 'Forum:')?>
        <?= form::dropdown('forum_id', array('none' => '-- Select Forum --') + $this->forums, isset($this->form['forum_id']) ? $this->form['forum_id'] : $_GET['forum_id']); ?>
        <span class="error"><?= $this->errors['forum_id'] ?></span>
      </div>    
      <div class="clearfix">
        <div class="clearfix error"><?= $this->errors['body'] ?></div>
        <?= form::textarea('body', $this->form['body']); ?>
        <br />
        <?= form::submit('post', 'Post Topic'); ?>
        <!-- <div class="right form-help"><?= help::link('general/content', 'Formatting guidelines.') ?></div> -->
      </div>
    </div>

    <div class="split-right">
      <div id="attachment" class="block">
        <?= form::hidden('key', $this->form['key']) ?>
        <div class="clearfix">
          <h3>Attachments</h3>
        </div>
        <div id="upload-wrapper" class="clearfix">
          <div id="upload-status"><?= html::image('images/loadingAnimation.gif') ?></div>
          <div id="upload-form">
            <?= form::upload(array('name' => 'attach')); ?>
            <?= form::button('upload', 'Attach', 'class="unbound"') ?>
            <!-- <span class="form-help"><?= html::thickbox_anchor('help/general/upload', 'File upload help.') ?></span> -->
          </div>
        </div>
        <div id="attachments" class="attachments" class="uploaded-files pp_attachment">
          <?php if ($this->upload_error): ?>
            <div class="upload-error"><?= $this->upload_error ?></div>
          <?php endif ?>
          <?php foreach ($this->uploads as $upload): ?>
            <div class="file file-type-<?= $upload['extension'] ?>">
              <?= $upload['name'] ?>
            </div>
          <?php endforeach ?>
        </div>
      </div>
      
      <div id="poll-form" class="block clearfix">
        <h3 class="title"><label><?= form::checkbox('add_poll', 1, 0) ?> Add a Poll</label></h3>
        <div id="poll-choices" class="clearfix">
          <div class="question clearfix">
            <?= form::label('question', 'Question: *') ?> <?= form::input('question') ?>
            <em>* please enter a question and at least two options.</em>
          </div>
          <?= form::label('poll[1]', 'Option 1:'); ?> <?= form::input('poll[1]'); ?><br />
          <?= form::label('poll[2]', 'Option 2:'); ?> <?= form::input('poll[2]'); ?><br />
          <?= form::label('poll[3]', 'Option 3:'); ?> <?= form::input('poll[3]'); ?><br />
          <?= form::label('poll[4]', 'Option 4:'); ?> <?= form::input('poll[4]'); ?><br />
          <?= form::label('poll[5]', 'Option 5:'); ?> <?= form::input('poll[5]'); ?>
          <a href="#" id="add-poll-option">+ Add another option</a>
          <div class="checkbox clearfix"><label><?= form::checkbox('private', 1, $this->form['private']) ?> Hide poll results from members</label></div>
        </div>
      </div>
      
    </div>
  </div>
  
  <?= form::close(); ?>
</div>
