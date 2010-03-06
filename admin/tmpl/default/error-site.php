<?php
/**
 * サイトエラーテンプレート
 *
 * サイト関連エラーのテンプレートです。
 * 一般的な記述方法はこのファイルをご覧ください。
 *
 * @author $Author:$
 * @version $Id:$
 */
?>
<?php theAdminHeader(); ?>
  <div id="main">
    <div id="contents">
      <h2>サイトエラー</h2>
      <p><?php theMessage(); ?></p>
    </div>
<?php theAdminSideLeft(); ?>
  </div>
<?php theAdminFooter(); ?>
