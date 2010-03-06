<?php theAdminHeader(); ?>
  <div id="main">
    <div id="contents">
      <?php if (do_login()) : ?>
      <p><?php theName(); ?>さん<a href="<?php theURILogout(); ?>">ログアウト</a></p>
      <?php endif; ?>
      <h2>管理者メインコンテンツ</h2>
    </div>
<?php theAdminSideLeft(); ?>
  </div>
<?php theAdminFooter(); ?>
