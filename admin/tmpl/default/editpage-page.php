<?php
/**
 * ページ作成テンプレート
 *
 * ページ作成のテンプレートです。
 * 一般的な記述方法はこのファイルをご覧ください。
 *
 * @author $Author:$
 * @version $Id:$
 */
?>
<?php theAdminHeader('editpage-'); ?>
  <div id="main">
    <div id="contents">
      <h2><a href="<?php theURI(); ?>?mode=editpage">一覧へ</a></h2>
      <p><a href="<?php theURI(); ?>?mode=editpage">一覧へ</a></p>

      <h2>ページ作成・修正</h2>
      <div id="page">
        <h3>アップロード</h3>
        <p><a href="<?php theURI(); ?>?mode=upload&amp;type=page&amp;keepThis=true&amp;TB_iframe=true"
          title="ファイルアップロード"
          class="thickbox">ファイルアップロード画面</a></p>

        <h3>ページ</h3>
        <form action="<?php theURI(); ?>" method="post">
          <input type="hidden" name="mode" value="execute" />
          <input type="hidden" name="type" value="page" />
<?php thePageHiddenID(); ?>
          パス名: <input type="text" name="path" value="<?php thePagePath(); ?>" size="32" /><br />
          タイトル: <input type="text" name="title" value="<?php thePageTitle(); ?>" size="64" /><br />
          <textarea name="description" class="tinymce"><?php thePageDescription(); ?></textarea><br />
<?php thePageEditModeButton(); ?>
          <input type="submit" value="submit" />
          <input type="reset" value="reset" />
        </form>
      </div>
    </div>
<?php theAdminSideLeft(); ?>
  </div>
<?php theAdminFooter(); ?>
