The <?= $vars['chapter_name'] ?> chapter at <?= $vars['school'] ?> has invited you to join them on ChapterBoard.

Use the link below to register your new user account:

<?= url::site('register/'. $vars['token']) ?>

** This link is unique to your email address and can only be used to create your account.  Please do not forward this to other chapter members as it will not work. Other chapter members must receive a personal invitation from your chapter administrator in order to register.

For chapter-specific questions you should contact your chapter's ChapterBoard administrator:

<?= $vars['name'] ? $vars['name'] ."\r\n" : ''; ?>
<?= $vars['phone'] ? $vars['phone'] ."\r\n" : ''; ?>
<?= $vars['email'] ? $vars['email'] ."\r\n" : ''; ?>

If you have any questions regarding ChapterBoard you can contact us at team@chapterboard.com.