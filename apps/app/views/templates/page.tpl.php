<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title><?php print html::specialchars($this->title); ?> | ChapterBoard</title>
    <link rel="shortcut icon" href="/favicon.ico">
    <link rel="apple-touch-icon-precomposed" href="/images/iphone-icon.png" />
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="/images/ipad-icon.png" />
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="/images/iphone4-icon.png" />
    <?= css::get(); ?>
    <?= ga::render(); // Google Analytics. ?>
  </head>
  <body class="section-<?= Router::$controller ?>">
    <?php if ($this->user->has_role('root') || $this->user->has_role('national')): ?>
      <div id="master-bar">
        <div class="fixed-wrapper clearfix">
          <?php if ($this->user->has_role('root')): ?>
            <div class="right">
              User ID:
              <?= form::open('shapeshift') ?>
              <?= form::input('user_id', '', 'class="medium"') ?>
              <?= form::submit('go', 'Go') ?>
              <?= form::close() ?>
            </div>
          <?php endif ?>
          <div class="left clearfix">
            Currently Viewing:
            <?= form::open('teleport') ?>
            <?= form::dropdown('site_id', $this->sites, kohana::config('chapterboard.site_id')) ?>
            <?= form::submit('go', 'Go') ?>
            <?= form::close(); ?>
          </div>
        </div>
      </div>
    <?php endif ?>
    <div id="header" class="clearfix">
      <?= View::factory('menu/top-nav'); ?>
    </div>

    <div id="content" class="clearfix">

      <?= $primary; ?>
      <div id="left" class="fixed-wrapper"><div id="left-inner">
        
        <div id="secondary"><div id="secondary-inner" class="clearfix">
          <?= $secondary; ?>
        </div></div><!-- secondary, secondary-inner -->
        
        <div id="main"><div id="main-inner" class="clearfix">
          <?= message::get() ?>
          
          <?= $content ?>
        </div></div><!-- main, main-inner -->

      </div></div><!-- left, left-inner -->

    </div><!-- content -->
    <div id="footer" class="fixed-wrapper">
      <?= View::factory('templates/footer') ?>
    </div> <!-- footer -->
    <div id="footer-print" class="no-screen">
      <?= View::factory('templates/footer-print') ?>
    </div>
    <?= javascript::get() ?>
    <!-- begin olark code -->
    <script type='text/javascript'>/*{literal}<![CDATA[*/window.olark||(function(i){var e=window,h=document,a=e.location.protocol=="https:"?"https:":"http:",g=i.name,b="load";(function(){e[g]=function(){(c.s=c.s||[]).push(arguments)};var c=e[g]._={},f=i.methods.length; while(f--){(function(j){e[g][j]=function(){e[g]("call",j,arguments)}})(i.methods[f])} c.l=i.loader;c.i=arguments.callee;c.f=setTimeout(function(){if(c.f){(new Image).src=a+"//"+c.l.replace(".js",".png")+"&"+escape(e.location.href)}c.f=null},20000);c.p={0:+new Date};c.P=function(j){c.p[j]=new Date-c.p[0]};function d(){c.P(b);e[g](b)}e.addEventListener?e.addEventListener(b,d,false):e.attachEvent("on"+b,d); (function(){function l(j){j="head";return["<",j,"></",j,"><",z,' onl'+'oad="var d=',B,";d.getElementsByTagName('head')[0].",y,"(d.",A,"('script')).",u,"='",a,"//",c.l,"'",'"',"></",z,">"].join("")}var z="body",s=h[z];if(!s){return setTimeout(arguments.callee,100)}c.P(1);var y="appendChild",A="createElement",u="src",r=h[A]("div"),G=r[y](h[A](g)),D=h[A]("iframe"),B="document",C="domain",q;r.style.display="none";s.insertBefore(r,s.firstChild).id=g;D.frameBorder="0";D.id=g+"-loader";if(/MSIE[ ]+6/.test(navigator.userAgent)){D.src="javascript:false"} D.allowTransparency="true";G[y](D);try{D.contentWindow[B].open()}catch(F){i[C]=h[C];q="javascript:var d="+B+".open();d.domain='"+h.domain+"';";D[u]=q+"void(0);"}try{var H=D.contentWindow[B];H.write(l());H.close()}catch(E){D[u]=q+'d.write("'+l().replace(/"/g,String.fromCharCode(92)+'"')+'");d.close();'}c.P(2)})()})()})({loader:(function(a){return "static.olark.com/jsclient/loader0.js?ts="+(a?a[1]:(+new Date))})(document.cookie.match(/olarkld=([0-9]+)/)),name:"olark",methods:["configure","extend","declare","identify"]});
      // When an operator is available we show the live chat link.
      olark('api.chat.updateVisitorNickname', {snippet: '<?= sprintf("%s #%s %s", $this->user->first_name, $this->user->id, $this->site->name()) ?>'})
      olark('api.box.hide');
      
      olark('api.chat.onOperatorsAvailable',function() {
        $('#live-chat-link').show().click(function() {
          olark('api.box.expand');
          $.cookie('olark_hidden', null, { path: '/' });
        });
      });
      olark('api.visitor.getDetails', function(details){
        if (details.isConversing && $.cookie('olark_hidden') !== 'true') {
          olark('api.box.expand');
          olark('api.box.onHide', function () {
            $.cookie('olark_hidden', true, { path: '/' });
            olark('api.chat.sendNotificationToOperator', { body: "visitor closed the chatbox." });
          });
          olark('api.box.onShrink', function() {
            $.cookie('olark_hidden', true, { path: '/' });
            olark('api.chat.sendNotificationToOperator', { body: "visitor shrunk the chatbox." });
          });
        }
      });
      olark.identify('4569-618-10-2750');/*]]>{/literal}*/
    </script>
    <!-- end olark code -->
    
    <script>
      if (navigator.userAgent.match(/(iPhone|iPod|iPad)/i)) {
        document.title = 'ChapterBoard';
      }
    </script>
  </body>
</html>