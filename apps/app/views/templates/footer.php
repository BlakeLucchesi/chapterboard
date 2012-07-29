<div class="clearfix">
  <div class="split-left">
    <?= html::anchor(Kohana::config('app.public_url'), 'Home') ?> |
    <?= html::anchor(Kohana::config('app.mobile_url'), 'Mobile Friendly') ?> |
    <?= html::anchor(Kohana::config('app.public_url') .'/privacy-policy', 'Privacy Policy') ?> |
    <?= html::anchor(Kohana::config('app.public_url') .'/terms-of-service', 'Terms of Service') ?> |
    <?= html::anchor(Kohana::config('app.public_url'), sprintf('&copy; %d ChapterBoard LLC', date('Y'))); ?> 
  </div>
  <div class="split-right">
    <?= html::anchor('http://blog.chapterboard.com', 'ChapterBoard Blog', array('target' => '_blank')) ?> |
    <?= html::anchor('http://www.facebook.com/chapterboard', 'Facebook', array('target' => '_blank')) ?> | 
    <?= html::anchor('http://www.twitter.com/chapterboard', 'Twitter', array('target' => '_blank')) ?>
  </div>
</div>
