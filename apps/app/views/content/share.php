<div class="clearfix heading">
  <h2><?= $this->title ?></h2>
</div>

<?= message::get(); ?>

<div class="clearfix">
  <div class="split-left">
    <div class="help">
      <p>It's fast, simple and easy. Just fill out the information below to share ChapterBoard with other fraternity or sorority chapters.</p>
      <p>Thanks!</p>
    </div>
    <fieldset>
      <?= form::open() ?>
      
      <div class="clearfix">
        <?= form::label('name', 'Friend\'s Name:')?>
        <?= form::input('name', $this->form['name'], 'class="medium"') ?>
        <span class="error"><?= $this->errors['name'] ?></span>
      </div>
      <div class="clearfix">
        <?= form::label('email', 'Friend\'s Email:')?>
        <?= form::input('email', $this->form['email'], 'class="medium"') ?>
        <span class="error"><?= $this->errors['email'] ?></span>
      </div>
      <div class="clearfix">
        <label>Message:</label>
        <span class="error"><?= $this->errors['message'] ?></span>
        <?= form::textarea('message', $this->form['message']) ?>
      </div>    
      <?= form::submit('send', 'Share') ?>
      <?= form::close() ?>
      
    </fieldset>
  </div>
  
  <div class="split-right">
    <iframe src="http://www.facebook.com/plugins/likebox.php?api_key=113869198637480&amp;id=13523121223&amp;width=350&amp;connections=12&amp;stream=false&amp;header=false" scrolling="no" frameborder="0" allowTransparency="true" style="border:none; overflow:hidden; width:350px; height:280px;"></iframe>
    
    <script src="http://widgets.twimg.com/j/2/widget.js"></script>
    <script>
    new TWTR.Widget({
      version: 2,
      type: 'profile',
      rpp: 3,
      interval: 6000,
      width: 350,
      height: 120,
      theme: {
        shell: {
          background: '#ffffff',
          color: '#000000'
        },
        tweets: {
          background: '#ffffff',
          color: '#333333',
          links: '#1b6bdb'
        }
      },
      features: {
        scrollbar: false,
        loop: false,
        live: false,
        hashtags: true,
        timestamp: true,
        avatars: false,
        behavior: 'all'
      }
    }).render().setUser('chapterboard').start();
    </script>
  </div>
</div>