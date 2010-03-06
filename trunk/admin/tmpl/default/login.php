<?php theAdminHeader(); ?>
  <div id="main">
    <div id="contents">
      <h2><a href="#">ログイン</a></h2>
      <p><?php theMessage(); ?></p>
      <form action="<?php theURI(); ?>" method="post">
      <fieldset>
        <legend>入力項目</legend>
        ユーザ名：<input type="text" name="username" /><br />
        パスワード：<input type="password" name="password" /><br />
        <input type="hidden" name="mode" value="login" />
        <input type="hidden" name="redirect" value="<?php theRedirect(); ?>" />
        <input type="submit" value="ログイン" />
        <input type="reset" value="クリア" />
      </fieldset>
      </form>
    </div>
    <div id="left">
    </div>
  </div>
<?php theAdminFooter(); ?>
