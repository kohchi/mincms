<?php
/**
 * テンプレート一覧テンプレート
 *
 * テンプレート一覧のテンプレートです。
 * 一般的な記述方法はこのファイルをご覧ください。
 *
 * @author $Author:$
 * @version $Id:$
 */
?>
<?php theAdminHeader(); ?>
  <div id="main">
    <div id="contents">
      <h2><a href="<?php theURI(); print '?mode=tmpladmin'; ?>">テンプレート一覧</a></h2>
      <div id="tmpls">
        <form action="<?php theURI(); ?>" method="post">
<?php foreach (get_tmpl_result() as $r) : ?>
          <div class="template">
            <h3><?php print $r['title']; ?></h3>
            <img src="<?php print $r['image']; ?>"
              alt="スクリーンショット" title="スクリーンショット" />
            <p><?php print $r['desc']; ?></p>
            <input type="radio" name="value" value="<?php print $r['dir']; ?>"
              <?php print($r['current'] ? 'checked="checked"' : ''); ?> />
              このテンプレートにする
          </div>
<?php endforeach; ?>
          <input type="hidden" name="mode" value="execute" />
          <input type="hidden" name="type" value="tmpl" />
          <input type="submit" value="submit" />
          <input type="reset" value="reset" />
        </form>
      </div>
    </div>
<?php theAdminSideLeft(); ?>
  </div>
<?php theAdminFooter(); ?>
