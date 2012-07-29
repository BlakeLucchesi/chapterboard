<div id="view-topic">

  <div id="breadcrumbs">
    <div><?= html::anchor('forum', 'Forums') ?> &raquo; <?= html::anchor('forum/'. $this->topic->forum_id, $this->topic->forum->title) ?> &raquo; <?= html::anchor('forum/topic/'. $this->topic->id, $this->topic->title) ?></div>
  </div>

  <?= message::get() ?>
  
  <div id="topic-form" class="topic-edit">
    <?= form::open_multipart(Router::$current_uri, array('id' => 'topic-form-id')); ?>

    <h3>Edit Topic</h3>
    <?= form::hidden('id', $this->topic->id) ?>

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
        <!-- <?= form::label('expires', 'Auto Archive:') ?> <?= form::checkbox('expire', 'archive', TRUE); ?> <small>(Posts without comments for 60 days will be archived automatically)</small><br /> -->

        <div class="clearfix">
          <!-- <?= form::label('body', 'Topic:'); ?> -->
          <?= form::textarea('body', $this->form['body']); ?>
          <br />
          <?= form::submit('post', 'Save Changes'); ?> or <?= html::anchor('forum/topic/'. $this->topic->id, 'Cancel') ?>
          <!-- <div class="right"><em><?= html::thickbox_anchor('help/general/content', 'Formatting guidelines.') ?></em></div> -->
          <div class="clearfix error"><?= $this->errors['body'] ?></div>
        </div>
      </div>

      <div class="split-right">
        <div id="attachment" class="block clearfix">
          <?= form::hidden('key', $this->form['key']) ?>
          <div class="clearfix">
            <h3>Attachments</h3>
          </div>
          <div id="upload-wrapper" class="clearfix">
            <div id="upload-status"><?= html::image('images/loadingAnimation.gif') ?></div>
            <div id="upload-form"><?= form::upload(array('name' => 'attach')); ?> <?= form::button('upload', 'Attach', 'class="unbound"') ?> <!-- <em><?= html::thickbox_anchor('help/general/upload', 'File upload help.') ?></em> --></div>
          </div>
          <div id="attachments" class="attachments" class="uploaded-files pp_attachment">
            <?php if ($this->upload_error): ?>
              <div class="upload-error"><?= $this->upload_error ?></div>
            <?php endif ?>
            <?php foreach ($this->uploads as $upload): ?>
              <div class="file file-type-<?= $upload['extension'] ?>">
                <?= $upload['name'] ?> - <?= html::anchor('upload/remove', 'Remove', array('class' => 'remove-file', 'filehash' => upload::filehash($upload['filepath']))) ?>
              </div>
            <?php endforeach ?>
          </div>
        </div>
        
          <div id="poll-form" class="block">
            <h3 class="title"><label><?= form::checkbox('add_poll', 1, $this->topic->poll->loaded) ?> Add a Poll</label></h3>
            <div id="poll-choices" class="clearfix" style="<?= $this->topic->poll->loaded ? 'display:block' : '' ?>">
              <div class="question clearfix">
                <?= form::label('question', 'Question: *') ?> <?= form::input('question', $this->topic->poll->question) ?>
                <em>* please enter a question and at least two options.</em>
              </div>
              <?php $i = 1; ?>
              <?php foreach ($this->topic->poll->poll_choices as $choice): ?>
                <?= form::label('poll['. $choice->id .']', 'Option '. $i .':'); ?> <?= form::input('poll['. $choice->id .']', $form['poll'][$choice->id] ? $form['poll'][$choice->id] : $choice->text); ?>
                <?= $i == $this->topic->poll->poll_choices->count() ? '' : '<br />' ?>
                <?php $i++; ?>
              <?php endforeach ?>
              <?php for ($i = $this->topic->poll->poll_choices->count(); $i < 5; $i++): ?>
                <?php $item = $i + 1; ?>
                <?= form::label('poll['. $item .']', 'Option '. $item .':'); ?> <?= form::input('poll['. $item .']', $form['poll'][$item]); ?><br />
              <?php endfor; ?>
              <a href="#" id="add-poll-option">+ Add another option</a>
              <div class="checkbox clearfix"><label><?= form::checkbox('private', 1, $this->form['poll']['private']) ?> Hide poll results from members</label></div>
            </div>
          </div>
        </div>
      </div>
    <?= form::close(); ?>
  </div>
  
  <!-- View Topic -->
  <div class="heading clearfix">
    <h2>Original Topic</h2>
  </div>

  <div class="view-topic">
    <div class="topic-header"><div class="topic-header-inner clearfix">
      <div class="author-photo">
        <?= html::anchor('profile/'. $this->topic->user->id, theme::image('tiny', $this->topic->user->picture(), array('class' => 'userphoto'))); ?>
      </div>
      <h3><?= $this->topic->title ?></h3>
      <div class="author-name">
        <span class="post-date">On <?php print date::display($this->topic->created, 'M d, Y g:i a')?></span> by <?= $this->topic->user->name(TRUE) ?>
      </div>

    </div></div>
    <div class="topic-body">
      <p><?= format::html($this->topic->body); ?></p>
    </div>
    <?php if ($this->topic->files->count()): ?>
      <div class="attachments">
        <h3>Attachments:</h3>
        <?php foreach ($this->topic->files as $file): ?>
          <div class="file file-type-<?= $file->extension ?>">
            <?php if (in_array($file->extension, array('jpg', 'jpeg', 'png', 'gif'))): ?>
              <?= html::thickbox_anchor('file/original/'. $file->filename, $file->name) ?>
            <?php else: ?>
              <?= html::anchor('file/original/'. $file->filename, $file->name) ?>
            <?php endif ?>
          </div>
        <?php endforeach ?>
      </div>
    <?php endif ?>
  </div>
  
</div>