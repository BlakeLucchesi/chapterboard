<html>
  <head>
    <title><?= $this->title; ?> | ChapterBoard Mobile</title>
    <link rel="icon" type="image/vnd.microsoft.icon" href="/favicon.ico" />
    <link rel="apple-touch-icon" href="/images/iphone-app-icon.png" />
    <meta content="width=device-width, user-scalable=no" name="viewport"/>  
    <meta content="initial-scale=1.0; maximum-scale=1.0; user-scalable=0;" name="viewport"/>
    <?= css::get() ?>
    <?= ga::render() ?>
  </head>
  <body>
    <div id="login-logo">
      <?= html::anchor('/', html::image('images/logo-mobile-login.gif')) ?>
    </div>
    
    <div id="wrapper">
      <div id="wrapper-inner">
        <?= $content; ?>
      </div>
    </div>

    <div id="footer">
      <div id="footer-inner">
        <?= View::factory('templates/footer') ?>
      </div>
    </div>
  </body>
</html>