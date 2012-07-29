<div style="background-color:#FFFFFF; padding:10px 0 100px 0;">
  <table width="95%" border="0" cellspacing="0" cellpadding="0" align="center">
    <tr>
      <td valign="top" style="border:#333333 solid 0px; border-collapse:collapse; display:block;">
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top">
              <table width="95%" cellspacing="0" cellpadding="0" align="center" style="font-family: Arial, Helvetica, sans-serif; font-size: 14px; color:#444; margin: 0 auto; line-height: 22px; border: 2px solid #00435c">
                <tr>
                  <td style="background-color: #00435c; height:44px;"><img src="<?= url::base() ?>images/mail-logo-small.gif" width="182" height="32" style="padding: 6px 20px;" /></td>
                </tr>
                <tr>
                  <td style="padding: 20px 27px;">
                    <?= $body ?>
                  </td>
                </tr>
                <tr>
                  <td>
                    <hr width="90%" style="height: 1px; border: 0; color: #cccccc; background-color: #cccccc;" />
                    <center style="font-size:11px; padding-bottom: 20px; color: #222;">
                      ChapterBoard LLC, 1301 4th Ave #707, Seattle, WA 98101<br />
                      (949) 525-4432 &nbsp; | &nbsp; <a href="mailto:team@chapterboard.com" style="color:#5c7996; text-decoration:none;"><strong>team@chapterboard.com</strong></a> &nbsp; | &nbsp; <a href="http://www.chapterboard.com"  style="color:#5c7996; text-decoration:none;" ><strong>www.chapterboard.com</strong></a>
                    </center>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
    <tr> 
      <td align="center" style="font-family:Verdana, Arial, Helvetica, sans-serif; color:#333333; font-size:10px; text-align:center; line-height: 1.5em">
        <div align="center"><br />
          This email was sent to <?= $to ?>.<br />
          To ensure that you continue receiving our emails, <br />
          please add us to your address book or safe list.<br /><br />
          Login to <a href="http://app.chapterboard.com" style="color:#333333"><strong>manage</strong></a> your notification preferences.
        </div>
      </td>
    </tr>
  </table>
</div>