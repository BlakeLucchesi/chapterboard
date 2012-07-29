<html>
  <head>
    <title><?= $this->title; ?> | ChapterBoard Mobile</title>
    <link rel="icon" type="image/vnd.microsoft.icon" href="/favicon.ico" />
    <link rel="apple-touch-icon" href="/images/iphone-app-icon.png" />
    <meta content="initial-scale=1.0; maximum-scale=1.0; width=device-width; user-scalable=no;" name="viewport"/>

    <?= css::get() ?>
    <?= ga::render() ?>
  </head>
  <body name="top">
    <div id="nav">
      <!-- <?= html::anchor('/', html::image('images/logo-mobile.gif')) ?> -->
      <?= View::factory('menu/primary') ?>
    </div>
    
    <div id="wrapper">
      <div id="wrapper-inner">
        <div id="body"><?= $content; ?></div>
      </div>
    </div>

    <div id="footer">
      <div id="footer-inner">
        <?= View::factory('templates/footer') ?>
      </div>
    </div>
  </body>
</html>