<?php
/**
 * 設定一覧テンプレート
 *
 * 設定一覧のテンプレートです。
 * 一般的な記述方法はこのファイルをご覧ください。
 *
 * @author $Author:$
 * @version $Id:$
 */
?>
<?php theAdminHeader(); ?>
  <div id="main">
    <div id="contents">
      <h2><a href="<?php theURI(); print '?mode=confadmin'; ?>">設定情報</a></h2>
      <div id="confs">
        <form action="<?php theURI(); ?>" method="post">
          <dl>
<?php foreach (get_tmpl_result() as $r) : ?>
            <dt><?php print $r['name']; ?></dt>
            <dd><input type="text" name="<?php print $r['id']; ?>"
              value="<?php print $r['value']; ?>" size="64" /></dd>
<?php endforeach; ?>
          </dl>
          <input type="hidden" name="mode" value="execute" />
          <input type="hidden" name="type" value="conf" />
          <input type="submit" value="submit" />
          <input type="reset" value="reset" />
        </form>
      </div>
    </div>
<?php theAdminSideLeft(); ?>
  </div>
<?php theAdminFooter(); ?>
